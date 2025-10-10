<?php

namespace Modules\AdminUser\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\AdminUser\App\Models\AdminUser;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AdminUserDestroyService
{
    function destroy(string $uuid, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $adminUser = AdminUser::where('uuid', $uuid)->first();

            if (!$adminUser) {
                throw new Exception('Admin user not found.', ErrorCode::NOT_FOUND);
            }

            if ($adminUser->super_admin) {
                throw new Exception('Super admin cannot be deleted.', ErrorCode::UNPROCESSABLE_CONTENT);
            }

            $adminUser->update(['status' => AdminUser::DELETED]);
            $adminUser->roles()->detach();
            $adminUser->delete();

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to delete admin user.', [
                'error' => $exception->getMessage(),
                'uuid' => $uuid,
                'ip_address' => $ipAddress
            ]);
            DB::rollback();
            throw $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
                "{$adminUser->name} admin user has been deleted by: " . Auth::user()->name,
                Auth::id(),
                ActivityTypeConstant::ADMIN_USER_DELETED,
                $ipAddress)
        );
    }
}
