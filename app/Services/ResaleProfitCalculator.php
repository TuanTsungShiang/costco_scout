<?php

namespace App\Services;

use App\Models\PriceObservation;
use App\Models\SalesChannel;

class ResaleProfitCalculator
{
    public function __construct(private RewardCalculator $rewardCalculator) {}

    /**
     * Calculate all estimated figures for one unit resale.
     * All monetary values returned as integer minor units.
     */
    public function calculate(
        PriceObservation $purchaseObservation,
        SalesChannel $channel,
        int $expectedSaleAmountMinor
    ): array {
        $costMinor = $purchaseObservation->amountInBaseCurrencyMinor();

        $platformFeeMinor  = (int) round($expectedSaleAmountMinor * $channel->platform_fee_basis_points / 10000);
        $paymentFeeMinor   = (int) round($expectedSaleAmountMinor * $channel->payment_fee_basis_points / 10000);
        $promotionFeeMinor = (int) round($expectedSaleAmountMinor * $channel->promotion_fee_basis_points / 10000);
        $returnLossMinor   = (int) round($expectedSaleAmountMinor * $channel->expected_return_loss_basis_points / 10000);
        $shippingMinor     = $channel->default_shipping_minor;
        $packagingMinor    = $channel->default_packaging_minor;

        $membershipRewardMinor = $this->rewardCalculator->totalReward($costMinor);

        $totalCostMinor = $costMinor
            + $platformFeeMinor
            + $paymentFeeMinor
            + $promotionFeeMinor
            + $returnLossMinor
            + $shippingMinor
            + $packagingMinor;

        $netProfitMinor = $expectedSaleAmountMinor
            - $totalCostMinor
            + $membershipRewardMinor;

        $adjustedCostMinor = $costMinor - $membershipRewardMinor;

        $roiBps = $adjustedCostMinor > 0
            ? (int) round($netProfitMinor / $adjustedCostMinor * 10000)
            : 0;

        $marginBps = $expectedSaleAmountMinor > 0
            ? (int) round($netProfitMinor / $expectedSaleAmountMinor * 10000)
            : 0;

        // break-even = cost / (1 - variable_fee_rate)
        $variableFeeRate = ($channel->platform_fee_basis_points
            + $channel->payment_fee_basis_points
            + $channel->promotion_fee_basis_points
            + $channel->expected_return_loss_basis_points) / 10000;

        $denominator = 1 - $variableFeeRate;
        $breakEvenMinor = $denominator > 0
            ? (int) ceil(($adjustedCostMinor + $shippingMinor + $packagingMinor) / $denominator)
            : 0;

        return [
            'cost_minor'                        => $costMinor,
            'expected_sale_amount_minor'        => $expectedSaleAmountMinor,
            'estimated_platform_fee_minor'      => $platformFeeMinor,
            'estimated_payment_fee_minor'       => $paymentFeeMinor,
            'estimated_promotion_fee_minor'     => $promotionFeeMinor,
            'estimated_shipping_minor'          => $shippingMinor,
            'estimated_packaging_minor'         => $packagingMinor,
            'estimated_return_loss_minor'       => $returnLossMinor,
            'estimated_other_cost_minor'        => 0,
            'estimated_membership_reward_minor' => $membershipRewardMinor,
            'estimated_net_profit_minor'        => $netProfitMinor,
            'roi_basis_points'                  => $roiBps,
            'profit_margin_basis_points'        => $marginBps,
            'break_even_amount_minor'           => $breakEvenMinor,
        ];
    }
}
