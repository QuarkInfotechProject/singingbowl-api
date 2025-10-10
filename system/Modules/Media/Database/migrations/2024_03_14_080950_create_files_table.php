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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_category_id')->nullable();
            $table->boolean('is_grouped')->default(false);
            $table->string('filename');
            $table->string('temp_filename')->index();
            $table->string('alternative_text')->nullable();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->string('description')->nullable();
            $table->string('disk');
            $table->string('path');
            $table->string('extension');
            $table->string('mime');
            $table->integer('size');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->timestamps();

            $table->foreign('file_category_id')->references('id')->on('file_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
