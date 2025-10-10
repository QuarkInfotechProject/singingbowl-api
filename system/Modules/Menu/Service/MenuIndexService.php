<?php

namespace Modules\Menu\Service;

use Modules\Menu\App\Models\Menu;

class MenuIndexService
{
    function index()
    {
        $menuItems = Menu::orderBy('sort_order', 'asc')->get();

        return $this->buildMenuTree($menuItems, 0);
    }

    function buildMenuTree($menuItems, $parentId) {
        $menuTree = [];

        foreach ($menuItems as $item) {
            if ($item->parent_id === $parentId) {
                $children = $this->buildMenuTree($menuItems, $item->id);

                $menuItem = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'sortOrder' => $item->sort_order,
                    'icon' => $item->icon,
                    'url' => $item->url,
                    'status' => $item->status,
                    'parentId' => $item->parent_id
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
