<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', [\App\Http\Controllers\StockController::class, 'index'])->name('stocks.index');

// require __DIR__.'/settings.php';
