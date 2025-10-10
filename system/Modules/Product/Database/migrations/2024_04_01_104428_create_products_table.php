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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('product_name');
            $table->string('slug');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('best_seller')->default(0);
            $table->boolean('has_variant')->default(false);
            $table->decimal('original_price', 12)->nullable();
            $table->decimal('special_price', 12)->nullable();
            $table->dateTime('special_price_start')->nullable();
            $table->dateTime('special_price_end')->nullable();
            $table->string('sku')->nullable();
            $table->text('description');
            $table->text('additional_description')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('on_sale')->default(false);
            $table->integer('quantity')->nullable();
            $table->boolean('in_stock')->nullable();
            $table->json('specifications')->nullable();
            $table->date('new_from')->nullable();
            $table->date('new_to')->nullable();
            $table->dateTime('sale_start')->nullable();
            $table->dateTime('sale_end')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
