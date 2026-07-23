<?php

namespace App\Models;

use App\Enums\ObservationSourceType;
use App\Enums\ObservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceObservation extends Model
{
    // Append-only: no updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'product_offer_id', 'store_id', 'raw_capture_id',
        'amount_minor', 'currency_code', 'fx_rate_to_base',
        'observed_at', 'source_type', 'status', 'notes',
        'invalidated_at', 'invalidated_reason', 'superseded_by_id',
    ];

    protected $casts = [
        'amount_minor'    => 'integer',
        'fx_rate_to_base' => 'decimal:10',
        'observed_at'     => 'date',
        'source_type'     => ObservationSourceType::class,
        'status'          => ObservationStatus::class,
        'invalidated_at'  => 'datetime',
    ];

    public function productOffer(): BelongsTo
    {
        return $this->belongsTo(ProductOffer::class);
    }

    public function priceTagCapture(): BelongsTo
    {
        return $this->belongsTo(PriceTagCapture::class);
    }

    public function supersededBy(): BelongsTo
    {
        return $this->belongsTo(PriceObservation::class, 'superseded_by_id');
    }

    public function amountInBaseCurrencyMinor(): int
    {
        if ($this->fx_rate_to_base === null || $this->fx_rate_to_base == 1) {
            return $this->amount_minor;
        }
        return (int) round($this->amount_minor * (float) $this->fx_rate_to_base);
    }

    public function isValid(): bool
    {
        return $this->status === ObservationStatus::VALID;
    }
}
