<?php

namespace App\Http\Controllers;

use App\Enums\CaptureStatus;
use App\Enums\ObservationSourceType;
use App\Enums\ObservationStatus;
use App\Models\CanonicalProduct;
use App\Models\PriceObservation;
use App\Models\ProductOffer;
use App\Models\Retailer;
use App\Models\Store;
use App\Services\PriceTagParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceInputController extends Controller
{
    public function index(): View
    {
        $retailers = Retailer::where('is_active', true)->orderBy('name')->get();
        $products  = CanonicalProduct::orderBy('brand')->orderBy('name')->get();

        return view('price-input.index', compact('retailers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_mode'          => 'required|in:ocr,costco,ec',
            'retailer_id'          => 'required|exists:retailers,id',
            'canonical_product_id' => 'required|exists:canonical_products,id',
            'amount_minor'         => 'required|integer|min:1',
            'currency_code'        => 'required|string|size:3',
            'notes'                => 'nullable|string|max:500',
        ]);

        $sourceMap = [
            'ocr'    => ObservationSourceType::PRICE_TAG_OCR,
            'costco' => ObservationSourceType::MANUAL,
            'ec'     => ObservationSourceType::MANUAL,
        ];

        // Find or create a ProductOffer linking this product to this retailer
        $product  = CanonicalProduct::findOrFail($validated['canonical_product_id']);
        $retailer = Retailer::findOrFail($validated['retailer_id']);

        $offer = ProductOffer::firstOrCreate(
            [
                'canonical_product_id' => $product->id,
                'retailer_id'          => $retailer->id,
            ],
            [
                'confirmed_at' => now(),
                'confirmed_by' => 'MANUAL',
                'is_active'    => true,
            ]
        );

        // Supersede previous VALID observation for this offer
        $previous = $offer->latestValidObservation();

        $observation = PriceObservation::create([
            'product_offer_id' => $offer->id,
            'amount_minor'     => $validated['amount_minor'],
            'currency_code'    => $validated['currency_code'],
            'source_type'      => $sourceMap[$validated['source_mode']]->value,
            'status'           => ObservationStatus::VALID->value,
            'notes'            => $validated['notes'] ?? null,
        ]);

        if ($previous) {
            $previous->status          = ObservationStatus::SUPERSEDED;
            $previous->superseded_by_id = $observation->id;
            $previous->save();
        }

        return redirect()->route('analyses.create', $product)
            ->with('success', '價格已記錄。選擇通路進行套利試算。');
    }
}
