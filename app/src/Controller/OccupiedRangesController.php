<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GetOccupiedRanges;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1')]
#[OA\Tag(name: 'occupied-ranges')]
final class OccupiedRangesController extends AbstractController
{
    public function __construct(private readonly GetOccupiedRanges $getOccupiedRanges)
    {
    }

    #[Route('/vehicles/{vehicleId}/occupied-ranges', name: 'api_occupied_ranges_get', methods: ['GET'])]
    #[OA\Get(
        path: '/v1/vehicles/{vehicleId}/occupied-ranges',
        summary: 'List periods when the vehicle is not available for booking.',
        parameters: [
            new OA\Parameter(
                name: 'vehicleId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Busy intervals for the vehicle.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'items',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'startDate', type: 'string', format: 'date'),
                                    new OA\Property(property: 'endDate', type: 'string', format: 'date'),
                                ],
                                type: 'object',
                            ),
                        ),
                    ],
                    type: 'object',
                ),
            ),
        ]
    )]
    public function get(int $vehicleId): JsonResponse
    {
        $items = $this->getOccupiedRanges->execute($vehicleId);

        return $this->json([
            'items' => $items,
        ]);
    }
}
