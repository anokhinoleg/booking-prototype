<?php

declare(strict_types=1);

namespace App\Dao;

use DateTimeImmutable;

interface CheckAvailabilityGatewayInterface
{
    public function hasOverlap(int $vehicleId, DateTimeImmutable $startDateUtc, DateTimeImmutable $endDateUtc): bool;
}
