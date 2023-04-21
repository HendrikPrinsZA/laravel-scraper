<?php

namespace Database\Seeders\Environments;

use App\Models\User;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    public function run(): void
    {
        if (User::firstWhere('email', 'test@example.com')) {
            return;
        }

        User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }
}
