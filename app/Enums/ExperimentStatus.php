<?php

namespace App\Enums;

enum ExperimentStatus: string
{
    case PLANNED       = 'PLANNED';
    case LISTED        = 'LISTED';
    case PARTIALLY_SOLD = 'PARTIALLY_SOLD';
    case SOLD_OUT      = 'SOLD_OUT';
    case CANCELLED     = 'CANCELLED';
    case FAILED        = 'FAILED';

    public function label(): string
    {
        return match($this) {
            self::PLANNED        => '計劃中',
            self::LISTED         => '已上架',
            self::PARTIALLY_SOLD => '部分售出',
            self::SOLD_OUT       => '已售完',
            self::CANCELLED      => '已取消',
            self::FAILED         => '失敗',
        };
    }
}
