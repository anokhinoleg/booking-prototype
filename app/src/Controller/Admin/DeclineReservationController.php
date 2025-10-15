<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\UpdatedReservationStatus;
use App\Enum\ReservationStatuses;
use App\Service\UpdateReservationStatus;
use App\Exception\ReservationNotFoundException;
use App\Exception\ReservationStatusChangeException;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/v1')]
#[OA\Tag(name: 'admin')]
final class DeclineReservationController extends AbstractController
{
    public function __construct(private readonly UpdateReservationStatus $updateReservationStatus)
    {
    }

    #[Route('/reservations/{id}/decline', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/v1/reservations/{id}/decline',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: ReservationStatuses::class,
                example: ReservationStatuses::DECLINED->value,
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reservation declined',
                content: new OA\JsonContent(
                    ref: new Model(type: UpdatedReservationStatus::class)
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Reservation not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Reservation not found.'),
                    ],
                ),
            ),
            new OA\Response(
                response: 409,
                description: 'Invalid reservation status transition',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unable to change reservation status from confirmed to declined.',
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: 500,
                description: 'Unexpected error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unexpected error occurred.',
                        ),
                    ],
                ),
            ),
        ],
    )]
    public function decline(int $id, #[MapRequestPayload] ReservationStatuses $status): JsonResponse
    {
        try {
            $updatedStatus = $this->updateReservationStatus->execute($id, ReservationStatuses::DECLINED);

            return $this->json($updatedStatus);
        } catch (ReservationNotFoundException $exception) {
            return $this->json(
                data: ['message' => $exception->getMessage()],
                status: 404,
            );
        } catch (ReservationStatusChangeException $exception) {
            return $this->json(
                data: ['message' => $exception->getMessage()],
                status: 409,
            );
        } catch (\Throwable $exception) {
            return $this->json(
                data: ['message' => $exception->getMessage()],
                status: 500,
            );
        }
    }
}
