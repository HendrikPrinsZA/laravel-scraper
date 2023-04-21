<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRate>
 */
class BicycleFactory extends Factory
{
    public function definition()
    {
        return [
            'object_number' => $this->faker->uniqid('object-'),
            'type' => $this->faker->randomElement([
                'man',
                'woman',
                'kid',
                'baby',
            ]),
            'sub_type' => $this->faker->randomElement([
                '2-wheels',
                '3-wheels',
                'unicycle',
            ]),
            'brand' => $this->faker->randomElement([
                'Gazelle',
                'Pashley',
            ]),
            'color' => $this->faker->colorName(),
            'description' => $this->faker->text(),
            'city' => $this->faker->city(),
            'storage_location' => $this->faker->city(),
            'registered_at' => $this->faker->date(),
        ];
    }
}
