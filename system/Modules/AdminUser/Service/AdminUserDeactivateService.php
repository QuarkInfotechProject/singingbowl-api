<?php

namespace Modules\AdminUser\Service;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\AdminUser\App\Models\AdminUser;
use Modules\AdminUser\DTO\AdminUserDeactivateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AdminUserDeactivateService
{
    function deactivateStatus(AdminUserDeactivateDTO $userDeactivateDTO, string $ipAddress)
    {
        try {
            DB::beginTransaction();

            $adminUser = AdminUser::where('uuid', $userDeactivateDTO->uuid)
                ->first();

            if (!$adminUser) {
                throw new Exception('Admin user not found.', ErrorCode::NOT_FOUND);
            }

            if ($adminUser->status === AdminUser::INACTIVE) {
                throw new Exception('Admin user is already in inactive state.', ErrorCode::CONFLICT);
            }

            if ($adminUser->super_admin) {
                throw new Exception('Super admin cannot be deactivated.', ErrorCode::UNPROCESSABLE_CONTENT);
            }

            $adminUser->update([
                'remarks' => $userDeactivateDTO->remarks,
                'status' => AdminUser::INACTIVE
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to deactivate admin user.', [
                'error' => $exception->getMessage(),
                'uuid' => $userDeactivateDTO->uuid,
                'ip_address' => $ipAddress
            ]);
            DB::rollback();
            throw $exception;
        }

        Event::dispatch(new AdminUserActivityLogEvent(
            "{$adminUser->name} admin user has been blocked by: " . Auth::user()->name,
            Auth::id(),
            ActivityTypeConstant::ADMIN_USER_BLOCKED,
            $ipAddress,
        ));
    }
}
