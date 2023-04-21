<?php

namespace Database\Seeders;

use Database\Seeders\Enums\CountryCodeSeeder;
use Database\Seeders\Enums\CurrencyCodeSeeder;
use Illuminate\Database\Seeder;

class DefaultSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CurrencyCodeSeeder::class);
        $this->call(CountryCodeSeeder::class);
    }
}
