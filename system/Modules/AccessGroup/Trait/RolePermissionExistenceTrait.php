<?php

namespace Modules\AccessGroup\Trait;

use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait RolePermissionExistenceTrait
{
    protected function checkRoleExistence($roleId)
    {
        // roleId is now expected to be a UUID
        $role = Role::find($roleId);

        if (!$role) {
            throw new Exception('User group not found.', ErrorCode::NOT_FOUND);
        }

        return $role;
    }

    protected function checkPermissionExistence($permissionIds)
    {
        // permissionIds are now expected to be UUIDs
        $permissions = Permission::findMany($permissionIds);

        $missingPermissionIds = array_diff($permissionIds, $permissions->pluck('id')->toArray());

        if (count($missingPermissionIds) > 0) {
            $missingPermissionIds = implode(', ', $missingPermissionIds);
            throw new Exception("Permissions with IDs {$missingPermissionIds} don't exist.", ErrorCode::NOT_FOUND);
        }

        return $permissions;
    }
}
