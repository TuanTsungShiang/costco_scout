<?php

namespace App\Services;

use App\Models\CanonicalProduct;

class UnitPriceCalculator
{
    /**
     * Returns unit price in minor units per comparison_quantity.
     * Returns null for BUNDLE products where unit price is meaningless.
     */
    public function calculate(CanonicalProduct $product, int $totalPriceMinor): ?int
    {
        if (! $product->canComputeUnitPrice()) {
            return null;
        }

        $totalContent = $product->totalContentAmount();

        if ($totalContent <= 0 || $product->comparison_quantity <= 0) {
            return null;
        }

        // unit_price_minor = price_minor * comparison_quantity / total_content
        return (int) round($totalPriceMinor * $product->comparison_quantity / $totalContent);
    }

    public function formatLabel(CanonicalProduct $product): string
    {
        return "每 {$product->comparison_quantity}{$product->comparison_unit}";
    }
}
