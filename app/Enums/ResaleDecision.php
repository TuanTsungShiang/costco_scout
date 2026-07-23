<?php

namespace App\Enums;

enum ResaleDecision: string
{
    case PASS          = 'PASS';
    case WATCH         = 'WATCH';
    case TEST_ONE_UNIT = 'TEST_ONE_UNIT';
    case RESTOCK       = 'RESTOCK';
    case SCALE         = 'SCALE';

    public function label(): string
    {
        return match($this) {
            self::PASS          => '跳過',
            self::WATCH         => '觀察',
            self::TEST_ONE_UNIT => '買一件試賣',
            self::RESTOCK       => '可以補貨',
            self::SCALE         => '擴大規模',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::PASS          => 'danger',
            self::WATCH         => 'warning',
            self::TEST_ONE_UNIT => 'primary',
            self::RESTOCK       => 'success',
            self::SCALE         => 'success',
        };
    }
}
