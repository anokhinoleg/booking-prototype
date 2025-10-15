<?php

declare(strict_types=1);

namespace App\Validation\Reservation\Rules;

use App\Dto\Reservation;
use App\Validation\Reservation\Violation;

final readonly class RentalLengthRule implements ReservationValidationRuleInterface
{
    public function __construct(
        private int $minDays = 1,
        private int $maxDays = 60
    ) {
    }

    public function validate(Reservation $reservation): array
    {
        $diffSec = new \DateTimeImmutable($reservation->endDate)->getTimestamp() - new \DateTimeImmutable($reservation->startDate)->getTimestamp();
        $days = (int) ceil($diffSec / 86400);

        if ($days < $this->minDays || $days > $this->maxDays) {
            return [new Violation('endDate', 'length_out_of_bounds', 'rental length must be 1–60 days')];
        }

        return [];
    }
}
