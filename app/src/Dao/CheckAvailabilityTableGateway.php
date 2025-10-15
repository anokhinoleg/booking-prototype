<?php

declare(strict_types=1);

namespace App\Dao;

use App\Enum\ReservationStatuses;
use DateTimeImmutable;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CheckAvailabilityTableGateway implements CheckAvailabilityGatewayInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function hasOverlap(int $vehicleId, DateTimeImmutable $startDateUtc, DateTimeImmutable $endDateUtc): bool
    {
        $connection = $this->entityManager->getConnection();

        $result = $connection->createQueryBuilder()
            ->select('1')
            ->from('reservation', 'r')
            ->where('r.vehicle_id = :vehicle_id')
            ->andWhere('r.status IN (:status)')
            ->andWhere('r.start_date < :end_date')
            ->andWhere('r.end_date > :start_date')
            ->setMaxResults(1)
            ->setParameters([
                'vehicle_id' => $vehicleId,
                'start_date' => $startDateUtc->format('Y-m-d H:i:s'),
                'end_date' => $endDateUtc->format('Y-m-d H:i:s'),
            ])->setParameter(
                key: 'status',
                value: ReservationStatuses::getOverlappingStatuses(),
                type: ArrayParameterType::STRING,
            )
            ->executeQuery();

        return (bool) $result->fetchOne();
    }
}
