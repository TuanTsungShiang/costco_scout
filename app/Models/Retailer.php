<?php

namespace App\Models;

use App\Enums\RetailerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retailer extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'country_code', 'is_active',
    ];

    protected $casts = [
        'type'      => RetailerType::class,
        'is_active' => 'boolean',
    ];

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function productOffers(): HasMany
    {
        return $this->hasMany(ProductOffer::class);
    }
}
