<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesChannel extends Model
{
    protected $fillable = [
        'name', 'slug',
        'platform_fee_basis_points',
        'payment_fee_basis_points',
        'promotion_fee_basis_points',
        'default_shipping_minor',
        'default_packaging_minor',
        'expected_return_loss_basis_points',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function resaleAnalyses(): HasMany
    {
        return $this->hasMany(ResaleAnalysis::class);
    }

    public function totalFeeBasisPoints(): int
    {
        return $this->platform_fee_basis_points
            + $this->payment_fee_basis_points
            + $this->promotion_fee_basis_points
            + $this->expected_return_loss_basis_points;
    }

    public function estimatedFeeMinor(int $salePriceMinor): int
    {
        return (int) round($salePriceMinor * $this->totalFeeBasisPoints() / 10000);
    }

    public function totalFixedCostMinor(): int
    {
        return $this->default_shipping_minor + $this->default_packaging_minor;
    }
}
