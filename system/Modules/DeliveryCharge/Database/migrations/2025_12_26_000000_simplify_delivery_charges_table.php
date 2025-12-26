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
        Schema::table('delivery_charges', function (Blueprint $table) {
            // Drop unused columns
            $table->dropColumn([
                'additional_charge_per_item',
                'weight_based_charge',
                'country',
                'country_code',
                'charge_above_20kg',
                'charge_above_45kg',
                'charge_above_100kg',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_charges', function (Blueprint $table) {
            $table->decimal('additional_charge_per_item')->nullable();
            $table->decimal('weight_based_charge')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->decimal('charge_above_20kg', 10, 2)->default(0);
            $table->decimal('charge_above_45kg', 10, 2)->default(0);
            $table->decimal('charge_above_100kg', 10, 2)->default(0);
        });
    }
};
