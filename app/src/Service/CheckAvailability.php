<?php

declare(strict_types=1);

namespace App\Service;

use App\Dao\CheckAvailabilityGatewayInterface;
use App\Dto\AvailabilityRequest;
use App\Dto\AvailabilityStatus;
use App\Service\Normalizer\AvailabilityWindowNormalizer;
use App\Validation\Availability\AvailabilityValidator;
use App\Validation\Availability\Violation;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;

final readonly class CheckAvailability
{
    public function __construct(
        private AvailabilityValidator $availabilityValidator,
        private CheckAvailabilityGatewayInterface $availabilityGateway,
        private AvailabilityWindowNormalizer $availabilityWindowNormalizer,
    ) {
    }

    public function execute(AvailabilityRequest $availabilityRequest): AvailabilityStatus
    {
        try {
            $violations = $this->availabilityValidator->violate($availabilityRequest);

            if (count($violations) !== 0) {
                return new AvailabilityStatus(
                    available: false,
                    violations: array_map(
                        fn(Violation $violation): array => $this->mapViolation($violation),
                        $violations,
                    ),
                );
            }

            $pickupAt = new DateTimeImmutable($availabilityRequest->pickupAt);
            $returnAt = new DateTimeImmutable($availabilityRequest->returnAt);

            $normalized = $this->availabilityWindowNormalizer->normalize($pickupAt, $returnAt);
            $timezone = $this->resolveTimezone();
            $hasOverlap = $this->availabilityGateway->hasOverlap(
                $availabilityRequest->vehicleId,
                $pickupAt->setTimezone($timezone),
                $returnAt->setTimezone($timezone),
            );

            if ($hasOverlap) {
                return new AvailabilityStatus(
                    available: false,
                    violations: [
                        [
                            'field' => 'dateRange',
                            'code' => 'overlap',
                            'message' => 'Vehicle is already reserved for the selected window.',
                        ],
                    ],
                    normalized: $normalized,
                );
            }

            return new AvailabilityStatus(
                available: true,
                violations: [],
                normalized: $normalized,
            );
        } catch (Throwable) {
            return new AvailabilityStatus(
                available: false,
                violations: [
                    [
                        'field' => 'system',
                        'code' => 'unavailable',
                        'message' => 'Unable to verify availability. Please try again later.',
                    ],
                ],
            );
        }
    }

    private function resolveTimezone(): DateTimeZone
    {
        return $this->availabilityWindowNormalizer->timezone();
    }

    private function mapViolation(Violation $violation): array
    {
        return [
            'field' => $violation->field,
            'code' => $violation->code,
            'message' => $violation->message,
        ];
    }
}
