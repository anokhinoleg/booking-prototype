<?php

declare(strict_types=1);

namespace App\Dao;

use App\Enum\ReservationStatuses;

interface FetchReservationStatusGatewayInterface
{
    public function fetch(int $reservationId): ReservationStatuses;
}
