<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\AvailabilityCheck;
use App\Dto\AvailabilityStatus;
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
        summary: 'Check whether the selected vehicle is free for the requested rental window.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: AvailabilityCheck::class)),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Vehicle availability state.',
                content: new OA\JsonContent(ref: new Model(type: AvailabilityStatus::class)),
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
    public function check(#[MapRequestPayload] AvailabilityCheck $availabilityRequest): JsonResponse
    {
        $availabilityStatus = $this->checkAvailability->execute($availabilityRequest);

        if (count($availabilityStatus->violations)) {
            return $this->json(data: $availabilityStatus, status: 400);
        }

        return $this->json($availabilityStatus);
    }
}
