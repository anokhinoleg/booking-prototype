<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class UpdatedReservationStatus
{
    public function __construct(
        public int $reservationId,
        public string $previousStatus,
        public string $newStatus,
    ) {
    }
}
