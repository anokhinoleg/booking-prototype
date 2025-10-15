<?php

declare(strict_types=1);

namespace App\Service;

use App\Dao\CheckAvailabilityGatewayInterface;
use App\Dto\AvailabilityRequest;
use App\Dto\AvailabilityStatus;
use App\Dto\Reservation;
use App\Validation\Reservation\ReservationValidator;
use App\Validation\Reservation\Violation;
use DateTimeImmutable;
use DateTimeZone;

final readonly class CheckAvailability
{
    public function __construct(
        private ReservationValidator $reservationValidator,
        private CheckAvailabilityGatewayInterface $availabilityGateway,
    ) {
    }

    public function execute(AvailabilityRequest $availabilityRequest): AvailabilityStatus
    {
        try {
            $reservation = new Reservation(
                vehicleId: $availabilityRequest->vehicleId,
                customerEmail: '',
                startDate: $availabilityRequest->startDate,
                endDate: $availabilityRequest->endDate,
                pickupLocation: '',
                dropOffLocation: '',
            );

            $violations = $this->reservationValidator->violate($reservation);

            if (count($violations) !== 0) {
                return new AvailabilityStatus(
                    available: false,
                    violations: array_map(
                        static fn(Violation $violation): array => [
                            'field' => $violation->field,
                            'code' => $violation->code,
                            'message' => $violation->message,
                        ],
                        $violations,
                    ),
                );
            }

            $utc = new DateTimeZone('UTC');
            $startDate = (new DateTimeImmutable($availabilityRequest->startDate))->setTimezone($utc);
            $endDate = (new DateTimeImmutable($availabilityRequest->endDate))->setTimezone($utc);

            $hasOverlap = $this->availabilityGateway->hasOverlap(
                $availabilityRequest->vehicleId,
                $startDate,
                $endDate,
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
                );
            }

            return new AvailabilityStatus(available: true);
        } catch (\Exception) {
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
}
