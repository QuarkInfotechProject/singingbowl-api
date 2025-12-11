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
        Schema::table('addresses', function (Blueprint $table) {
            
            // 1. Add new columns
            // We use 'after' to place them nicely in the database, though it's optional.
            $table->string('email')->after('user_id');
            
            // 2. Handle the "Renamed" column
            // Instead of creating a new string column, we RENAME the existing 'address' column
            // so you don't lose any old data stored in it.
            $table->renameColumn('address', 'address_line_1');

            // 3. Add the rest of the new fields
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('postal_code')->after('address_line_2');
            $table->string('landmark')->nullable()->after('postal_code');
            $table->string('address_type')->default('home')->after('landmark'); // Values: 'home', 'office', 'other'
            $table->text('delivery_instructions')->nullable()->after('address_type');
            $table->boolean('is_default')->default(false)->after('delivery_instructions');
            $table->string('label')->nullable()->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // If we rollback, we remove the new columns
            $table->dropColumn([
                'email',
                'address_line_2',
                'postal_code',
                'landmark',
                'address_type',
                'delivery_instructions',
                'is_default',
                'label'
            ]);

            // And we rename 'address_line_1' back to 'address'
            $table->renameColumn('address_line_1', 'address');
        });
    }
};
