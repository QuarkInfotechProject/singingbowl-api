<?php

namespace Modules\AccessGroup\Service\Role;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Spatie\Permission\Models\Role;

class RoleDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        $role = Role::find($id);

        if (!$role) {
            throw new Exception('User group not found', ErrorCode::NOT_FOUND);
        }

        try {
            $this->logRoleDestroy($role['name'], $ipAddress);

            $role->delete();
        } catch (\Exception $exception) {
            Log::info('Failed to delete role.', ['error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    private function logRoleDestroy(string $name, $ipAddress)
    {
        Event::dispatch(new AdminUserActivityLogEvent(
                'User group destroyed of name: ' . $name,
                Auth::id(),
                ActivityTypeConstant::USER_GROUP_DESTROYED,
                $ipAddress)
        );
    }
}
