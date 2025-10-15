<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    required: [
        'vehicleId',
        'customerEmail',
        'startDate',
        'endDate',
        'pickupLocation',
        'dropOffLocation',
    ],
)]
final readonly class Reservation
{
    public function __construct(
        public int $vehicleId,
        public string $customerEmail,
        #[OA\Property(
            type: 'string',
            format: 'date-time',
            nullable: false
        )]
        public string $startDate,
        #[OA\Property(
            type: 'string',
            format: 'date-time',
            nullable: false
        )]
        public string $endDate,
        public string $pickupLocation,
        public string $dropOffLocation,
        #[OA\Property(type: 'boolean', default: false)]
        public bool $isCustomDropOffLocation = false,
    ) {
    }
}
