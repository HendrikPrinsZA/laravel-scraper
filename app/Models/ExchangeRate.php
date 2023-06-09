<?php

namespace App\Models;

use App\Traits\HasCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    use HasFactory, HasCollection;

    protected $fillable = [
        'base_currency_id',
        'target_currency_id',
        'date',
        'rate',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'rate' => 'float',
    ];

    public array $collection_unique_attributes = [
        'base_currency_id',
        'target_currency_id',
        'date',
    ];

    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function targetCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
