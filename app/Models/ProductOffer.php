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

    /**
     * 取得此 offer 最新的 VALID 觀測。
     * 傳入 $storeId 時只比對同一 store（賣場 vs 線上 是不同 store，
     * 各自獨立 supersede，不會互相覆蓋）。傳 false 代表不分 store。
     */
    public function latestValidObservation(int|null|false $storeId = false): ?PriceObservation
    {
        $query = $this->priceObservations()->where('status', 'VALID');

        if ($storeId !== false) {
            $query->where('store_id', $storeId);
        }

        return $query->latest('created_at')->first();
    }
}
