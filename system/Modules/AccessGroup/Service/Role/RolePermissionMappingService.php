<?php

namespace Modules\AccessGroup\Service\Role;

use Modules\AccessGroup\Trait\RolePermissionExistenceTrait;

class RolePermissionMappingService
{
    use RolePermissionExistenceTrait;

    function index($groupId)
    {
        // groupId is now validated as UUID in controller
        $role = $this->checkRoleExistence($groupId);

        return $role->permissions
            ->pluck('id');
    }
}
