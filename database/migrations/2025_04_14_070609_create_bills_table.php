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
        Schema::create('bills', function (Blueprint $table) {
            $table->id(); // local ID
            $table->bigInteger('bill_id')->unique(); // external source ID
            $table->string('bill_number')->nullable();
            $table->dateTime('bill_date')->nullable();
            $table->string('customer')->nullable();
            $table->string('from_place')->nullable();
            $table->string('to_place')->nullable();
            $table->date('dep_date')->nullable();
            $table->string('dep_time')->nullable();
            $table->date('arr_date')->nullable();
            $table->string('arr_time')->nullable();
            $table->string('vehicle_reg_no')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('car')->nullable();
            $table->integer('driver_id')->nullable();
            $table->string('zoho_invoice_id')->nullable(); // set when exported
            $table->timestamp('synced_at')->nullable();    // timestamp of export
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
