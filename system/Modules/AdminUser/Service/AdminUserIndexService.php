<?php

namespace Modules\AdminUser\Service;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Modules\AdminUser\DTO\AdminUserFilterDTO;

class AdminUserIndexService
{
    function index(AdminUserFilterDTO $adminUserFilterDTO)
    {
        if (isset($adminUserFilterDTO->name) || isset($adminUserFilterDTO->email) || isset($adminUserFilterDTO->status)) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        return DB::table('admin_users')
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'admin_users.id')
            ->when($adminUserFilterDTO->name, function ($query) use ($adminUserFilterDTO) {
                $query->where('admin_users.name', 'like', '%' . $adminUserFilterDTO->name . '%');
            })
            ->when($adminUserFilterDTO->email, function ($query) use ($adminUserFilterDTO) {
                $query->where('admin_users.email', $adminUserFilterDTO->email);
            })
            ->when($adminUserFilterDTO->status, function ($query) use ($adminUserFilterDTO) {
                $query->where('admin_users.status', $adminUserFilterDTO->status);
            })
            ->select(
                'admin_users.id',
                'admin_users.uuid',
                'admin_users.name as fullName',
                'admin_users.email',
                'admin_users.status',
                'model_has_roles.role_id as groupId'
            )
            ->latest('admin_users.created_at')
            ->paginate(25);
    }
}
