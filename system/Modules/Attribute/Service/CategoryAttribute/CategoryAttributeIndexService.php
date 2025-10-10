<?php

namespace Modules\Attribute\Service\CategoryAttribute;

use Illuminate\Support\Facades\DB;
use Modules\Category\App\Models\Category;

class CategoryAttributeIndexService
{
    function index(int $categoryId)
    {
        // Verify category exists
        $category = Category::findOrFail($categoryId);

        $query = DB::table('category_attribute')
            ->join('attributes', 'category_attribute.attribute_id', '=', 'attributes.id')
            ->join('attribute_sets', 'attributes.attribute_set_id', '=', 'attribute_sets.id')
            ->where('category_attribute.category_id', $categoryId)
            ->select(
                'attributes.id',
                'attribute_sets.name as attributeSet',
                'attributes.name as attributeName',
                'category_attribute.sort_order',
                'category_attribute.is_active'
            )
            ->orderBy('category_attribute.sort_order');

        $perPage = request()->query('perPage', 25);

        return $query->paginate($perPage);
    }
}
