<?php

namespace App\Enums;

enum InterestStatusEnum: string
{
    case PENDING = 'pending';
    case CONTACTED = 'contacted';
    case ENROLLED = 'enrolled';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
