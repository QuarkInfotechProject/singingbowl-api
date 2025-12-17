<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the primary key constraint first
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Drop the default value (sequence) and change the column type to UUID
        DB::statement('ALTER TABLE notifications ALTER COLUMN id DROP DEFAULT');
        DB::statement('ALTER TABLE notifications ALTER COLUMN id TYPE UUID USING id::text::uuid');
        
        // Drop the sequence if it exists
        DB::statement('DROP SEQUENCE IF EXISTS notifications_id_seq');

        // Re-add the primary key constraint
        Schema::table('notifications', function (Blueprint $table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the primary key constraint
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });

        // Change the column type back to bigint
        DB::statement('ALTER TABLE notifications ALTER COLUMN id TYPE BIGINT USING id::text::bigint');

        // Re-add the primary key constraint
        Schema::table('notifications', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
