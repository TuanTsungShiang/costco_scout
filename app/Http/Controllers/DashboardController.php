<?php

namespace App\Http\Controllers;

use App\Models\CanonicalProduct;
use App\Models\ResaleAnalysis;
use App\Models\ResaleExperiment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $latestAnalyses = ResaleAnalysis::with(['canonicalProduct', 'salesChannel', 'purchasePriceObservation'])
            ->orderByDesc('analyzed_at')
            ->limit(10)
            ->get();

        $stats = [
            'total_products'    => CanonicalProduct::count(),
            'total_analyses'    => ResaleAnalysis::count(),
            'active_experiments'=> ResaleExperiment::whereIn('status', ['PLANNED', 'LISTED', 'PARTIALLY_SOLD'])->count(),
            'profitable_count'  => ResaleAnalysis::where('estimated_net_profit_minor', '>', 0)->count(),
        ];

        return view('dashboard.index', compact('latestAnalyses', 'stats'));
    }
}
