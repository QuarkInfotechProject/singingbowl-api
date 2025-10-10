<?php

namespace Modules\AccessGroup\App\Console;

use Illuminate\Console\Command;
use Modules\AdminUser\App\Models\AdminUser;
use Spatie\Permission\Models\Role;

class AssignSuperAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'assign:role';

    /**
     * The console command description.
     */
    protected $description = 'Assign super admin role to a super admin.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'admin'
        ]);

        $adminUser = AdminUser::first();

        if (!$adminUser) {
            $this->error("User not found.");
            return;
        }

        $adminUser->assignRole('Super Admin');

        $this->info("Super admin role has been assigned to the user.");
    }
}
