<?php

namespace Database\Seeders;

use App\Models\Api\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserCompanySeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create();
        Company::factory()->create();
    }
}
