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
        Schema::create('system_config_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name')->index()->unique();
            $table->string('title');
            $table->text('value')->nullable();
            $table->string('section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_config_settings');
    }
};
