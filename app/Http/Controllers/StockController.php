<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with([
            'metrics' => function ($query) {
                $query->latest('date');
            }
        ])->get()->map(function ($stock) {
            $latestMetric = $stock->metrics->first();

            return [
                'id' => $stock->id,
                'symbol' => $stock->symbol,
                'company_name' => $stock->company_name,
                'sector' => $stock->sector,
                'price' => $latestMetric ? $latestMetric->price : 0,
                'change_percentage' => $latestMetric ? $latestMetric->change_percentage : 0,
                'bandar_volume' => $latestMetric ? $latestMetric->bandar_volume : 0,
                'target_price' => $latestMetric ? $latestMetric->target_price : 0,
                'target_action' => $latestMetric ? $latestMetric->target_action : '-',
                'mos' => $latestMetric ? $latestMetric->mos : 0,
                'last_updated' => $latestMetric ? $latestMetric->date->format('d M Y') : '-',
            ];
        });

        return Inertia::render('Stocks/Index', [
            'stocks' => $stocks,
        ]);
    }
}
