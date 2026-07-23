<?php

namespace App\Http\Controllers;

use App\Enums\ExperimentStatus;
use App\Models\ResaleAnalysis;
use App\Models\ResaleExperiment;
use App\Services\ExperimentResultService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResaleExperimentController extends Controller
{
    public function __construct(private ExperimentResultService $resultService) {}

    public function create(ResaleAnalysis $analysis): View
    {
        $analysis->load(['canonicalProduct', 'salesChannel']);
        return view('experiments.create', compact('analysis'));
    }

    public function store(Request $request, ResaleAnalysis $analysis): RedirectResponse
    {
        $validated = $request->validate([
            'quantity_purchased'   => 'required|integer|min:1',
            'quantity_listed'      => 'required|integer|min:1',
            'purchase_total_minor' => 'required|integer|min:1',
            'notes'                => 'nullable|string',
        ]);

        ResaleExperiment::create([
            'resale_analysis_id'   => $analysis->id,
            'quantity_purchased'   => $validated['quantity_purchased'],
            'quantity_listed'      => $validated['quantity_listed'],
            'quantity_sold'        => 0,
            'purchase_total_minor' => $validated['purchase_total_minor'],
            'status'               => ExperimentStatus::PLANNED->value,
            'notes'                => $validated['notes'] ?? null,
        ]);

        return redirect()->route('analyses.show', $analysis)
            ->with('success', '實驗已建立，準備上架');
    }

    public function edit(ResaleExperiment $experiment): View
    {
        $experiment->load(['resaleAnalysis.canonicalProduct', 'resaleAnalysis.salesChannel']);
        return view('experiments.edit', compact('experiment'));
    }

    public function update(Request $request, ResaleExperiment $experiment): RedirectResponse
    {
        $validated = $request->validate([
            'quantity_sold'                    => 'required|integer|min:0',
            'actual_average_sale_amount_minor' => 'nullable|integer|min:0',
            'actual_platform_fee_minor'        => 'nullable|integer',
            'actual_payment_fee_minor'         => 'nullable|integer',
            'actual_shipping_minor'            => 'nullable|integer',
            'actual_packaging_minor'           => 'nullable|integer',
            'actual_other_cost_minor'          => 'nullable|integer',
            'status'                           => 'required|in:' . implode(',', array_column(ExperimentStatus::cases(), 'value')),
            'notes'                            => 'nullable|string',
        ]);

        if ($validated['status'] === ExperimentStatus::LISTED->value
            && $experiment->listed_at === null) {
            $experiment->listed_at = now();
        }

        $this->resultService->recordSale($experiment, $validated);
        $experiment->notes = $validated['notes'] ?? $experiment->notes;
        $experiment->save();

        return redirect()->route('analyses.show', $experiment->resale_analysis_id)
            ->with('success', '實驗紀錄已更新');
    }

    public function index(): View
    {
        $experiments = ResaleExperiment::with([
            'resaleAnalysis.canonicalProduct',
            'resaleAnalysis.salesChannel',
        ])->orderByDesc('created_at')->paginate(20);

        return view('experiments.index', compact('experiments'));
    }
}
