<?php

namespace App\Enums;

enum ContentUnit: string
{
    case G     = 'g';
    case KG    = 'kg';
    case ML    = 'ml';
    case L     = 'L';
    case SHEET = 'sheet';
    case COUNT = 'count';

    public function label(): string
    {
        return match($this) {
            self::G     => '公克',
            self::KG    => '公斤',
            self::ML    => '毫升',
            self::L     => '公升',
            self::SHEET => '片',
            self::COUNT => '個',
        };
    }
}
