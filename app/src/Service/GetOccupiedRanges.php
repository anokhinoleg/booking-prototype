<?php

declare(strict_types=1);

namespace App\Service;

use App\Dao\OccupiedRangesReservationsDao;
use App\Dao\SubscriptionDao;
use DateTimeImmutable;

final readonly class GetOccupiedRanges
{
    public function __construct(
        private OccupiedRangesReservationsDao $reservationsDao,
        private SubscriptionDao $subscriptionDao,
    ) {
    }

    /**
     * @return array<int, array{startDate: string, endDate: string}>
     */
    public function execute(int $vehicleId): array
    {
        $reservationRanges = $this->reservationsDao->findByVehicleId($vehicleId);
        $subscriptionRanges = $this->subscriptionDao->findByVehicleId($vehicleId);

        $allRanges = array_merge($reservationRanges, $subscriptionRanges);

        if ($allRanges === []) {
            return [];
        }

        usort(
            $allRanges,
            static fn (array $left, array $right): int => $left['start'] <=> $right['start'],
        );

        $merged = [];

        foreach ($allRanges as $range) {
            $start = $this->toDateOnly($range['start']);
            $end = $this->toDateOnly($range['end']);

            if ($merged === []) {
                $merged[] = ['start' => $start, 'end' => $end];

                continue;
            }

            $lastIndex = count($merged) - 1;
            $lastRange = $merged[$lastIndex];

            if ($start <= $lastRange['end']) {
                $merged[$lastIndex]['end'] = $end > $lastRange['end'] ? $end : $lastRange['end'];

                continue;
            }

            $merged[] = ['start' => $start, 'end' => $end];
        }

        return array_map(
            static fn (array $range): array => [
                'startDate' => $range['start']->format('Y-m-d'),
                'endDate' => $range['end']->format('Y-m-d'),
            ],
            $merged,
        );
    }

    private function toDateOnly(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->setTime(0, 0);
    }
}
