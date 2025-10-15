<?php

declare(strict_types=1);

namespace App\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(required: ['available'])]
final readonly class AvailabilityStatus
{
    /**
     * @param array<int, array{field: string, code: string, message: string}> $violations
     */
    public function __construct(
        #[OA\Property(type: 'boolean')]
        public bool $available,
        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'field', type: 'string'),
                    new OA\Property(property: 'code', type: 'string'),
                    new OA\Property(property: 'message', type: 'string'),
                ],
                type: 'object',
            ),
            nullable: true,
        )]
        public array $violations = [],
        public ?string $message = null,
    ) {
    }
}
