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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_searchable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_displayed')->default(true);
            $table->string('slug');
            $table->integer('parent_id')->default(0);
            $table->integer('filter_price_min')->unsigned()->nullable();
            $table->integer('filter_price_max')->unsigned()->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('show_in_new_arrivals')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
