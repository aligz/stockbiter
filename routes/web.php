<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


Route::get('/stocks', [\App\Http\Controllers\StockController::class, 'index'])->name('stocks.index');

// require __DIR__.'/settings.php';
