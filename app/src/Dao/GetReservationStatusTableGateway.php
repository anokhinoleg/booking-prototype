<?php

declare(strict_types=1);

namespace App\Dao;

use App\Enum\ReservationStatuses;
use App\Exception\ReservationNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetReservationStatusTableGateway implements GetReservationStatusGatewayInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function get(int $reservationId): ReservationStatuses
    {
        $connection = $this->entityManager->getConnection();

        $status = $connection->createQueryBuilder()
            ->select('r.status')
            ->from('reservation', 'r')
            ->where('r.id = :reservation_id')
            ->setMaxResults(1)
            ->setParameter('reservation_id', $reservationId)
            ->executeQuery()
            ->fetchOne();

        if ($status === false) {
            throw new ReservationNotFoundException('Reservation not found.');
        }

        return ReservationStatuses::from($status);
    }
}
