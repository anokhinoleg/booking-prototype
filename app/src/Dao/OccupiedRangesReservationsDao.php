<?php

declare(strict_types=1);

namespace App\Dao;

use App\Enum\ReservationStatuses;
use DateTimeImmutable;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OccupiedRangesReservationsDao
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return array<int, array{start: DateTimeImmutable, end: DateTimeImmutable}>
     */
    public function findByVehicleId(int $vehicleId): array
    {
        $connection = $this->entityManager->getConnection();

        $results = $connection->createQueryBuilder()
            ->select('r.start_date', 'r.end_date')
            ->from('reservation', 'r')
            ->where('r.vehicle_id = :vehicle_id')
            ->andWhere('r.status IN (:statuses)')
            ->orderBy('r.start_date', 'ASC')
            ->setParameter('vehicle_id', $vehicleId)
            ->setParameter(
                key: 'statuses',
                value: ReservationStatuses::getOverlappingStatuses(),
                type: ArrayParameterType::STRING,
            )
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(
            static fn (array $row): array => [
                'start' => new DateTimeImmutable($row['start_date']),
                'end' => new DateTimeImmutable($row['end_date']),
            ],
            $results,
        );
    }
}
