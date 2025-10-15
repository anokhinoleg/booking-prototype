<?php

declare(strict_types=1);

namespace App\Service;

use App\Dao\GetReservationStatusGatewayInterface;
use App\Dao\UpdateReservationStatusGatewayInterface;
use App\Dto\UpdatedReservationStatus;
use App\Enum\ReservationStatuses;
use App\Exception\ReservationNotFoundException;
use App\Exception\ReservationStatusChangeException;

final readonly class UpdateReservationStatus
{
    public function __construct(
        private GetReservationStatusGatewayInterface    $fetchReservationStatusGateway,
        private UpdateReservationStatusGatewayInterface $updateReservationStatusGateway,
    ) {
    }

    public function execute(int $reservationId, ReservationStatuses $status): UpdatedReservationStatus
    {
        $currentStatus = $this->fetchReservationStatusGateway->get($reservationId);

        if ($currentStatus === $status) {
            return new UpdatedReservationStatus(
                reservationId: $reservationId,
                previousStatus: $currentStatus->value,
                newStatus: $status->value,
                message: sprintf('Reservation is already %s.', $status->value),
            );
        }

        if ($currentStatus !== ReservationStatuses::REQUESTED) {
            return new UpdatedReservationStatus(
                reservationId: $reservationId,
                previousStatus: $currentStatus->value,
                newStatus: $status->value,
                message: sprintf(
                    'Reservation is already %s. Unable to change to %s.',
                    $currentStatus->value,
                    $status->value,
                ),
            );
        }

        try {
            $this->updateReservationStatusGateway->update($reservationId, $currentStatus, $status);

            return new UpdatedReservationStatus(
                reservationId: $reservationId,
                previousStatus: $currentStatus->value,
                newStatus: $status->value,
                message: sprintf(
                    'Reservation has been changed successfully from %s to %s',
                    $currentStatus->value,
                    $status->value,
                ),
            );
        } catch (ReservationStatusChangeException|ReservationNotFoundException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new ReservationStatusChangeException($exception->getMessage(), previous: $exception);
        }
    }
}
