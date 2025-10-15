<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\AvailabilityRequest;
use App\Dto\AvailabilityStatus;
use App\Dto\NormalizedAvailabilityWindow;
use App\Service\CheckAvailability;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1')]
#[OA\Tag(name: 'availability')]
final class AvailabilityController extends AbstractController
{
    public function __construct(private readonly CheckAvailability $checkAvailability)
    {
    }

    #[Route('/availability', name: 'api_availability_check', methods: ['POST'])]
    #[OA\Post(
        path: '/v1/availability',
        summary: 'Check whether the selected vehicle is free for the requested pickup and return window.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: AvailabilityRequest::class)),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Vehicle availability state.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'available', type: 'boolean'),
                        new OA\Property(
                            property: 'violations',
                            type: 'array',
                            nullable: true,
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'field', type: 'string'),
                                    new OA\Property(property: 'code', type: 'string'),
                                    new OA\Property(property: 'message', type: 'string'),
                                ],
                            ),
                        ),
                        new OA\Property(property: 'normalized', ref: new Model(type: NormalizedAvailabilityWindow::class), nullable: true),
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error.',
                content: new OA\JsonContent(ref: new Model(type: AvailabilityStatus::class)),
            ),
            new OA\Response(
                response: 503,
                description: 'Temporary failure to assess availability.',
                content: new OA\JsonContent(ref: new Model(type: AvailabilityStatus::class)),
            ),
        ]
    )]
    public function check(#[MapRequestPayload] AvailabilityRequest $availabilityRequest): JsonResponse
    {
        $availabilityStatus = $this->checkAvailability->execute($availabilityRequest);

        $statusCode = $this->resolveStatusCode($availabilityStatus);

        return $this->json(
            data: [
                'available' => $availabilityStatus->available,
                'violations' => $availabilityStatus->violations !== [] ? $availabilityStatus->violations : null,
                'normalized' => $this->normalizeWindow($availabilityStatus->normalized),
            ],
            status: $statusCode,
        );
    }

    public function calendar(): JsonResponse
    {
        //TODO: get busy dates logic
        return $this->json([]);
    }

    private function resolveStatusCode(AvailabilityStatus $availabilityStatus): int
    {
        if ($availabilityStatus->available) {
            return 200;
        }

        $codes = array_map(static fn(array $violation): string => $violation['code'], $availabilityStatus->violations);

        if (in_array('unavailable', $codes, true)) {
            return 503;
        }

        if ($codes === [] || (count($codes) === 1 && $codes[0] === 'overlap')) {
            return 200;
        }

        return 400;
    }

    private function normalizeWindow(?NormalizedAvailabilityWindow $window): ?array
    {
        if ($window === null) {
            return null;
        }

        return [
            'pickup_utc' => $window->pickupUtc,
            'return_utc' => $window->returnUtc,
        ];
    }
}
