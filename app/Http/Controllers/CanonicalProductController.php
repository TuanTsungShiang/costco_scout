<?php

namespace App\Http\Controllers;

use App\Enums\ComparisonMode;
use App\Models\CanonicalProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CanonicalProductController extends Controller
{
    public function index(): View
    {
        $products = CanonicalProduct::withCount('resaleAnalyses')
            ->orderBy('brand')
            ->orderBy('name')
            ->paginate(20);

        return view('products.index', compact('products'));
    }

    public function create(Request $request): View
    {
        // Pre-fill defaults from OCR-extracted query params (price-input flow)
        $defaults = [
            'brand'               => $request->query('brand', ''),
            'name'                => $request->query('name', ''),
            'notes'               => $request->query('notes', ''),
            'comparison_mode'     => $request->query('comparison_mode', ''),
            'package_count'       => $request->query('package_count', 1),
            'content_per_package' => $request->query('content_per_package', 1),
            'content_unit'        => $request->query('content_unit', ''),
            'comparison_quantity' => $request->query('comparison_quantity', 100),
            'comparison_unit'     => $request->query('comparison_unit', ''),
            'return_to'           => $request->query('return_to', ''),
        ];

        return view('products.create', [
            'modes'    => ComparisonMode::cases(),
            'defaults' => $defaults,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'brand'               => 'required|string|max:100',
            'name'                => 'required|string|max:200',
            'gtin'                => 'nullable|string|max:14|unique:canonical_products,gtin',
            'comparison_mode'     => 'required|in:' . implode(',', array_column(ComparisonMode::cases(), 'value')),
            'package_count'       => 'required|integer|min:1',
            'content_per_package' => 'required|integer|min:1',
            'content_unit'        => 'required|string|max:10',
            'comparison_quantity' => 'required|integer|min:1',
            'comparison_unit'     => 'required|string|max:10',
            'notes'               => 'nullable|string',
        ]);

        $product = CanonicalProduct::create($validated);

        // 從 price-input 過來的：建完直接跳回，並帶新商品 ID 讓下拉自動選
        if ($request->input('return_to') === 'price-input') {
            return redirect()->route('price-input', ['new_product_id' => $product->id])
                ->with('success', "「{$product->brand} {$product->name}」已建立，請繼續填入觀測價格。");
        }

        return redirect()->route('products.show', $product)
            ->with('success', '商品已建立');
    }

    public function show(CanonicalProduct $product): View
    {
        $product->load([
            'productOffers.retailer',
            'productOffers.priceObservations' => fn($q) => $q->where('status', 'VALID')->latest('created_at'),
            'resaleAnalyses' => fn($q) => $q->with(['salesChannel'])->orderByDesc('analyzed_at')->limit(5),
        ]);

        return view('products.show', compact('product'));
    }

    public function edit(CanonicalProduct $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'modes'   => ComparisonMode::cases(),
        ]);
    }

    public function update(Request $request, CanonicalProduct $product): RedirectResponse
    {
        $validated = $request->validate([
            'brand'               => 'required|string|max:100',
            'name'                => 'required|string|max:200',
            'gtin'                => 'nullable|string|max:14|unique:canonical_products,gtin,' . $product->id,
            'comparison_mode'     => 'required|in:' . implode(',', array_column(ComparisonMode::cases(), 'value')),
            'package_count'       => 'required|integer|min:1',
            'content_per_package' => 'required|integer|min:1',
            'content_unit'        => 'required|string|max:10',
            'comparison_quantity' => 'required|integer|min:1',
            'comparison_unit'     => 'required|string|max:10',
            'notes'               => 'nullable|string',
        ]);

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', '商品已更新');
    }
}
