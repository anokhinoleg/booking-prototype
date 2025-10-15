<?php

declare(strict_types=1);

namespace App\Validation\Reservation;

final readonly class Violation
{
    public function __construct(
        public string $field,
        public string $code,
        public string $message,
    ) {
    }
}
