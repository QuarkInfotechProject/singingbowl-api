<?php

namespace Modules\AdminUser\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\AdminUser\App\Models\AdminUser;

class AdminUserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminUser::create(
            [
                'name' => 'Super Admin',
                'super_admin' => true,
                'email' => 'admin@squarebx.com',
                'status' => AdminUser::ACTIVE,
                'password' => Hash::make('password'),
            ]
        );
    }
}
