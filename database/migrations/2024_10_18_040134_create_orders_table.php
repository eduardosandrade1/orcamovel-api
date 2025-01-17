<?php

use App\Models\Api\Client;
use App\Models\Api\VehicleInfo;
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
            $table->foreignIdFor(Client::class)->onDelete('cascade');
            $table->foreignIdFor(User::class)->onDelete('cascade');
            $table->string("vehicle_color");
            $table->decimal('total_price', 10, 2);  // Coluna de preço total
            $table->text('notes')->nullable();  // Notas opcionais sobre o pedido
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
