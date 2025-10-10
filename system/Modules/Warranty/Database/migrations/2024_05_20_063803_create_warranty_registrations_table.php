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
        Schema::create('warranty_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('product_name');
            $table->integer('quantity');
            $table->date('date_of_purchase');
            $table->string('purchased_from');
            $table->string('order_id')->nullable();
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
        Schema::dropIfExists('warranty_registrations');
    }
};
