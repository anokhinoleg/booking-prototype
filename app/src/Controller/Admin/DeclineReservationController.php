<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Enum\ReservationStatuses;
use App\Service\UpdateReservationStatus;
use App\Exception\ReservationNotFoundException;
use App\Exception\ReservationStatusChangeException;
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
    #[OA\Patch(path: '/v1/reservations/{id}/decline')]
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
