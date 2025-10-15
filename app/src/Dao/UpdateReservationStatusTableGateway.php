<?php

declare(strict_types=1);

namespace App\Dao;

use App\Dto\UpdatedReservationStatus;
use App\Enum\ReservationStatuses;
use App\Exception\ReservationStatusChangeException;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UpdateReservationStatusTableGateway implements UpdateReservationStatusGatewayInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function update(
        int $reservationId,
        ReservationStatuses $currentStatus,
        ReservationStatuses $newStatus,
    ): int {
        $connection = $this->entityManager->getConnection();

        $affectedRows = $connection->createQueryBuilder()
            ->update('reservation')
            ->set('status', ':new_status')
            ->where('id = :reservation_id')
            ->andWhere('status = :current_status')
            ->setParameters([
                'new_status' => $newStatus->value,
                'reservation_id' => $reservationId,
                'current_status' => $currentStatus->value,
            ])
            ->executeStatement();

        if ($affectedRows === 0) {
            throw new ReservationStatusChangeException(
                sprintf(
                    'Unable to change reservation status from %s to %s.',
                    $currentStatus->value,
                    $newStatus->value,
                )
            );
        }

        return (int) $affectedRows;
    }
}
