<?php

declare(strict_types=1);

namespace App\Validation\Availability\Rules;

use App\Dto\AvailabilityRequest;
use App\Validation\Availability\Violation;
use DateTimeImmutable;
use Exception;

final readonly class RentalLengthRule implements AvailabilityValidationRuleInterface
{
    public function __construct(private int $minDays = 1, private int $maxDays = 60)
    {
    }

    public function validate(AvailabilityRequest $availability): array
    {
        try {
            $pickupAt = new DateTimeImmutable($availability->pickupAt);
            $returnAt = new DateTimeImmutable($availability->returnAt);
        } catch (Exception) {
            return [];
        }

        $diffSeconds = $returnAt->getTimestamp() - $pickupAt->getTimestamp();
        $days = (int) ceil($diffSeconds / 86400);

        if ($days < $this->minDays || $days > $this->maxDays) {
            return [new Violation('returnAt', 'length_out_of_bounds', sprintf('Rental length must be between %d and %d days.', $this->minDays, $this->maxDays))];
        }

        return [];
    }
}
