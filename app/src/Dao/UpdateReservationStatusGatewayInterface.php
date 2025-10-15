<?php

declare(strict_types=1);

namespace App\Dao;

use App\Dto\UpdatedReservationStatus;
use App\Enum\ReservationStatuses;

interface UpdateReservationStatusGatewayInterface
{
    /**
     * Update reservation status and return the resulting state.
     */
    public function update(
        int $reservationId,
        ReservationStatuses $currentStatus,
        ReservationStatuses $newStatus,
    ): int;
}
