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
        Schema::create('guest_cart_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guest_cart_id');
            $table->unsignedBigInteger('coupon_id');
            $table->decimal('discount_amount', 8, 2);
            $table->timestamps();

            $table->foreign('guest_cart_id')->references('id')->on('guest_carts')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->unique(['guest_cart_id', 'coupon_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_cart_coupons');
    }
};