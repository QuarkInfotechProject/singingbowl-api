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
        Schema::create('delivery_charges', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('delivery_charge')->default(0);
            $table->decimal('additional_charge_per_item')->nullable();
            $table->decimal('weight_based_charge')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_charges');
    }
};
