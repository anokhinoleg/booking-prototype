<?php

declare(strict_types=1);

namespace App\Validation\Availability\Rules;

use App\Dto\AvailabilityRequest;
use App\Validation\Availability\Violation;

final readonly class VehicleIdRule implements AvailabilityValidationRuleInterface
{
    public function validate(AvailabilityRequest $availability): array
    {
        if ($availability->vehicleId <= 0) {
            return [new Violation('vehicleId', 'invalid_vehicle', 'vehicleId must be a positive integer.')];
        }

        return [];
    }
}
