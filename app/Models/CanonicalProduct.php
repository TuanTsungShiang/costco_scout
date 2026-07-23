<?php

namespace App\Models;

use App\Enums\ComparisonMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CanonicalProduct extends Model
{
    protected $fillable = [
        'brand', 'name', 'gtin', 'comparison_mode',
        'package_count', 'content_per_package', 'content_unit',
        'comparison_quantity', 'comparison_unit', 'notes',
    ];

    protected $casts = [
        'comparison_mode' => ComparisonMode::class,
    ];

    public function productOffers(): HasMany
    {
        return $this->hasMany(ProductOffer::class);
    }

    public function resaleAnalyses(): HasMany
    {
        return $this->hasMany(ResaleAnalysis::class);
    }

    public function totalContentAmount(): int
    {
        return $this->package_count * $this->content_per_package;
    }

    public function canComputeUnitPrice(): bool
    {
        return $this->comparison_mode->canComputeUnitPrice();
    }
}
