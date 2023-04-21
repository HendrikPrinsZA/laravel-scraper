<?php

namespace Database\Seeders;

use Database\Seeders\Environments\LocalSeeder;
use Database\Seeders\Environments\ProductionSeeder;
use Database\Seeders\Environments\TestingSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DefaultSeeder::class);

        switch (app()->environment()) {
            case 'local':
                $this->call(LocalSeeder::class);
                break;
            case 'testing':
                $this->call(TestingSeeder::class);
                break;
            case 'staging':
            case 'production':
                $this->call(ProductionSeeder::class);
                break;
        }
    }
}
