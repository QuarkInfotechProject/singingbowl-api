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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('mobile');
            $table->string('backup_mobile')->nullable();
            $table->string('address')->nullable();
            $table->string('country_name')->nullable();
            $table->integer('province_id')->nullable();
            $table->string('province_name')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('city_name')->nullable();
            $table->integer('zone_id')->nullable();
            $table->string('zone_name')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
