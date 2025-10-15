<?php

declare(strict_types=1);

namespace App\Validation\Availability\Rules;

use App\Dto\AvailabilityRequest;
use App\Validation\Availability\Violation;
use DateTimeImmutable;
use Exception;

final readonly class DateTimeFormatRule implements AvailabilityValidationRuleInterface
{
    public function validate(AvailabilityRequest $availability): array
    {
        $violations = [];

        if (!$this->isValidDateTime($availability->pickupAt)) {
            $violations[] = new Violation('pickupAt', 'invalid_datetime', 'pickupAt must be a valid ISO-8601 datetime string.');
        }

        if (!$this->isValidDateTime($availability->returnAt)) {
            $violations[] = new Violation('returnAt', 'invalid_datetime', 'returnAt must be a valid ISO-8601 datetime string.');
        }

        return $violations;
    }

    private function isValidDateTime(string $value): bool
    {
        try {
            new DateTimeImmutable($value);

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
