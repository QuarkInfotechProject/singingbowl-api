<?php

namespace Modules\AdminUser\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\AdminUser\App\Models\AdminUser;
use Modules\AdminUser\DTO\AdminUserUpdateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Spatie\Permission\Models\Role;

class AdminUserUpdateService
{
    function update(AdminUserUpdateDTO $userUpdateDTO, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $adminUser = AdminUser::where('uuid', $userUpdateDTO->uuid)->first();

            if (!$adminUser) {
                throw new Exception('Admin user not found.', ErrorCode::NOT_FOUND);
            }

            if ($adminUser->super_admin) {
                throw new Exception('Super admin cannot be updated.', ErrorCode::UNPROCESSABLE_CONTENT);
            }

            $updateData = [
                'name' => $userUpdateDTO->name,
            ];

            if ($userUpdateDTO->password) {
                $updateData['password'] = Hash::make($userUpdateDTO->password);
            }

            $adminUser->update($updateData);

            if ($userUpdateDTO->groupId) {
                try {
                    $role = Role::findById($userUpdateDTO->groupId);
                    $adminUser->roles()->sync([$role->id]);
                } catch (\Exception $roleException) {
                    Log::error('Failed to update role for admin user.', [
                        'error' => $roleException->getMessage(),
                        'uuid' => $userUpdateDTO->uuid,
                        'group_id' => $userUpdateDTO->groupId,
                        'ip_address' => $ipAddress
                    ]);
                    DB::rollback();
                    throw $roleException;
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to update admin user.', [
                'error' => $exception->getMessage(),
                'uuid' => $userUpdateDTO->uuid,
                'ip_address' => $ipAddress
            ]);
            DB::rollback();
            throw $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                "{$adminUser->name} admin user has been updated by: " . Auth::user()->name,
                Auth::id(),
                ActivityTypeConstant::ADMIN_USER_UPDATED,
                $ipAddress)
        );
    }
}
