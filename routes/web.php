<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\StockController::class, 'index'])->name('stocks.index');
Route::post('/stocks', [\App\Http\Controllers\StockController::class, 'store'])->name('stocks.store');
Route::get('/stocks/{stock:symbol}', [\App\Http\Controllers\StockController::class, 'show'])->name('stocks.show');
Route::delete('/stocks/{stock}', [\App\Http\Controllers\StockController::class, 'destroy'])->name('stocks.destroy');

// require __DIR__.'/settings.php';
