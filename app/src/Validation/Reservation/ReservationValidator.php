<?php

namespace App\Validation\Reservation;

use App\Dto\Reservation;
use App\Validation\Reservation\Rules\ReservationValidationRuleInterface;

final readonly class ReservationValidator
{
    /** @param ReservationValidationRuleInterface[] $validationRules */
    public function __construct(
        private array $validationRules,
    ) {
    }

    public function violate(Reservation $reservation): array
    {
        $violations = [];

        foreach ($this->validationRules as $rule) {
            $violations = array_merge($violations, $rule->validate($reservation));

            if (count($violations) !== 0) return $violations;
        }

        return $violations;
    }
}
