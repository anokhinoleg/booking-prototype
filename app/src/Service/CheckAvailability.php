<?php

declare(strict_types=1);

namespace App\Service;

use App\Dao\CheckAvailabilityGatewayInterface;
use App\Dto\AvailabilityCheck;
use App\Dto\AvailabilityStatus;
use DateTimeImmutable;
use DateTimeZone;

final readonly class CheckAvailability
{
    public function __construct(
        private CheckAvailabilityGatewayInterface $availabilityGateway,
    ) {
    }

    public function execute(AvailabilityCheck $availabilityRequest): AvailabilityStatus
    {
        try {
            $utc = new DateTimeZone('UTC');
            $startDate = new DateTimeImmutable($availabilityRequest->startDate)->setTimezone($utc);
            $endDate = new DateTimeImmutable($availabilityRequest->endDate)->setTimezone($utc);

            $hasOverlap = $this->availabilityGateway->hasOverlap(
                $availabilityRequest->vehicleId,
                $startDate,
                $endDate,
            );

            if ($hasOverlap) {
                return new AvailabilityStatus(
                    available: false,
                    message: 'The vehicle has already booked for the provided dates.',
                );
            }

            return new AvailabilityStatus(
                available: true,
                message: 'Vehicle is free for the provided dates.',
            );
        } catch (\Exception $exception) {
            return new AvailabilityStatus(
                available: false,
                message: $exception->getMessage(),
            );
        }
    }
}
