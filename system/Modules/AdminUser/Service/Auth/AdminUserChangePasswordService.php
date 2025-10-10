<?php

namespace Modules\AdminUser\Service\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\AdminUser\DTO\AdminUserChangePasswordDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class AdminUserChangePasswordService
{
    function changePassword(AdminUserChangePasswordDTO $adminUserChangePasswordDTO, string $ipAddress)
    {
        try {
            DB::beginTransaction();
            $this->executePasswordChange($adminUserChangePasswordDTO);
            $this->logChangePassword($ipAddress);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    /**
     * @param $adminUserChangePasswordDTO
     * @return void
     */
    private function executePasswordChange($adminUserChangePasswordDTO)
    {
        Auth::user()->update([
            'password' => bcrypt($adminUserChangePasswordDTO->confirmPassword)
        ]);
    }

    /**
     * @param $ipAddress
     * @return void
     */
    private function logChangePassword($ipAddress)
    {
        Event::dispatch(new AdminUserActivityLogEvent(
                'Password Updated of name: ' . Auth::user()->name,
                Auth::id(),
                ActivityTypeConstant::PASSWORD_UPDATED,
                $ipAddress)
        );
    }
}
