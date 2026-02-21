<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMetric extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'stock_id',
        'date',
        'price',
        'change',
        'change_percentage',
        'market_cap',
        'volume',
        'bandar_code',
        'bandar_avg_price',
        'bandar_volume',
        'total_bid_volume',
        'total_offer_volume',
        'offer_highest',
        'bid_lowest',
        'fraksi',
        'target_price',
        'target_action',
        'mos',
        'target_r1',
        'day_high',
        'bandar_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'price' => 'decimal:2',
            'change' => 'decimal:2',
            'change_percentage' => 'decimal:2',
            'bandar_avg_price' => 'decimal:2',
            'offer_highest' => 'decimal:2',
            'bid_lowest' => 'decimal:2',
            'target_price' => 'decimal:2',
            'mos' => 'decimal:2',
            'target_r1' => 'decimal:2',
            'day_high' => 'decimal:2',
        ];
    }

    /**
     * Get the stock that owns the metric.
     */
    public function stock(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
