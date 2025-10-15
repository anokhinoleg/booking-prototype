<?php

declare(strict_types=1);

namespace App\Dao;

use App\Dto\Reservation;

interface CreateReservationGatewayInterface
{
    /**
     * Interface for creating reservation entity
     */
    public function save(Reservation $reservation): int;
}
