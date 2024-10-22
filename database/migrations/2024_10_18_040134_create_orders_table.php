<?php

use App\Models\Api\Client;
use App\Models\Api\Vehicle;
use App\Models\Api\VehicleBrand;
use App\Models\Api\VehicleColor;
use App\Models\Api\VehicleType;
use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vehicle::class)->onDelete('cascade');
            $table->foreignIdFor(Client::class)->onDelete('cascade');
            $table->foreignIdFor(VehicleBrand::class)->onDelete('cascade');
            $table->foreignIdFor(VehicleColor::class)->onDelete('cascade');
            $table->foreignIdFor(VehicleType::class)->onDelete('cascade');
            $table->foreignIdFor(User::class)->onDelete('cascade');
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
