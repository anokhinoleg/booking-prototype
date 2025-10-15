<?php

declare(strict_types=1);

namespace App\Service;

use App\Dao\CreateReservationGatewayInterface;
use App\Dto\Reservation;
use App\Dto\ReservationStatus;
use App\Enum\ReservationStatuses;
use App\Validation\Reservation\ReservationValidator;
use App\Validation\Reservation\Violation;

final readonly class CreateReservation
{
    public function __construct(
        private CreateReservationGatewayInterface $newReservation,
        private ReservationValidator              $newReservationValidator,
    ) {
    }

    public function execute(Reservation $reservation): ReservationStatus
    {
        try {
            $violations = $this->newReservationValidator->violate($reservation);

            if (count($violations) !== 0) {
                return new ReservationStatus(
                    id: null,
                    status: ReservationStatuses::FAILED->value,
                    violations: array_map(
                        static fn(Violation $violation): array => [
                            'field' => $violation->field,
                            'code' => $violation->code,
                            'message' => $violation->message,
                        ],
                        $violations,
                    ),
                    message: 'Invalid input data.',
                );
            }

            $id = $this->newReservation->save($reservation);

            return new ReservationStatus(
                id: $id,
                status: ReservationStatuses::REQUESTED->value,
                message: 'Reservation is created.',
            );
        } catch (\Exception $exception) {
            return new ReservationStatus(
                id: null,
                status: ReservationStatuses::FAILED->value,
                message: $exception->getMessage(),
            );
        }
    }
}
