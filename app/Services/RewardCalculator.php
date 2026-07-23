<?php

namespace App\Services;

class RewardCalculator
{
    public function __construct(private array $config) {}

    /**
     * Estimate the Executive member reward for a given purchase price.
     * Returns integer minor units.
     */
    public function estimateExecutiveReward(int $purchasePriceMinor): int
    {
        $tier = $this->config['membership_tier'] ?? 'EXECUTIVE';

        if ($tier !== 'EXECUTIVE') {
            return 0;
        }

        $rateBps = $this->config['executive_reward_rate_basis_points'] ?? null;
        $capMinor = $this->config['executive_reward_cap_minor'] ?? null;

        if ($rateBps === null) {
            return 0;
        }

        $reward = (int) round($purchasePriceMinor * $rateBps / 10000);

        if ($capMinor !== null && $reward > (int) $capMinor) {
            $reward = (int) $capMinor;
        }

        return $reward;
    }

    /**
     * Estimate co-brand card cashback on a purchase.
     */
    public function estimateCobrandReward(int $purchasePriceMinor): int
    {
        $rateBps = $this->config['cobrand_card_rate_basis_points'] ?? null;

        if ($rateBps === null) {
            return 0;
        }

        return (int) round($purchasePriceMinor * $rateBps / 10000);
    }

    public function totalReward(int $purchasePriceMinor): int
    {
        return $this->estimateExecutiveReward($purchasePriceMinor)
             + $this->estimateCobrandReward($purchasePriceMinor);
    }
}
