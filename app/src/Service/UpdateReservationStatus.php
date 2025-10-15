<?php

declare(strict_types=1);

namespace App\Service;

use App\Dao\FetchReservationStatusGatewayInterface;
use App\Dao\UpdateReservationStatusGatewayInterface;
use App\Dto\UpdatedReservationStatus;
use App\Enum\ReservationStatuses;
use App\Exception\ReservationNotFoundException;
use App\Exception\ReservationStatusChangeException;

final readonly class UpdateReservationStatus
{
    public function __construct(
        private FetchReservationStatusGatewayInterface $fetchReservationStatusGateway,
        private UpdateReservationStatusGatewayInterface $updateReservationStatusGateway,
    ) {
    }

    public function execute(int $reservationId, ReservationStatuses $status): UpdatedReservationStatus
    {
        $currentStatus = $this->fetchReservationStatusGateway->fetch($reservationId);

        if ($currentStatus === $status) {
            throw new ReservationStatusChangeException(
                sprintf('Reservation is already %s.', strtolower($status->value))
            );
        }

        if ($currentStatus !== ReservationStatuses::REQUESTED) {
            throw new ReservationStatusChangeException(
                sprintf(
                    'Reservation already %s. Unable to change to %s.',
                    strtolower($currentStatus->value),
                    strtolower($status->value)
                )
            );
        }

        try {
            return $this->updateReservationStatusGateway->updateStatus($reservationId, $currentStatus, $status);
        } catch (ReservationStatusChangeException|ReservationNotFoundException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new ReservationStatusChangeException($exception->getMessage(), previous: $exception);
        }
    }
}
