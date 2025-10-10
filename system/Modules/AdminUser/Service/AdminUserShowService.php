<?php

namespace Modules\AdminUser\Service;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AdminUserShowService
{
    function show(string $uuid)
    {
        $adminUser = DB::table('admin_users')
            ->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'admin_users.id')
            ->select(
                'admin_users.id',
                'admin_users.uuid',
                'admin_users.name as fullName',
                'admin_users.email',
                'admin_users.status',
                'model_has_roles.role_id as groupId'
            )
            ->where('uuid', $uuid)
            ->first();

        if (!$adminUser) {
            throw new Exception('Admin user not found.', ErrorCode::NOT_FOUND);
        }

        return $adminUser;
    }
}
