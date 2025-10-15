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

    public function hasOverlap(int $vehicleId, DateTimeImmutable $pickupAtUtc, DateTimeImmutable $returnAtUtc): bool
    {
        $connection = $this->entityManager->getConnection();

        $result = $connection->createQueryBuilder()
            ->select('1')
            ->from('reservation', 'r')
            ->where('r.vehicle_id = :vehicle_id')
            ->andWhere('r.status IN (:status)')
            ->andWhere('r.start_date < :return_at')
            ->andWhere('r.end_date > :pickup_at')
            ->setMaxResults(1)
            ->setParameters([
                'vehicle_id' => $vehicleId,
                'pickup_at' => $pickupAtUtc->format('Y-m-d H:i:s'),
                'return_at' => $returnAtUtc->format('Y-m-d H:i:s'),
            ])->setParameter(
                key: 'status',
                value: ReservationStatuses::getOverlappingStatuses(),
                type: ArrayParameterType::STRING,
            )
            ->executeQuery();

        return (bool) $result->fetchOne();
    }
}
