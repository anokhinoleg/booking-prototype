<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(required: ['pickup_utc', 'return_utc'])]
final readonly class NormalizedAvailabilityWindow
{
    public function __construct(
        #[OA\Property(property: 'pickup_utc', type: 'string', example: '2025-10-20 06:00:00')]
        public string $pickupUtc,
        #[OA\Property(property: 'return_utc', type: 'string', example: '2025-10-22 06:00:00')]
        public string $returnUtc,
    ) {
    }
}
