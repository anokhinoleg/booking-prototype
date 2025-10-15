<?php

declare(strict_types=1);

namespace App\Validation\Reservation\Rules;

use App\Dto\Reservation;
use App\Validation\Reservation\Violation;
use DateTimeImmutable;
use DateTimeZone;

final readonly class LeadTimeRule implements ReservationValidationRuleInterface
{
    private DateTimeZone $berlin;

    public function __construct(private readonly string $tz = 'Europe/Berlin')
    {
        $this->berlin = new DateTimeZone($tz);
    }

    public function validate(Reservation $reservation): array
    {
        $now = new DateTimeImmutable('now', $this->berlin);
        $tomorrow = new DateTimeImmutable($now->format('Y-m-d').' 08:00:00', $this->berlin)->modify('+1 day');

        if (new DateTimeImmutable($reservation->startDate)->setTimezone($this->berlin) < $tomorrow) {
            return [new Violation(
                'startDate',
                'lead_time_violation',
                'lead time is 1 day (earliest pickup is tomorrow 08:00)'
            )];
        }

        return [];
    }
}
