<?php

declare(strict_types=1);

namespace App\Validation\Availability\Rules;

use App\Dto\AvailabilityRequest;
use App\Validation\Availability\Violation;
use DateTimeImmutable;
use Exception;

final readonly class PickupBeforeReturnRule implements AvailabilityValidationRuleInterface
{
    public function validate(AvailabilityRequest $availability): array
    {
        try {
            $pickupAt = new DateTimeImmutable($availability->pickupAt);
            $returnAt = new DateTimeImmutable($availability->returnAt);
        } catch (Exception) {
            // DateTime format handled by DateTimeFormatRule
            return [];
        }

        if ($pickupAt >= $returnAt) {
            return [new Violation('pickupAt', 'pickup_after_return', 'pickupAt must be before returnAt.')];
        }

        return [];
    }
}
