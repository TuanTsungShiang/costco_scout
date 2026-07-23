<?php

namespace App\Enums;

enum MarketDataStatus: string
{
    case UNVERIFIED          = 'UNVERIFIED';
    case LISTING_PRICE_ONLY  = 'LISTING_PRICE_ONLY';
    case MANUAL_MARKET_CHECK = 'MANUAL_MARKET_CHECK';
    case OWN_SALES_HISTORY   = 'OWN_SALES_HISTORY';

    public function label(): string
    {
        return match($this) {
            self::UNVERIFIED          => '未驗證',
            self::LISTING_PRICE_ONLY  => '僅有標價',
            self::MANUAL_MARKET_CHECK => '人工比價',
            self::OWN_SALES_HISTORY   => '自有銷售紀錄',
        };
    }

    public function isLow(): bool
    {
        return $this === self::UNVERIFIED;
    }

    public function hasSalesData(): bool
    {
        return $this === self::OWN_SALES_HISTORY;
    }
}
