<?php

declare(strict_types=1);

namespace App\Enum;

enum ReservationStatuses: string
{
    case REQUESTED = 'REQUESTED';
    case CONFIRMED = 'CONFIRMED';
    case DECLINED = 'DECLINED';
    case FAILED = 'FAILED';
//    case CANCELLED = 'CANCELLED';

    public static function getOverlappingStatuses(): array
    {
        return [
            self::REQUESTED->value,
            self::CONFIRMED->value,
        ];
    }
}
