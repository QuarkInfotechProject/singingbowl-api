<?php

namespace Modules\Category\Service\Admin;

use Modules\Category\App\Models\Category;

class CategoryIndexService
{
    function index()
    {
        $categoryItems = Category::orderBy('sort_order', 'asc')->get();

        return $this->buildCategoryTree($categoryItems, 0);
    }

    function buildCategoryTree($categoryItems, $parentId) {
        $categoryTree = [];

        foreach ($categoryItems as $item) {
            if ($item->parent_id === $parentId) {
                $children = $this->buildCategoryTree($categoryItems, $item->id);

                $categoryItem = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'sortOrder' => $item->sort_order,
                    'parentId' => $item->parent_id
                ];

                if (!empty($children)) {
                    $categoryItem['children'] = $children;
                }

                $categoryTree[] = $categoryItem;
            }
        }

        return $categoryTree;
    }
}
