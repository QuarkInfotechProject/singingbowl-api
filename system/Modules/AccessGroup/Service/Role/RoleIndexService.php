<?php

namespace Modules\AccessGroup\Service\Role;

use Spatie\Permission\Models\Role;

class RoleIndexService
{
    function index($group)
    {
        $query = Role::query();

        $query->when(isset($group), function ($query) use ($group) {
            return $query->where('name', 'like', '%' . $group . '%');
        });

        return $query->where('name', '!=', 'Super Admin')
            ->select('id', 'name', 'created_at as createdAt')
            ->paginate(20);
    }
}
