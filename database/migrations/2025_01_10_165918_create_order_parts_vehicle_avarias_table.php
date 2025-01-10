<?php

use App\Models\Api\OrderPartsVehicle;
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
        Schema::create('order_parts_vehicle_avarias', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderPartsVehicle::class);
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_parts_vehicle_avarias');
    }
};
