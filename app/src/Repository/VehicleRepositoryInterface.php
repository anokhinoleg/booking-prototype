<?php

declare(strict_types=1);

namespace App\Repository;

interface VehicleRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): array;
}
