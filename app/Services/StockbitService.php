<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StockbitService
{
    protected string $baseUrl = 'https://exodus.stockbit.com';

    protected string $authUrl = 'https://stockbit.com';

    protected ?string $token;

    public function __construct(?string $token = null)
    {
        $this->token = $token ?: (\Illuminate\Support\Facades\Storage::exists('stockbit_token') ? \Illuminate\Support\Facades\Storage::get('stockbit_token') : null);
    }

    /**
     * Get headers for requests.
     */
    protected function getHeaders(): array
    {
        if (! $this->token) {
            throw new \Exception('Stockbit token is required.');
        }

        return [
            'accept' => 'application/json',
            'authorization' => 'Bearer '.$this->token,
            'origin' => 'https://stockbit.com',
            'referer' => 'https://stockbit.com/',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];
    }

    /**
     * Fetch Market Detector (Broker Summary).
     */
    public function getMarketDetector(string $symbol, string $fromDate, string $toDate)
    {
        $url = "{$this->baseUrl}/marketdetectors/{$symbol}";

        $response = Http::withHeaders($this->getHeaders())
            ->get($url, [
                'from' => $fromDate,
                'to' => $toDate,
                'transaction_type' => 'TRANSACTION_TYPE_NET',
                'market_board' => 'MARKET_BOARD_REGULER',
                'investor_type' => 'INVESTOR_TYPE_ALL',
                'limit' => 25,
            ]);

        if ($response->failed()) {
            Log::error("Stockbit Market Detector Failed: {$response->body()}");
            throw new \Exception('Failed to fetch market detector data.');
        }

        return $response->json();
    }

    /**
     * Fetch Orderbook (Real-time Price & Bid/Offer).
     */
    public function getOrderbook(string $symbol)
    {
        $url = "{$this->baseUrl}/company-price-feed/v2/orderbook/companies/{$symbol}";

        $response = Http::withHeaders($this->getHeaders())->get($url);

        if ($response->failed()) {
            Log::error("Stockbit Orderbook Failed: {$response->body()}");
            throw new \Exception('Failed to fetch orderbook data.');
        }

        return $response->json();
    }

    /**
     * Fetch Emiten Info (Sector, etc).
     */
    public function getEmitenInfo(string $symbol)
    {
        $url = "{$this->baseUrl}/emitten/{$symbol}/info";

        $response = Http::withHeaders($this->getHeaders())->get($url);

        if ($response->failed()) {
            return null; // Info might be missing, non-critical
        }

        return $response->json();
    }
}
