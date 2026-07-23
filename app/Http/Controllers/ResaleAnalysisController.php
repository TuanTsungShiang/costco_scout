<?php

namespace App\Http\Controllers;

use App\Enums\MarketDataStatus;
use App\Models\CanonicalProduct;
use App\Models\PriceObservation;
use App\Models\ResaleAnalysis;
use App\Models\SalesChannel;
use App\Services\ResaleDecisionService;
use App\Services\ResaleProfitCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResaleAnalysisController extends Controller
{
    public function __construct(
        private ResaleProfitCalculator $profitCalc,
        private ResaleDecisionService $decisionService,
    ) {}

    public function create(CanonicalProduct $product): View
    {
        $offers = $product->productOffers()
            ->with('retailer')
            ->where('is_active', true)
            ->get();

        // Build list of valid observations per offer
        $observationsByOffer = $offers->mapWithKeys(function ($offer) {
            return [$offer->id => $offer->priceObservations()
                ->where('status', 'VALID')
                ->latest('created_at')
                ->first()];
        });

        $channels     = SalesChannel::where('is_active', true)->get();
        $marketStates = MarketDataStatus::cases();

        return view('analyses.create', compact(
            'product', 'offers', 'observationsByOffer', 'channels', 'marketStates'
        ));
    }

    public function store(Request $request, CanonicalProduct $product): RedirectResponse
    {
        $validated = $request->validate([
            'price_observation_id'  => 'required|exists:price_observations,id',
            'sales_channel_id'      => 'required|exists:sales_channels,id',
            'expected_sale_amount'  => 'required|integer|min:1',
            'market_data_status'    => 'required|in:' . implode(',', array_column(MarketDataStatus::cases(), 'value')),
        ]);

        $observation = PriceObservation::findOrFail($validated['price_observation_id']);
        $channel     = SalesChannel::findOrFail($validated['sales_channel_id']);
        $saleMinor   = (int) $validated['expected_sale_amount'];
        $marketStatus = MarketDataStatus::from($validated['market_data_status']);

        $figures = $this->profitCalc->calculate($observation, $channel, $saleMinor);
        $decision = $this->decisionService->decide($figures['roi_basis_points'], $marketStatus);

        ResaleAnalysis::create([
            'canonical_product_id'            => $product->id,
            'purchase_price_observation_id'   => $observation->id,
            'sales_channel_id'                => $channel->id,
            'market_data_status'              => $marketStatus->value,
            'decision'                        => $decision->value,
            'analyzed_at'                     => now(),
            ...$figures,
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', '分析已完成：' . $decision->label());
    }

    public function show(ResaleAnalysis $analysis): View
    {
        $analysis->load([
            'canonicalProduct',
            'purchasePriceObservation.productOffer.retailer',
            'salesChannel',
            'experiments',
        ]);

        return view('analyses.show', compact('analysis'));
    }
}
