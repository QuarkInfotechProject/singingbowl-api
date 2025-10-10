<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_user_activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('activityType');
            $table->string('ipAddress');
            $table->string('modifierId');
            $table->string('modifierUsername');
            $table->unsignedBigInteger('objectId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_activity_log');
    }
};
