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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->unsignedBigInteger('product_id')->index();
            $table->enum('type', ['review', 'question']);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('comment')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_replied')->default(false);
            $table->string('ip_address')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
