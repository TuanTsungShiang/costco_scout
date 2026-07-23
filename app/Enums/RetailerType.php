<?php

namespace App\Enums;

enum RetailerType: string
{
    case PHYSICAL = 'PHYSICAL';
    case ONLINE   = 'ONLINE';
    case BOTH     = 'BOTH';

    public function label(): string
    {
        return match($this) {
            self::PHYSICAL => '實體',
            self::ONLINE   => '網路',
            self::BOTH     => '全通路',
        };
    }
}
