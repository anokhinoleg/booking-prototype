<?php

declare(strict_types=1);

namespace App\Validation\Reservation\Rules;

use App\Dto\Reservation;
use App\Validation\Reservation\Violation;

final readonly class PickupBeforeReturnRule implements ReservationValidationRuleInterface
{
    public function validate(Reservation $reservation): array
    {
        if ($reservation->startDate >= $reservation->endDate) {
            return [new Violation(
                'startDate',
                'pickup_after_return',
                'startDate must be before endDate',
            )];
        }

        return [];
    }
}
