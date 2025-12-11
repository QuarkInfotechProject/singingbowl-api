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
            // Place country fields after the description
            $table->string('country')->nullable()->after('description');
            $table->string('country_code')->nullable()->after('country');

            // Place new weight charges after the existing weight_based_charge
            $table->decimal('charge_above_20kg', 10, 2)->default(0)->after('weight_based_charge');
            $table->decimal('charge_above_45kg', 10, 2)->default(0)->after('charge_above_20kg');
            $table->decimal('charge_above_100kg', 10, 2)->default(0)->after('charge_above_45kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_charges', function (Blueprint $table) {
            $table->dropColumn([
                'country',
                'country_code',
                'charge_above_20kg',
                'charge_above_45kg',
                'charge_above_100kg'
            ]);
        });
    }
};
