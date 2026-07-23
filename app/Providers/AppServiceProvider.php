<?php

namespace App\Providers;

use App\Services\ExperimentResultService;
use App\Services\ResaleDecisionService;
use App\Services\ResaleProfitCalculator;
use App\Services\RewardCalculator;
use App\Services\UnitPriceCalculator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RewardCalculator::class, function ($app) {
            return new RewardCalculator(config('arbitrage.costco'));
        });

        $this->app->singleton(ResaleDecisionService::class, function ($app) {
            return new ResaleDecisionService(config('arbitrage.resale'));
        });

        $this->app->singleton(ResaleProfitCalculator::class, function ($app) {
            return new ResaleProfitCalculator($app->make(RewardCalculator::class));
        });

        $this->app->singleton(UnitPriceCalculator::class);
        $this->app->singleton(ExperimentResultService::class);
    }

    public function boot(): void {}
}
