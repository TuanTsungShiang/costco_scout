<?php

namespace App\Models;

use App\Enums\MarketDataStatus;
use App\Enums\ResaleDecision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResaleAnalysis extends Model
{
    protected $fillable = [
        'canonical_product_id', 'purchase_price_observation_id', 'sales_channel_id',
        'expected_sale_amount_minor',
        'estimated_platform_fee_minor', 'estimated_payment_fee_minor',
        'estimated_promotion_fee_minor', 'estimated_shipping_minor',
        'estimated_packaging_minor', 'estimated_return_loss_minor',
        'estimated_other_cost_minor', 'estimated_membership_reward_minor',
        'estimated_net_profit_minor', 'roi_basis_points', 'profit_margin_basis_points',
        'break_even_amount_minor', 'market_data_status', 'decision', 'analyzed_at',
    ];

    protected $casts = [
        'market_data_status' => MarketDataStatus::class,
        'decision'           => ResaleDecision::class,
        'analyzed_at'        => 'datetime',
    ];

    public function canonicalProduct(): BelongsTo
    {
        return $this->belongsTo(CanonicalProduct::class);
    }

    public function purchasePriceObservation(): BelongsTo
    {
        return $this->belongsTo(PriceObservation::class, 'purchase_price_observation_id');
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }

    public function experiments(): HasMany
    {
        return $this->hasMany(ResaleExperiment::class);
    }

    public function roiPercent(): float
    {
        return $this->roi_basis_points / 100;
    }

    public function profitMarginPercent(): float
    {
        return $this->profit_margin_basis_points / 100;
    }

    public function totalEstimatedCostMinor(): int
    {
        return $this->estimated_platform_fee_minor
            + $this->estimated_payment_fee_minor
            + $this->estimated_promotion_fee_minor
            + $this->estimated_shipping_minor
            + $this->estimated_packaging_minor
            + $this->estimated_return_loss_minor
            + $this->estimated_other_cost_minor;
    }
}
