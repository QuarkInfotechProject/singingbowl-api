<?php

namespace Modules\AccessGroup\Service\Role;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Spatie\Permission\Models\Role;

class RoleUpdateService
{
    function update($data, string $ipAddress)
    {
        $role = Role::find($data['id']);

        if (!$role) {
            throw new Exception('User group not found.', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $this->logRoleUpdate($role['name'], $data['groupName'], $ipAddress);

            $role->update(['name' => $data['groupName']]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::info('Failed to update role.', ['error' => $exception->getMessage()]);
            DB::rollBack();
            throw $exception;
        }
    }

    private function logRoleUpdate($name, $groupName, $ipAddress)
    {
        Event::dispatch(new AdminUserActivityLogEvent(
                'User group updated of name: ' . $name . ' to ' . $groupName,
                Auth::id(),
                ActivityTypeConstant::USER_GROUP_UPDATED,
                $ipAddress)
        );
    }
}
