<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockMetric;
use App\Services\AdimologyService;
use App\Services\StockbitService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncStockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:sync {symbol?} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync stock data from Stockbit and calculate Adimology metrics.';

    /**
     * Execute the console command.
     */
    public function handle(AdimologyService $adimologyService)
    {
        $symbol = $this->argument('symbol');
        $today = $this->argument('date') ?? Carbon::today()->subDay()->format('Y-m-d');

        // 1. Initialize Service (will auto-load token from Storage)
        try {
            $stockbit = new StockbitService;
        } catch (\Exception $e) {
            $this->error($e->getMessage()); // Token is required

            return 1;
        }

        if ($symbol) {
            $this->syncSymbol(strtoupper($symbol), $today, $stockbit, $adimologyService);
        } else {
            $stocks = Stock::whereDoesntHave('metrics', function ($query) use ($today) {
                $query->where('date', $today);
            })->get();

            if ($stocks->isEmpty()) {
                $this->info("All stocks have been synced for {$today}.");

                return 0;
            }

            $this->info('Syncing ' . $stocks->count() . ' stocks for ' . $today . '...');

            foreach ($stocks as $stock) {
                $this->syncSymbol($stock->symbol, $today, $stockbit, $adimologyService);
                sleep(1); // Prevent rate limiting
            }
        }

        return 0;
    }

    private function syncSymbol($symbol, $today, StockbitService $stockbit, AdimologyService $adimologyService)
    {
        // Check if already synced today
        $stock = Stock::where('symbol', $symbol)->first();
        if ($stock && StockMetric::where('stock_id', $stock->id)->where('date', $today)->exists()) {
            $this->info("Data for {$symbol} on {$today} already exists. Skipping...");

            return;
        }

        $this->info("Syncing data for {$symbol}...");

        try {
            // 2. Fetch Data
            $this->info('Fetching Market Detector...');
            $marketDetector = $stockbit->getMarketDetector($symbol, $today, $today);

            $this->info('Fetching Orderbook...');
            $orderbook = $stockbit->getOrderbook($symbol);

            $this->info('Fetching Emiten Info...');
            $emitenInfo = $stockbit->getEmitenInfo($symbol);

            // logger('marketDetector', $marketDetector);
            // logger('orderbook', $orderbook);
            // logger('emitenInfo', $emitenInfo);

            // 4. Process Data for Adimology
            // Extract Broker Data
            $conclusion = $marketDetector['data']['broker_summary']['conclusion'] ?? [];
            $bandarStatus = $conclusion['status'] ?? null;

            $brokersBuy = $marketDetector['data']['broker_summary']['brokers_buy'] ?? [];
            if (empty($brokersBuy)) {
                $this->warn("No broker data found for {$symbol} (Market might be closed or no transaction). Skipping...");

                return;
            }

            // Sort by BVal (Value) Descending
            usort($brokersBuy, function ($a, $b) {
                return $b['bval'] - $a['bval'];
            });
            $topBroker = $brokersBuy[0];

            // Extract Market Data
            $obData = $orderbook['data'];
            $price = (float) $obData['close'];
            $dayHigh = isset($obData['high']) ? (float) $obData['high'] : $price;
            $change = (float) $obData['change'];
            $changePercentage = (float) @$obData['percentage_change']; // e.g. "1.25"
            $marketCap = (int) ($obData['market_cap'] ?? 0);
            $volume = (int) ($obData['volume'] ?? 0);

            $totalBid = (int) str_replace(',', '', $obData['total_bid_offer']['bid']['lot'] ?? '0');
            $totalOffer = (int) str_replace(',', '', $obData['total_bid_offer']['offer']['lot']);

            // ARA/ARB Proxy
            $offers = $obData['offer'] ?? [];
            $bids = $obData['bid'] ?? [];

            $highestOffer = !empty($offers) ? max(array_column($offers, 'price')) : $price;
            $lowestBid = !empty($bids) ? min(array_column($bids, 'price')) : $price;

            // Prepare for Service
            $marketDataInput = [
                'price' => $price,
                'offer_highest' => (float) $highestOffer,
                'bid_lowest' => (float) $lowestBid,
                'total_bid_volume' => $totalBid,
                'total_offer_volume' => $totalOffer,
            ];

            $brokerDataInput = [
                'avg_price' => $topBroker ? (float) $topBroker['netbs_buy_avg_price'] : 0,
                'volume' => $topBroker ? (float) $topBroker['blot'] : 0, // 'blot' is Broker Lot?
            ];

            // 5. Calculate Adimology
            $results = $adimologyService->calculate($marketDataInput, $brokerDataInput);
            // logger('results', $results);

            // 6. Save to Database
            $isFca = false;
            if (isset($emitenInfo['data']['indexes']) && is_array($emitenInfo['data']['indexes'])) {
                $isFca = in_array('Pemantauan-Khusus', $emitenInfo['data']['indexes']);
            }

            $stock = Stock::updateOrCreate(
                ['symbol' => $symbol],
                [
                    'company_name' => $emitenInfo['data']['name'] ?? $symbol,
                    'sector' => $emitenInfo['data']['sector'] ?? null,
                    'is_fca' => $isFca,
                ]
            );

            // Create Metric
            $metric = StockMetric::updateOrCreate(
                [
                    'stock_id' => $stock->id,
                    'date' => $today,
                ],
                [
                    'price' => $price,
                    'change' => $change,
                    'change_percentage' => $changePercentage,
                    'market_cap' => $marketCap,
                    'volume' => $volume,
                    'bandar_code' => $topBroker ? $topBroker['netbs_broker_code'] : null,
                    'bandar_status' => $bandarStatus,
                    'bandar_avg_price' => $brokerDataInput['avg_price'],
                    'bandar_volume' => $brokerDataInput['volume'],
                    'total_bid_volume' => $totalBid,
                    'total_offer_volume' => $totalOffer,
                    'offer_highest' => $marketDataInput['offer_highest'],
                    'bid_lowest' => $marketDataInput['bid_lowest'],
                    'day_high' => $dayHigh,
                    'fraksi' => $results['fraksi'],
                    'target_r1' => $results['target_r1'],
                    'target_price' => $results['target_price'],
                    'target_action' => $results['action'],
                    'mos' => $results['mos'],
                ]
            );

            $this->info("Successfully synced {$symbol}! Target Price: {$results['target_price']} (MOS: {$results['mos']}%)");

        } catch (\Exception $e) {
            $this->error("Error syncing {$symbol}: " . $e->getMessage());
        }
    }
}
