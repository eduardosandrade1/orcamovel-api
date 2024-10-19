<?php

namespace Database\Factories\Api;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate' => strtoupper($this->faker->bothify('???-####')),
            'model' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'color' => $this->faker->safeColorName(),
        ];
    }
}
