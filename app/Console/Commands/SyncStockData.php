<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockMetric;
use App\Models\User;
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
    protected $signature = 'stock:sync {symbol}';

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
        $symbol = strtoupper($this->argument('symbol'));
        $this->info("Syncing data for {$symbol}...");

        // 1. Initialize Service (will auto-load token from Storage)
        try {
            $stockbit = new StockbitService();
        } catch (\Exception $e) {
            $this->error($e->getMessage()); // Token is required
            return 1;
        }


        try {
            // 2. Fetch Data

            // 3. Fetch Data
            $today = Carbon::today()->subDays(5)->format('Y-m-d');
            $fromDate = Carbon::today()->subMonths(5)->format('Y-m-d'); // Fetch 3 months data for broker summary if needed? 
            // Adimology uses *current* day for broker summary usually, or a specific range.
            // Let's use today for range to get today's broker summary or last trading day.
            // If market is closed, it might return empty.
            // For now let's use today.

            $this->info('Fetching Market Detector...');
            $marketDetector = $stockbit->getMarketDetector($symbol, $today, $today);

            $this->info('Fetching Orderbook...');
            $orderbook = $stockbit->getOrderbook($symbol);


            $this->info('Fetching Emiten Info...');
            $emitenInfo = $stockbit->getEmitenInfo($symbol);

            // 4. Process Data for Adimology
            // Extract Broker Data
            $brokersBuy = $marketDetector['data']['broker_summary']['brokers_buy'] ?? [];
            if (empty($brokersBuy)) {
                $this->warn('No broker data found (Market might be closed or no transaction). Using 0 values.');
                $topBroker = null;
            } else {
                // Sort by BVal (Value) Descending
                usort($brokersBuy, function ($a, $b) {
                    return $b['bval'] - $a['bval'];
                });
                $topBroker = $brokersBuy[0];
            }

            // Extract Market Data
            $obData = $orderbook['data'];
            $price = (float) $obData['close'];
            $change = (float) $obData['change'];
            $changePercentage = (float) @$obData['change_percentage']; // e.g. "1.25"
            $marketCap = (int) ($obData['market_cap'] ?? 0);
            $volume = (int) ($obData['volume'] ?? 0);

            $totalBid = (int) str_replace(',', '', $obData['total_bid_offer']['bid']['lot']);
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

            // 6. Save to Database
            $stock = Stock::updateOrCreate(
                ['symbol' => $symbol],
                [
                    'company_name' => $emitenInfo['data']['name'] ?? $symbol,
                    'sector' => $emitenInfo['data']['sector'] ?? null,
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
                    'bandar_avg_price' => $brokerDataInput['avg_price'],
                    'bandar_volume' => $brokerDataInput['volume'],
                    'total_bid_volume' => $totalBid,
                    'total_offer_volume' => $totalOffer,
                    'offer_highest' => $marketDataInput['offer_highest'],
                    'bid_lowest' => $marketDataInput['bid_lowest'],
                    'fraksi' => $results['fraksi'],
                    'target_price' => $results['target_price'],
                    'target_action' => $results['action'],
                    'mos' => $results['mos'],
                ]
            );

            $this->info("Successfully synced {$symbol}! Target Price: {$results['target_price']} (MOS: {$results['mos']}%)");

        } catch (\Exception $e) {
            $this->error("Error syncing {$symbol}: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
