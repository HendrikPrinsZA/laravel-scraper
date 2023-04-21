<?php

namespace Database\Seeders\Enums;

use Illuminate\Database\Seeder;

abstract class EnumSeeder extends Seeder
{
    public function run()
    {
        $this->seed();
        $this->cleanup();
    }

    abstract public function seed(): void;

    protected function cleanup(): void
    {
    }
}
