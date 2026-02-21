<?php

namespace App\Services;

class AdimologyService
{
    /**
     * Calculate Fraksi based on price.
     */
    public function getFraksi(float $price): int
    {
        if ($price < 200) {
            return 1;
        }
        if ($price >= 200 && $price < 500) {
            return 2;
        }
        if ($price >= 500 && $price < 2000) {
            return 5;
        }
        if ($price >= 2000 && $price < 5000) {
            return 10;
        }

        return 25; // >= 5000
    }

    /**
     * Calculate Adimology Targets.
     */
    public function calculate(array $marketData, array $brokerData)
    {
        $harga = $marketData['price'];
        $fraksi = $this->getFraksi($harga);

        $ara = $marketData['offer_highest']; // Proxy for ARA
        $arb = $marketData['bid_lowest'];   // Proxy for ARB

        // Safety check for division by zero if fraksi is 0 (unlikely)
        if ($fraksi == 0) {
            $fraksi = 1;
        }

        // Total Papan = (ARA - ARB) / Fraksi
        $totalPapan = ($ara - $arb) / $fraksi;
        if ($totalPapan == 0) {
            $totalPapan = 1;
        } // Avoid division by zero

        // Rata rata Bid Ofer = (Total Bid + Total Offer) / Total Papan
        $totalBidInfo = $marketData['total_bid_volume']; // In Lots
        $totalOfferInfo = $marketData['total_offer_volume']; // In Lots

        // If data is from orderbook, they are usually in Lots.
        // Adimology TS code divides by 100? No, TS code: `marketData.totalBid / 100`.
        // Wait, let's check TS code again.
        // `marketData.totalBid: parseLot(obData.total_bid_offer.bid.lot)` -> parseLot removes commas and returns Number.
        // `calculateTargets` call: `marketData.totalBid / 100`.
        // So the input to `calculateTargets` expects 1/100th of lots? Or maybe it expects "Total Value" proxy?
        // Let's stick to the formula: `(TotalBid + TotalOffer) / TotalPapan`.
        // If TS code divides input by 100, we should check why.
        // Assuming "Lot" to "Shares" conversion is * 100.
        // But TS code divides by 100. Maybe it normalizes?
        // Let's implement exactly as TS:

        // Input from TS: `marketData.totalBid / 100` passed as `totalBid` argument.
        $totalBidInput = $totalBidInfo / 100;
        $totalOfferInput = $totalOfferInfo / 100;

        $rataRataBidOfer = ($totalBidInput + $totalOfferInput) / $totalPapan;
        if ($rataRataBidOfer == 0) {
            $rataRataBidOfer = 1;
        }

        $bandarAvgPrice = $brokerData['avg_price'];
        $bandarVolume = $brokerData['volume']; // In Lots usually?

        // TS Code: `barangBandar: Math.round(Number(topBroker.blot))` -> "blot" implies Broker Lot?
        // TS Code Pass: `brokerData.barangBandar`.
        // Formula: `p = Barang Bandar / Rata rata Bid Ofer`.
        // Since `Rata rata Bid Ofer` used /100 inputs, maybe `Barang Bandar` should also be normalized?
        // TS code does NOT divide `barangBandar` by 100.

        // a = Rata rata bandar × 5%
        $a = $bandarAvgPrice * 0.05;

        // p = Barang Bandar / Rata rata Bid Ofer
        $p = $bandarVolume / $rataRataBidOfer;

        // Target Realistis = Rata rata bandar + a + (p/2 × Fraksi)
        $targetRealistis = $bandarAvgPrice + $a + (($p / 2) * $fraksi);

        // Target Max = Rata rata bandar + a + (p × Fraksi)
        $targetMax = $bandarAvgPrice + $a + ($p * $fraksi);

        // MOS (Margin of Safety) - Not in TS but requested.
        // MOS = ((Target Price - Current Price) / Current Price) * 100%
        $mos = 0;
        if ($harga > 0) {
            $mos = (($targetMax - $harga) / $harga) * 100;
        }

        return [
            'fraksi' => $fraksi,
            'total_papan' => round($totalPapan),
            'rata_rata_bid_offer' => round($rataRataBidOfer),
            'a' => round($a),
            'p' => round($p),
            'target_r1' => round($targetRealistis),
            'target_realistis' => round($targetRealistis),
            'target_price' => round($targetMax), // Using Target Max as main target
            'mos' => round($mos, 2),
            'action' => $mos > 0 ? 'Accumulate' : 'Wait', // Simple logic
        ];
    }
}
