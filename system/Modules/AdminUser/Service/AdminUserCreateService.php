<?php

namespace Modules\AdminUser\Service;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\AdminUser\App\Models\AdminUser;
use Modules\AdminUser\DTO\AdminUserCreateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Spatie\Permission\Models\Role;

class AdminUserCreateService
{
    function create(AdminUserCreateDTO $userCreateDTO, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $adminUser = AdminUser::create([
                'uuid' => Str::uuid(),
                'name' => $userCreateDTO->name,
                'email' => $userCreateDTO->email,
                'password' => Hash::make($userCreateDTO->password),
            ]);

            try {
                $role = Role::findById($userCreateDTO->groupId);
                $adminUser->assignRole($role);
            } catch (\Exception $roleException) {
                Log::info('Failed to assign role to admin user.', [
                    'error' => $roleException->getMessage(),
                    'admin_user_id' => $adminUser->id,
                    'role_id' => $userCreateDTO->groupId
                ]);
                DB::rollback();
                throw $roleException;
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::info('Failed to create admin user.', ['error' => $exception->getMessage()]);
            DB::rollback();
            throw $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                "{$userCreateDTO->name} admin user has been created by: " . Auth::user()->name,
                Auth::id(),
                ActivityTypeConstant::ADMIN_USER_CREATED,
                $ipAddress)
        );
    }
}
