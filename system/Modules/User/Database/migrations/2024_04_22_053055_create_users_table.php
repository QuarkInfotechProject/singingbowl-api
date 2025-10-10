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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_no')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->boolean('offers_notification')->default(false);
            $table->string('profile_picture')->nullable();
            $table->string('password')->nullable();
            $table->integer('status')->default(1);
            $table->text('remarks')->nullable();
            $table->string('oauth_type')->nullable();
            $table->string('oauth_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
