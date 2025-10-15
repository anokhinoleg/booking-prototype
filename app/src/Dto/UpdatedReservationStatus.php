<?php

declare(strict_types=1);

namespace App\Dto;

#[OA\Schema(
    required: [
        'reservationId',
        'previousStatus',
        'newStatus',
        'message'
    ],
)]
final readonly class UpdatedReservationStatus
{
    public function __construct(
        public int $reservationId,
        public string $previousStatus,
        public string $newStatus,
        public string $message = '',
    ) {
    }
}
