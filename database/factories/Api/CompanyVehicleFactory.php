<?php

namespace Database\Factories\Api;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\CompanyVehicle>
 */
class CompanyVehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Api\Company::factory(),
            'vehicle_id' => \App\Models\Api\Vehicle::factory(),
        ];
    }
}
