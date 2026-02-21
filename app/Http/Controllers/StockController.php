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
            'latestMetric' => function ($query) {
                $query->orderByDesc('date');
            },
        ])
            ->get()
            ->map(function ($stock) {
                $metric = $stock->latestMetric;

                $price = $metric ? $metric->price : 0;
                $change = $metric ? $metric->change : 0;
                $prevClose = $price - $change;

                return [
                    'id' => $stock->id,
                    'symbol' => $stock->symbol,
                    'company_name' => $stock->company_name,
                    'sector' => $stock->sector,
                    'price' => $price,
                    'prev_close' => $prevClose,
                    'change_percentage' => $metric ? $metric->change_percentage : null,
                    'target_r1' => $metric ? $metric->target_r1 : null,
                    'target_max' => $metric ? $metric->target_price : null,
                    'day_high' => $metric ? $metric->day_high : null,
                    'close' => $price,
                    'target_action' => $metric ? $metric->target_action : null,
                    'mos' => $metric ? $metric->mos : null,
                    'bandar_code' => $metric ? $metric->bandar_code : null,
                    'bandar_status' => $metric ? $metric->bandar_status : null,
                    'bandar_vol' => $metric ? $metric->bandar_volume : null,
                    'bandar_avg' => $metric ? $metric->bandar_avg_price : null,
                    'last_updated' => $metric ? $metric->date->format('d-M') : 'Pending Sync',
                ];
            });

        return Inertia::render('Stocks/Index', [
            'stocks' => $stocks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'symbol' => ['required', 'string', 'unique:stocks,symbol'],
        ]);

        Stock::create([
            'symbol' => strtoupper($request->symbol),
        ]);

        return redirect()->back()->with('success', 'Stock added successfully.');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->back()->with('success', 'Stock removed successfully.');
    }
}
