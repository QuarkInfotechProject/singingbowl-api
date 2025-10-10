<?php

namespace Modules\Menu\Service;

use Illuminate\Support\Facades\DB;
use Modules\Menu\App\Models\Menu;
use Spatie\Permission\Models\Permission;

class MenuService
{
    function index()
    {
        $user = auth()->user();

        $menuItems = Menu::where('status', true)
            ->when(!$user->hasRole('Super Admin'), function ($query) use ($user) {
                $permittedMenuIds = $this->getPermittedMenuIdsForUser($user);
                return $query->whereIn('id', $permittedMenuIds);
            })
            ->orderBy('sort_order', 'asc')
            ->get();

        return $this->buildMenuTree($menuItems, 0);
    }

    private function getPermittedMenuIdsForUser($user)
    {
        $userPermissionIds = $user->getAllPermissions()->pluck('id')->toArray();

        $adminNavigationPermissionId = Permission::where('name', 'view_menus')->value('id');

        return DB::table('permission_menu')
            ->whereIn('permission_id', $userPermissionIds)
//            ->whereNotIn('permission_id', [$adminNavigationPermissionId])
            ->pluck('menu_id')
            ->toArray();
    }

    function buildMenuTree($menuItems, $parentId)
    {
        $menuTree = [];

        foreach ($menuItems as $item) {
            if ($item->parent_id === $parentId) {
                $children = $this->buildMenuTree($menuItems, $item->id);

                $menuItem = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'icon' => $item->icon,
                    'url' => $item->url,
                    'permissionName' => $item->permisison_name
                ];

                if (!empty($children)) {
                    $menuItem['children'] = $children;
                }

                $menuTree[] = $menuItem;
            }
        }

        return $menuTree;
    }
}
