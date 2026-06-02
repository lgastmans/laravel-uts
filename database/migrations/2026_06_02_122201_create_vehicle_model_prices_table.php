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
        Schema::create('vehicle_model_prices', function (Blueprint $table) {
            $table->id();
            $table->string('model')->unique();
            $table->decimal('day_rent', 10, 2)->default(0);
            $table->decimal('rate_per_km', 10, 2)->default(0);
            $table->decimal('hourly_charge', 10, 2)->default(0);
            $table->decimal('local_charge', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_model_prices');
    }
};
