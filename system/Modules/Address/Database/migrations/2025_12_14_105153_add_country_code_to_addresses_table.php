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
        Schema::table('addresses', function (Blueprint $table) {
            // Adding country_code, generally 2 or 3 chars (e.g., US, IN, NPL), 
            // but we allow up to 10 just in case.
            // We make it nullable initially to prevent errors with existing rows, 
            // unless you plan to fill it immediately.
            $table->string('country_code', 10)->nullable()->after('country_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('country_code');
        });
    }
};
