<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'retailer_id', 'branch_name', 'country_code',
        'currency_code', 'timezone', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(Retailer::class);
    }

    public function priceTagCaptures(): HasMany
    {
        return $this->hasMany(PriceTagCapture::class);
    }
}
