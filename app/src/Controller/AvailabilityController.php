<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/v1')]
#[OA\Tag(name: 'availability')]
final class AvailabilityController extends AbstractController
{
    #[Route('/availability', name: 'api_availability_check', methods: ['POST'])]
    #[OA\Post(
        path: '/v1/availability',
        summary: '',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['vehicle_id','pickup_at','return_at'],
                properties: [
                    new OA\Property(property: 'vehicle_id', type: 'string', example: 'v1'),
                    new OA\Property(property: 'pickup_at', type: 'string', format: 'date-time', example: '2025-10-20T08:00:00'),
                    new OA\Property(property: 'return_at', type: 'string', format: 'date-time', example: '2025-10-22T08:00:00'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'available', type: 'boolean'),
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'), nullable: true),
                        new OA\Property(property: 'normalized', properties: [
                            new OA\Property(property: 'pickup_utc', type: 'string', example: '2025-10-20 06:00:00'),
                            new OA\Property(property: 'return_utc', type: 'string', example: '2025-10-22 06:00:00'),
                        ], type: 'object', nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function check(): JsonResponse
    {
        return new JsonResponse([
            'available'  => true,
            'normalized' => [
                'pickup_utc' => new \DateTimeImmutable()->format('Y-m-d H:i:s'),
                'return_utc' => new \DateTimeImmutable('+1')->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function calendar(): JsonResponse
    {
        //TODO: get busy dates logic
        return $this->json([]);
    }
}
