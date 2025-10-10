<?php

namespace Modules\AccessGroup\Service\Permission;

use Spatie\Permission\Models\Permission;

class PermissionIndexService
{
    function index()
    {
        return Permission::select('id', 'name', 'section', 'description', 'isIndex')
            ->get();
    }
}
