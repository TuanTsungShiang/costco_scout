<?php

namespace App\Services;

use App\Enums\MarketDataStatus;
use App\Enums\ResaleDecision;

class ResaleDecisionService
{
    public function __construct(private array $config) {}

    /**
     * Determine the resale decision based on ROI and data confidence.
     *
     * Rules:
     *   LOW confidence (UNVERIFIED)        → max TEST_ONE_UNIT even if ROI passes
     *   No real sales data (not OWN_SALES_HISTORY) → max TEST_ONE_UNIT
     *   ROI < min threshold                 → PASS
     *   ROI >= threshold, low confidence    → WATCH
     *   ROI >= threshold, has market check  → TEST_ONE_UNIT
     *   ROI >= threshold, own sales data    → RESTOCK / SCALE
     */
    public function decide(int $roiBasisPoints, MarketDataStatus $marketDataStatus): ResaleDecision
    {
        $minRoiBps = $this->config['min_roi_basis_points'] ?? 800;

        if ($roiBasisPoints < 0) {
            return ResaleDecision::PASS;
        }

        if ($marketDataStatus === MarketDataStatus::UNVERIFIED) {
            return $roiBasisPoints >= $minRoiBps
                ? ResaleDecision::WATCH
                : ResaleDecision::PASS;
        }

        if ($roiBasisPoints < $minRoiBps) {
            return ResaleDecision::PASS;
        }

        if (! $marketDataStatus->hasSalesData()) {
            return ResaleDecision::TEST_ONE_UNIT;
        }

        // OWN_SALES_HISTORY — scale threshold is 2× min ROI
        return $roiBasisPoints >= $minRoiBps * 2
            ? ResaleDecision::SCALE
            : ResaleDecision::RESTOCK;
    }
}
