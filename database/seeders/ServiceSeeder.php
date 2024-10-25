<?php

namespace Database\Seeders;

use App\Models\Api\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::factory()->create([
            'name' => 'Funilaria',
            'company_id' => 1,
        ]);

        Service::factory()->create([
            'name' => 'Pintura',
            'company_id' => 1,
        ]);
    }
}
