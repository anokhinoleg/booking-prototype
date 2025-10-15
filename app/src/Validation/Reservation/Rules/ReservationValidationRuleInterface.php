<?php

namespace App\Validation\Reservation\Rules;

use App\Dto\Reservation;

interface ReservationValidationRuleInterface
{
    public function validate(Reservation $reservation): array;
}
