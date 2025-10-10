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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->string('sku');
            $table->boolean('status')->default(true);
            $table->decimal('original_price', 12);
            $table->decimal('special_price', 12)->nullable();
            $table->dateTime('special_price_start')->nullable();
            $table->dateTime('special_price_end')->nullable();
            $table->integer('quantity')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->UnSignedBigInteger('product_id');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
