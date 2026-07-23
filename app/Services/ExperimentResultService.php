<?php

namespace App\Services;

use App\Enums\ExperimentStatus;
use App\Models\ResaleExperiment;

class ExperimentResultService
{
    public function recordSale(ResaleExperiment $experiment, array $data): ResaleExperiment
    {
        $experiment->fill([
            'quantity_sold'                    => $data['quantity_sold'],
            'actual_average_sale_amount_minor' => $data['actual_average_sale_amount_minor'],
            'actual_platform_fee_minor'        => $data['actual_platform_fee_minor'] ?? null,
            'actual_payment_fee_minor'         => $data['actual_payment_fee_minor'] ?? null,
            'actual_shipping_minor'            => $data['actual_shipping_minor'] ?? null,
            'actual_packaging_minor'           => $data['actual_packaging_minor'] ?? null,
            'actual_other_cost_minor'          => $data['actual_other_cost_minor'] ?? null,
        ]);

        $experiment->actual_net_profit_minor = $this->computeNetProfit($experiment);

        $quantitySold = $data['quantity_sold'];
        if ($quantitySold >= $experiment->quantity_listed) {
            $experiment->status = ExperimentStatus::SOLD_OUT;
            $experiment->completed_at = now();
        } elseif ($quantitySold > 0) {
            $experiment->status = ExperimentStatus::PARTIALLY_SOLD;
        }

        if ($quantitySold > 0 && $experiment->first_sold_at === null) {
            $experiment->first_sold_at = now();
        }

        $experiment->save();
        return $experiment;
    }

    private function computeNetProfit(ResaleExperiment $experiment): ?int
    {
        if ($experiment->actual_average_sale_amount_minor === null
            || $experiment->purchase_total_minor === null) {
            return null;
        }

        $revenue = $experiment->actual_average_sale_amount_minor * $experiment->quantity_sold;

        $costs = ($experiment->purchase_total_minor ?? 0)
            + ($experiment->actual_platform_fee_minor ?? 0)
            + ($experiment->actual_payment_fee_minor ?? 0)
            + ($experiment->actual_shipping_minor ?? 0)
            + ($experiment->actual_packaging_minor ?? 0)
            + ($experiment->actual_other_cost_minor ?? 0);

        return $revenue - $costs;
    }
}
