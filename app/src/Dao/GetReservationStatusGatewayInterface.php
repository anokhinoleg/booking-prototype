<?php

declare(strict_types=1);

namespace App\Dao;

use App\Enum\ReservationStatuses;

interface GetReservationStatusGatewayInterface
{
    public function get(int $reservationId): ReservationStatuses;
}
