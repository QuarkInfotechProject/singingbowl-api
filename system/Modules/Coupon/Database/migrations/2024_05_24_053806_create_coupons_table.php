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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('type')->default('percentage');
            $table->decimal('value', 18)->unsigned()->default(0);
            $table->decimal('max_discount', 8, 2)->nullable();
            $table->decimal('minimum_spend', 18)->unsigned()->default(0);
            $table->integer('usage_limit_per_coupon')->unsigned()->nullable();
            $table->integer('usage_limit_per_customer')->unsigned()->nullable();
            $table->integer('min_quantity')->unsigned()->nullable();
            $table->integer('used')->default(0);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_bulk_offer')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('apply_automatically')->default(false);
            $table->boolean('individual_use_only')->default(false);
            $table->json('payment_methods')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
