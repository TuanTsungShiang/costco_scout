<?php

namespace App\Http\Controllers;

use App\Enums\ObservationSourceType;
use App\Enums\ObservationStatus;
use App\Models\PriceObservation;
use App\Models\ProductOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceObservationController extends Controller
{
    public function create(ProductOffer $offer): View
    {
        $offer->load(['canonicalProduct', 'retailer']);
        return view('observations.create', compact('offer'));
    }

    public function store(Request $request, ProductOffer $offer): RedirectResponse
    {
        $validated = $request->validate([
            'amount_minor'    => 'required|integer|min:1',
            'currency_code'   => 'required|string|size:3',
            'fx_rate_to_base' => 'nullable|numeric|min:0',
            'source_type'     => 'required|in:' . implode(',', array_column(ObservationSourceType::cases(), 'value')),
            'notes'           => 'nullable|string',
        ]);

        // Supersede the previous VALID observation for this offer
        $previous = $offer->latestValidObservation();

        $observation = PriceObservation::create([
            'product_offer_id' => $offer->id,
            'amount_minor'     => $validated['amount_minor'],
            'currency_code'    => $validated['currency_code'],
            'fx_rate_to_base'  => $validated['fx_rate_to_base'] ?? null,
            'source_type'      => $validated['source_type'],
            'status'           => ObservationStatus::VALID->value,
            'notes'            => $validated['notes'] ?? null,
        ]);

        if ($previous) {
            $previous->status         = ObservationStatus::SUPERSEDED;
            $previous->superseded_by_id = $observation->id;
            $previous->save();
        }

        return redirect()->route('products.show', $offer->canonical_product_id)
            ->with('success', '價格已記錄');
    }

    public function invalidate(PriceObservation $observation): RedirectResponse
    {
        $observation->status         = ObservationStatus::INVALIDATED;
        $observation->invalidated_at = now();
        $observation->save();

        return back()->with('success', '價格已作廢');
    }
}
