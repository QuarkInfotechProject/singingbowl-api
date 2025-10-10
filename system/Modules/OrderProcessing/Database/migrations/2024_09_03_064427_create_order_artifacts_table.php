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
        Schema::create('order_artifacts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('order_count');
            $table->string('shipping_company')->default('none');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_artifacts');
    }
};
