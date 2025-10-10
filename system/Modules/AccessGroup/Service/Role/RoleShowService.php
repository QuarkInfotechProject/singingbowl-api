<?php

namespace Modules\AccessGroup\Service\Role;

use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Spatie\Permission\Models\Role;

class RoleShowService
{
    function show(int $id)
    {
        $role = Role::select('id', 'name')->find($id);

        if (!$role) {
            throw new Exception('User group not found.', ErrorCode::NOT_FOUND);
        }

        return $role;
    }
}
