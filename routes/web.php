<?php

use App\Http\Controllers\CanonicalProductController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\PriceScrapeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PriceInputController;
use App\Http\Controllers\PriceObservationController;
use App\Http\Controllers\ResaleAnalysisController;
use App\Http\Controllers\ResaleExperimentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::post('/api/ocr/recognize', [OcrController::class, 'recognize'])->name('ocr.recognize');
Route::post('/api/price/scrape', [PriceScrapeController::class, 'scrape'])->name('price.scrape');

Route::get('/price-input', [PriceInputController::class, 'index'])->name('price-input');
Route::post('/price-input', [PriceInputController::class, 'store'])->name('price-input.store');

Route::resource('products', CanonicalProductController::class)->except(['destroy']);

Route::get('offers/{offer}/observations/create', [PriceObservationController::class, 'create'])
    ->name('observations.create');
Route::post('offers/{offer}/observations', [PriceObservationController::class, 'store'])
    ->name('observations.store');
Route::patch('observations/{observation}/invalidate', [PriceObservationController::class, 'invalidate'])
    ->name('observations.invalidate');

Route::get('products/{product}/analyses/create', [ResaleAnalysisController::class, 'create'])
    ->name('analyses.create');
Route::post('products/{product}/analyses', [ResaleAnalysisController::class, 'store'])
    ->name('analyses.store');
Route::get('analyses/{analysis}', [ResaleAnalysisController::class, 'show'])
    ->name('analyses.show');

Route::get('analyses/{analysis}/experiments/create', [ResaleExperimentController::class, 'create'])
    ->name('experiments.create');
Route::post('analyses/{analysis}/experiments', [ResaleExperimentController::class, 'store'])
    ->name('experiments.store');
Route::get('experiments', [ResaleExperimentController::class, 'index'])
    ->name('experiments.index');
Route::get('experiments/{experiment}/edit', [ResaleExperimentController::class, 'edit'])
    ->name('experiments.edit');
Route::patch('experiments/{experiment}', [ResaleExperimentController::class, 'update'])
    ->name('experiments.update');
