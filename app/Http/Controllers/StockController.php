<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index()
    {
        $metrics = \App\Models\StockMetric::with('stock')
            ->orderByDesc('date')
            ->get()
            ->map(function ($metric) {
                return [
                    'id' => $metric->id,
                    'symbol' => $metric->stock->symbol,
                    'company_name' => $metric->stock->company_name,
                    'price' => $metric->price,
                    'change_percentage' => $metric->change_percentage,
                    'target_price' => $metric->target_price,
                    'target_action' => $metric->target_action,
                    'mos' => $metric->mos,
                    'last_updated' => $metric->date->format('d M Y'),
                ];
            });

        return Inertia::render('Stocks/Index', [
            'metrics' => $metrics,
        ]);
    }
}
