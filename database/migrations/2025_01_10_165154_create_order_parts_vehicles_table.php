<?php

use App\Models\Api\Order;
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
        Schema::create('order_parts_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class);
            $table->string('label');
            $table->string('value');
            $table->string('complement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_parts_vehicles');
    }
};
