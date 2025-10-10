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
        Schema::create('guest_carts', function (Blueprint $table) {
            $table->id();
            $table->uuid('guest_token')->unique()->index();
            $table->timestamps();
        });

        Schema::create('guest_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_cart_id')->constrained('guest_carts')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->integer('quantity')->unsigned();
            $table->decimal('purchased_price', 10, 2);
            $table->json('variant_options')->nullable(); // Storing selected variant options if any
            $table->timestamps();

            // Foreign keys to products and product_variants (optional, depends on if you want DB level constraint)
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_cart_items');
        Schema::dropIfExists('guest_carts');
    }
};
