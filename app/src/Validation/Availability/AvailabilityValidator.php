<?php

declare(strict_types=1);

namespace App\Validation\Availability;

use App\Dto\AvailabilityRequest;
use App\Validation\Availability\Rules\AvailabilityValidationRuleInterface;

final readonly class AvailabilityValidator
{
    /** @param AvailabilityValidationRuleInterface[] $validationRules */
    public function __construct(private array $validationRules)
    {
    }

    /**
     * @return Violation[]
     */
    public function violate(AvailabilityRequest $availability): array
    {
        $violations = [];

        foreach ($this->validationRules as $rule) {
            $violations = array_merge($violations, $rule->validate($availability));

            if (count($violations) !== 0) {
                return $violations;
            }
        }

        return $violations;
    }
}
