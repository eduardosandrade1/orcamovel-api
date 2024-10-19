<?php

namespace Database\Factories\Api;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => \App\Models\Api\Vehicle::factory(),
            'client_id' => \App\Models\Api\Client::factory(),
            'total_price' => $this->faker->randomFloat(2, 1000, 5000),
            'notes' => $this->faker->sentence(),
        ];
    }
}
