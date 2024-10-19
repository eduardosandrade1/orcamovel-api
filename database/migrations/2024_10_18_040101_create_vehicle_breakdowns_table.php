<?php

use App\Models\Api\Order;
use App\Models\Api\VehicleParts;
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
        Schema::create('vehicle_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->onDelete('cascade');
            $table->foreignIdFor(VehicleParts::class)->onDelete('cascade');
            $table->string('breakdown_type'); // ex: Quebrado, Rachado, Riscado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_breakdowns');
    }
};
