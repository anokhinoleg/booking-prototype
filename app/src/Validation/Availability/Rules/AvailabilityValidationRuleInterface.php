<?php

declare(strict_types=1);

namespace App\Validation\Availability\Rules;

use App\Dto\AvailabilityRequest;
use App\Validation\Availability\Violation;

interface AvailabilityValidationRuleInterface
{
    /**
     * @return Violation[]
     */
    public function validate(AvailabilityRequest $availability): array;
}
