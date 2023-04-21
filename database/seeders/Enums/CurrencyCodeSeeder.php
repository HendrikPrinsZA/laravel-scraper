<?php

namespace Database\Seeders\Enums;

use App\Enums\CurrencyCode;
use App\Models\Currency;
use App\Services\ExchangeRateService;

class CurrencyCodeSeeder extends EnumSeeder
{
    public function __construct(
        protected ExchangeRateService $exchangeRateService
    ) {
    }

    public function seed(): void
    {
        $currencies = $this->exchangeRateService->getCurrencies();
        $currencies->upsert();
    }

    protected function cleanup(): void
    {
        Currency::whereNotIn('code', CurrencyCode::cases())->delete();
    }
}
