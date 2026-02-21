<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'symbol',
        'company_name',
        'sector',
        'is_fca',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_fca' => 'boolean',
        ];
    }

    /**
     * Get the metrics for the stock.
     */
    public function metrics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockMetric::class);
    }

    public function latestMetric()
    {
        return $this->hasOne(StockMetric::class)->latestOfMany();
    }
}
