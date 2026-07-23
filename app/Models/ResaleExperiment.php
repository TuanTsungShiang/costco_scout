<?php

namespace App\Models;

use App\Enums\ExperimentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResaleExperiment extends Model
{
    protected $fillable = [
        'resale_analysis_id', 'quantity_purchased', 'quantity_listed', 'quantity_sold',
        'purchase_total_minor', 'actual_average_sale_amount_minor',
        'actual_platform_fee_minor', 'actual_payment_fee_minor',
        'actual_shipping_minor', 'actual_packaging_minor',
        'actual_other_cost_minor', 'actual_net_profit_minor',
        'listed_at', 'first_sold_at', 'completed_at', 'status', 'notes',
    ];

    protected $casts = [
        'status'       => ExperimentStatus::class,
        'listed_at'    => 'datetime',
        'first_sold_at'=> 'datetime',
        'completed_at' => 'datetime',
    ];

    public function resaleAnalysis(): BelongsTo
    {
        return $this->belongsTo(ResaleAnalysis::class);
    }

    public function actualRoiPercent(): ?float
    {
        if ($this->purchase_total_minor === null || $this->purchase_total_minor == 0) {
            return null;
        }
        return $this->actual_net_profit_minor / $this->purchase_total_minor * 100;
    }

    public function isComplete(): bool
    {
        return in_array($this->status, [
            ExperimentStatus::SOLD_OUT,
            ExperimentStatus::CANCELLED,
            ExperimentStatus::FAILED,
        ]);
    }
}
