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
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('product_name');
            $table->integer('quantity');
            $table->string('purchased_from');
            $table->json('images');
            $table->string('description');
            $table->string('address')->nullable();
            $table->string('country_name')->nullable();
            $table->string('province_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('zone_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};
