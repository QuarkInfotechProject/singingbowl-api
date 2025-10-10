<?php

namespace Modules\AccessGroup\Service\Role;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\AccessGroup\Trait\RolePermissionExistenceTrait;
use Modules\Menu\App\Models\Menu;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class RoleAssignPermissionService
{
    use RolePermissionExistenceTrait;

    function assignPermission($data)
    {
        try {
            DB::beginTransaction();

            // groupId and permissionId are now validated as UUIDs in controller
            $role = $this->checkRoleExistence($data['groupId']);
            $permissions = $this->checkPermissionExistence($data['permissionId']);

            $permissionAssignments = $permissions->map(function ($permission) use ($role, $data) {
                $menu = Menu::where('permission_name', $permission->name)->first();
                $menuId = $menu?->id;

                return [
                    'role' => $role,
                    'permission' => $permission,
                    'isIndex' => $permission->isIndex,
                    'menuId' => $menuId,
                ];
            });

            foreach ($permissionAssignments as $assignment) {
                $this->assignPermissionToGroup($assignment['role'], $assignment['permission'], $assignment['isIndex']);
                if ($assignment['isIndex'] && $assignment['menuId']) {
                    $this->setPermissionMenu($assignment['permission']->id, $data['groupId'], $assignment['menuId']);
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            Log::info('Failed to assign permission.', ['error' => $exception->getMessage()]);
            DB::rollBack();
            throw $exception;
        }
    }

    private function assignPermissionToGroup($role, $permission, $isIndex)
    {
        DB::table('permission_menu')
            ->where('group_id', $role->id)
            ->where('permission_id', $permission->id)
            ->delete();

        $role->permissions()->detach($permission);

        if ($role->hasPermissionTo($permission)) {
            throw new Exception('Permission already exists.', ErrorCode::CONFLICT);
        }

        $role->givePermissionTo($permission);

        DB::table('role_has_permissions')
            ->where('permission_id', $permission->id)
            ->where('role_id', $role->id)
            ->update(['isIndex' => $isIndex]);
    }

    private function setPermissionMenu($permissionId, $groupId, $menuId)
    {
        DB::table('permission_menu')->updateOrInsert(
            ['permission_id' => $permissionId, 'group_id' => $groupId],
            ['menu_id' => $menuId]
        );
    }
}
