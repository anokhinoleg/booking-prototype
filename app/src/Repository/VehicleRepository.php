<?php

declare(strict_types=1);

namespace App\Repository;

final class VehicleRepository implements VehicleRepositoryInterface
{
    private const array VEHICLES = [
        1 => [
            'id' => 1,
            'make' => 'Ford',
            'name' => 'Ford Puma',
        ],
        2 => [
            'id' => 2,
            'make' => 'Audi',
            'name' => 'Audi A4',
        ],
        3 => [
            'id' => 3,
            'make' => 'VW',
            'name' => 'VW Polo',
        ],
        4 => [
            'id' => 4,
            'make' => 'VW',
            'name' => 'VW Tiguan',
        ],
        5 => [
            'id' => 5,
            'make' => 'VW',
            'name' => 'VW Taigo',
        ],
        6 => [
            'id' => 6,
            'make' => 'Audi',
            'name' => 'Audi A4 Avant S-line',
        ]
    ];

    public function findAll(): array
    {
        return $this::VEHICLES;
    }

    public function findById(int $id): array
    {
        return $this::VEHICLES[$id];
    }
}
