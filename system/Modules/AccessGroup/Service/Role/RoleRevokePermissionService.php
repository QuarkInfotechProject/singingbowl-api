<?php

namespace Modules\AccessGroup\Service\Role;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\AccessGroup\Trait\RolePermissionExistenceTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Spatie\Permission\Models\Permission;

class RoleRevokePermissionService
{
    use RolePermissionExistenceTrait;

    function revokePermission($data)
    {
        // groupId and permissionId are now validated as UUIDs in controller
        $role = $this->checkRoleExistence($data['groupId']);
        $permission = Permission::find($data['permissionId']);

        if (!$permission) {
            throw new Exception('Permission doesn\'t exist.', ErrorCode::NOT_FOUND);
        }

        if (!$role->hasPermissionTo($permission)) {
            throw new Exception('Permission doesn\'t exist for this user group.', ErrorCode::NOT_FOUND);
        }

        $menuRelatedPermissions = DB::table('permission_menu')
            ->where('permission_id', $data['permissionId'])
            ->first();

        try {
            DB::beginTransaction();

            $role->revokePermissionTo($permission);

            if (!empty($menuRelatedPermissions)) {
                DB::table('permission_menu')
                    ->where('group_id', $data['groupId'])
                    ->where('permission_id', $data['permissionId'])
                    ->delete();
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::info('Failed to revoke permission.', ['error' => $exception->getMessage()]);
            DB::rollBack();
            throw $exception;
        }
    }
}
