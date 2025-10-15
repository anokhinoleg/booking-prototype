<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\Dto\NormalizedAvailabilityWindow;
use DateTimeImmutable;
use DateTimeZone;

final readonly class AvailabilityWindowNormalizer
{
    private DateTimeZone $timezone;

    public function __construct(private string $targetTimezone = 'UTC')
    {
        $this->timezone = new DateTimeZone($this->targetTimezone);
    }

    public function normalize(DateTimeImmutable $pickupAt, DateTimeImmutable $returnAt): NormalizedAvailabilityWindow
    {
        return new NormalizedAvailabilityWindow(
            pickupUtc: $pickupAt->setTimezone($this->timezone)->format('Y-m-d H:i:s'),
            returnUtc: $returnAt->setTimezone($this->timezone)->format('Y-m-d H:i:s'),
        );
    }

    public function timezone(): DateTimeZone
    {
        return $this->timezone;
    }
}
