<?php

namespace Database\Factories;

use App\Enums\CurrencyCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    public function definition()
    {
        $currency = $this->faker->randomElement(CurrencyCode::cases());

        return [
            'code' => $currency->value,
            'name' => $currency->name,
        ];
    }
}
