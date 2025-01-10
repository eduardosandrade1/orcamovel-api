<?php

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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('plate');                 // Placa do veículo
            $table->date('prev_date')->nullable();               // Data prevista
            $table->decimal('price_parts', 8, 2);    // Preço das peças
            $table->string('type_vehicle');          // Tipo do veículo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('plate');                 // Placa do veículo
            $table->dropColumn('prev_date');               // Data prevista
            $table->dropColumn('price_parts');    // Preço das peças
            $table->dropColumn('type_vehicle');          // Tipo do veículo
        });
    }
};
