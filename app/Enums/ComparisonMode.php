<?php

namespace App\Enums;

enum ComparisonMode: string
{
    case WEIGHT = 'WEIGHT';
    case VOLUME = 'VOLUME';
    case COUNT  = 'COUNT';
    case SHEET  = 'SHEET';
    case BUNDLE = 'BUNDLE';

    public function label(): string
    {
        return match($this) {
            self::WEIGHT => '重量',
            self::VOLUME => '容量',
            self::COUNT  => '數量',
            self::SHEET  => '片數',
            self::BUNDLE => '套組',
        };
    }

    public function canComputeUnitPrice(): bool
    {
        return $this !== self::BUNDLE;
    }
}
