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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id(); // auto-incrementing integer primary key
            $table->uuid('uuid')->nullable()->unique(); // separate uuid column
            $table->string('name');
            $table->boolean('super_admin')->default(false);
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('status')->default(1);
            $table->string('remarks')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
