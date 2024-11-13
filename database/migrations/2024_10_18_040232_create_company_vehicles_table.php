<?php

use App\Models\Api\Company;
use App\Models\Api\Vehicle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Company::class)->onDelete('cascade');
            $table->foreignIdFor(Vehicle::class)->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_vehicles');
    }
};
