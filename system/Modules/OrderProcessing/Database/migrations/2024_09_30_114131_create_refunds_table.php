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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->boolean('restock_items')->default(false);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::create('order_item_refund', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refund_id');
            $table->unsignedBigInteger('order_item_id');
            $table->integer('quantity');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('refund_id')->references('id')->on('refunds')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_refund');
        Schema::dropIfExists('refunds');
    }
};
