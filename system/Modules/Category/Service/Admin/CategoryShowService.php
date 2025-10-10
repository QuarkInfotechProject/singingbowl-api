<?php

namespace Modules\Category\Service\Admin;

use Modules\Category\App\Models\Category;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoryShowService
{
    function show(int $id)
    {
        $category = Category::with('files')
            ->select('id', 'name', 'description', 'is_searchable as searchable', 'is_active as active', 'is_displayed as isDisplayed', 'slug', 'parent_id as parentId', 'filter_price_min as filterPriceMin', 'filter_price_max as filterPriceMax')
            ->find($id);

        if (!$category) {
            throw new Exception('Category not found', ErrorCode::NOT_FOUND);
        }

        return $category;
    }
}
