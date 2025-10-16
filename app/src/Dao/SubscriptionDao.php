<?php

declare(strict_types=1);

namespace App\Dao;

use DateTimeImmutable;

final class SubscriptionDao
{
    private const array SUBSCRIPTIONS = [
        1 => [
            'vehicle_id' => 2,
            'start_date' => '2025-12-01',
            'end_date' => '2026-02-28',
            'source' => 'subscription'
        ],
        2 => [
            'vehicle_id' => 3,
            'start_date' => '2025-01-01',
            'end_date' => '2025-06-30',
            'source' => 'subscription'
        ],
        3 => [
            'vehicle_id' => 4,
            'start_date' => '2025-03-01',
            'end_date' => '2025-07-31',
            'source' => 'subscription'
        ],
        4 => [
            'vehicle_id' => 5,
            'start_date' => '2025-04-15',
            'end_date' => '2025-10-14',
            'source' => 'subscription'
        ],
        5 => [
            'vehicle_id' => 1,
            'start_date' => '2025-08-01',
            'end_date' => '2026-01-31',
            'source' => 'subscription'
        ],
        6 => [
            'vehicle_id' => 6,
            'start_date' => '2025-09-01',
            'end_date' => '2026-02-28',
            'source' => 'subscription'
        ],
    ];

    /**
     * @return array<int, array{start: DateTimeImmutable, end: DateTimeImmutable}>
     */
    public function findByVehicleId(int $vehicleId): array
    {
        $subscriptions = array_filter(
            self::SUBSCRIPTIONS,
            static fn (array $subscription): bool => $subscription['vehicle_id'] === $vehicleId,
        );

        return array_values(array_map(
            static fn (array $subscription): array => [
                'start' => new DateTimeImmutable($subscription['start_date']),
                'end' => new DateTimeImmutable($subscription['end_date']),
            ],
            $subscriptions,
        ));
    }
}
