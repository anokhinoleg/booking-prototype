<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Reservation;
use App\Service\CreateReservation;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/v1')]
#[OA\Tag(name: 'reservation')]
final class ReservationController extends AbstractController
{
    public function __construct(private readonly CreateReservation $createReservation)
    {
    }

    #[Route('/reservations', methods: ['POST'])]
    #[OA\Post(
        path: '/v1/reservations',
        requestBody: new OA\RequestBody(required: true),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'reservation_id', type: 'integer'),
                        new OA\Property(property: 'reservation_status', type: 'string'),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'reservation', ref: new Model(type: Reservation::class)),
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error or unavailable',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'reservation_status', type: 'string'),
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(
                            property: 'violations',
                            type: 'array',
                            items: new OA\Items(type: 'array', items: new OA\Items(type: 'string')),
                        ),
                    ],
                )
            ),
        ]
    )]

    public function create(#[MapRequestPayload] Reservation $reservation): JsonResponse
    {
        $reservationStatus = $this->createReservation->execute($reservation);

        if (count($reservationStatus->violations)) {
            return $this->json(
                data: [
                    'reservation_status' => $reservationStatus->status,
                    'message' => $reservationStatus->message,
                    'violations' => $reservationStatus->violations,
                ],
                status: 400,
            );
        }

        return $this->json(
            data: [
                'reservation_id' => $reservationStatus->id,
                'reservation_status' => $reservationStatus->status,
                'message' => $reservationStatus->message,
                'reservation' => $reservation,
            ],
            status: 201
        );
    }
}
