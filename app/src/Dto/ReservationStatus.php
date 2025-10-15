<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class ReservationStatus
{
    public function __construct(
        public ?int $id,
        public string $status,
        public array $violations = [],
        public ?string $message = null,
    ) {
    }
}
