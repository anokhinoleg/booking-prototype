<?php

declare(strict_types=1);

namespace App\Validation\Availability\Rules;

use App\Dto\AvailabilityRequest;
use App\Validation\Availability\Violation;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

final readonly class LeadTimeRule implements AvailabilityValidationRuleInterface
{
    private DateTimeZone $timeZone;

    public function __construct(private string $timezone = 'Europe/Berlin')
    {
        $this->timeZone = new DateTimeZone($timezone);
    }

    public function validate(AvailabilityRequest $availability): array
    {
        try {
            $pickupAt = new DateTimeImmutable($availability->pickupAt);
        } catch (Exception) {
            return [];
        }

        $now = new DateTimeImmutable('now', $this->timeZone);
        $tomorrowMorning = new DateTimeImmutable($now->format('Y-m-d') . ' 08:00:00', $this->timeZone);
        $earliestPickup = $tomorrowMorning->modify('+1 day');

        if ($pickupAt->setTimezone($this->timeZone) < $earliestPickup) {
            return [new Violation('pickupAt', 'lead_time_violation', 'Earliest pickup is tomorrow at 08:00 local time.')];
        }

        return [];
    }
}
