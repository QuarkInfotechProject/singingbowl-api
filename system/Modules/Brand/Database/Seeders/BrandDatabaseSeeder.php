<?php

namespace Modules\Brand\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BrandDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Faker instance to generate fake data
        $faker = Faker::create();

        // Seed the 'brands' table with sample data
        DB::table('brands')->insert([
            'name' => $faker->company,
            'description' => $faker->paragraph,
            'status' => $faker->randomElement(['active', 'inactive']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // You can add more sample brands as needed
        DB::table('brands')->insert([
            'name' => $faker->company,
            'description' => $faker->paragraph,
            'status' => 'active',  // Assigning a specific status
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
