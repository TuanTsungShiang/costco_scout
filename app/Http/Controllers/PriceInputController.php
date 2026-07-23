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
            'source_url'           => 'nullable|url|max:1000',
            'notes'                => 'nullable|string|max:500',
        ]);

        $sourceMap = [
            'ocr'    => ObservationSourceType::PRICE_TAG_OCR,
            'costco' => ObservationSourceType::MANUAL,
            'ec'     => ObservationSourceType::MANUAL,
        ];

        $product  = CanonicalProduct::findOrFail($validated['canonical_product_id']);
        $retailer = Retailer::findOrFail($validated['retailer_id']);

        // 依模式解析 store：賣場(ocr)=實體店、線上(costco)=線上商店、電商(ec)=無 store。
        // store_id 是「賣場 vs 線上 兩筆獨立觀測」的分界，避免線上價覆蓋賣場價。
        $storeId = $this->resolveStoreId($retailer, $validated['source_mode']);

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

        // 線上模式：把商品網址記到 offer（同一 offer 只留最新網址）
        if ($validated['source_mode'] !== 'ocr' && !empty($validated['source_url'])) {
            $offer->external_url = $validated['source_url'];
            $offer->save();
        }

        // 只 supersede「同一 store」的前一筆 VALID 觀測（賣場/線上互不干擾）
        $previous = $offer->latestValidObservation($storeId);

        $observation = PriceObservation::create([
            'product_offer_id' => $offer->id,
            'store_id'         => $storeId,
            'amount_minor'     => $validated['amount_minor'],
            'currency_code'    => $validated['currency_code'],
            'observed_at'      => now()->toDateString(),
            'source_type'      => $sourceMap[$validated['source_mode']]->value,
            'status'           => ObservationStatus::VALID->value,
            'notes'            => $validated['notes'] ?? null,
        ]);

        if ($previous) {
            $previous->status           = ObservationStatus::SUPERSEDED;
            $previous->superseded_by_id = $observation->id;
            $previous->save();
        }

        return redirect()->route('analyses.create', $product)
            ->with('success', '價格已記錄。選擇通路進行套利試算。');
    }

    /**
     * 依價格來源模式，找出對應的 store_id。
     * - costco（線上）：該通路 branch_name 含「線上」的 store
     * - ocr（賣場）：該通路的實體 store（branch_name 不含「線上」）
     * - ec（電商）：無 store（回 null）
     */
    private function resolveStoreId(Retailer $retailer, string $mode): ?int
    {
        if ($mode === 'ec') {
            return null;
        }

        $stores = Store::where('retailer_id', $retailer->id)
            ->where('is_active', true)
            ->get();

        if ($stores->isEmpty()) {
            return null;
        }

        if ($mode === 'costco') {
            $online = $stores->first(fn ($s) => str_contains($s->branch_name, '線上'));
            return $online?->id ?? $stores->first()->id;
        }

        // mode === 'ocr'（賣場）：優先實體店
        $physical = $stores->first(fn ($s) => !str_contains($s->branch_name, '線上'));
        return $physical?->id ?? $stores->first()->id;
    }
}
