<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    required: [
        'vehicleId',
        'startDate',
        'endDate',
    ],
)]
final readonly class AvailabilityRequest
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1, nullable: false)]
        public int $vehicleId,
        #[OA\Property(type: 'string', format: 'date-time', example: '2025-10-20T08:00:00+02:00', nullable: false)]
        public string $startDate,
        #[OA\Property(type: 'string', format: 'date-time', example: '2025-10-22T08:00:00+02:00', nullable: false)]
        public string $endDate,
    ) {
    }
}
