<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOffer extends Model
{
    protected $fillable = [
        'canonical_product_id', 'retailer_id',
        'external_product_id', 'external_url', 'external_title',
        'confirmed_at', 'confirmed_by', 'is_active',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'is_active'    => 'boolean',
    ];

    public function canonicalProduct(): BelongsTo
    {
        return $this->belongsTo(CanonicalProduct::class);
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(Retailer::class);
    }

    public function priceObservations(): HasMany
    {
        return $this->hasMany(PriceObservation::class);
    }

    public function latestValidObservation(): ?PriceObservation
    {
        return $this->priceObservations()
            ->where('status', 'VALID')
            ->latest('created_at')
            ->first();
    }
}
