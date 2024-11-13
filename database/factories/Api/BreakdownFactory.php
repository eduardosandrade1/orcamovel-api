<?php

namespace Database\Factories\Api;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BreakdownFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Api\Order::factory(),
            'vehicle_part_id' => \App\Models\Api\VehicleParts::factory(),
            'breakdown_type' => $this->faker->randomElement(['Quebrado', 'Rachado', 'Riscado']),
        ];
    }
}
