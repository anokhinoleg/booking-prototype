<?php

declare(strict_types=1);

namespace App\Dao;

use App\Dto\Reservation;
use App\Enum\ReservationStatuses;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateReservationTableGateway implements CreateReservationGatewayInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Reservation $reservation): int
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            //locks rows for subsequential update
            $connection->createQueryBuilder()
                ->select('id')
                ->from('reservation', 'r')
                ->where('r.vehicle_id = :vehicle_id')
                ->andWhere('r.status IN (:status)')
                ->andWhere('r.start_date < :end_date')
                ->andWhere('r.end_date > :start_date')
                ->setParameters([
                    'vehicle_id' => $reservation->vehicleId,
                    'start_date' => new \DateTimeImmutable($reservation->startDate)->format('Y-m-d H:i:s'),
                    'end_date' => new \DateTimeImmutable($reservation->endDate)->format('Y-m-d H:i:s'),
                ])->setParameter(
                    key: 'status',
                    value: ReservationStatuses::getOverlappingStatuses(),
                    type: ArrayParameterType::STRING
                )->forUpdate()
                ->executeQuery();

            $isOverlap = $connection->createQueryBuilder()
                ->select('1')
                ->from('reservation', 'r')
                ->where('r.vehicle_id = :vehicle_id')
                ->andWhere('r.status in (:status)')
                ->andWhere('r.start_date < :end_date')
                ->andWhere('r.end_date > :start_date')
                ->setMaxResults(1)
                ->setParameters([
                    'vehicle_id' => $reservation->vehicleId,
                    'start_date' => new \DateTimeImmutable($reservation->startDate)->format('Y-m-d H:i:s'),
                    'end_date'=> new \DateTimeImmutable($reservation->endDate)->format('Y-m-d H:i:s'),
                ])->setParameter(
                    key: 'status',
                    value: ReservationStatuses::getOverlappingStatuses(),
                    type: ArrayParameterType::STRING
                )->executeQuery();

            if ($isOverlap->fetchOne()) {
                throw new \Exception('Overlapping reservation failed');
            }

            $connection->createQueryBuilder()
                ->insert('reservation')
                ->values([
                    'vehicle_id' => ':vehicle_id',
                    'customer_email' => ':customer_email',
                    'start_date' => ':start_date',
                    'end_date' => ':end_date',
                    'pickup_location' => ':pickup_location',
                    'drop_off_location' => ':drop_off_location',
                    'is_custom_drop_off_location' => ':is_custom_drop_off_location',
                    'status' => ':status',
                ])->setParameters([
                    'vehicle_id' => $reservation->vehicleId,
                    'customer_email' => $reservation->customerEmail,
                    'start_date' => new \DateTimeImmutable($reservation->startDate)->format('Y-m-d H:i:s'),
                    'end_date' => new \DateTimeImmutable($reservation->endDate)->format('Y-m-d H:i:s'),
                    'pickup_location' => $reservation->pickupLocation,
                    'drop_off_location' => $reservation->dropOffLocation,
                    'is_custom_drop_off_location' => $reservation->isCustomDropOffLocation ? 1 : 0,
                    'status' => ReservationStatuses::REQUESTED->value,
                ])->executeStatement();
            $id = $connection->lastInsertId();
            $connection->commit();

            return (int) $id;
        } catch (\Exception $e) {
            try { $connection->rollBack(); } catch (\Throwable) {}

            throw $e;
        }
    }
}
