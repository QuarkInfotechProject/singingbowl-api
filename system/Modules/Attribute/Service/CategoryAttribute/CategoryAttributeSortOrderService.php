<?php

namespace Modules\Attribute\Service\CategoryAttribute;

use Illuminate\Support\Facades\DB;
use Modules\Category\App\Models\Category;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoryAttributeSortOrderService
{
    function reOrder(int $categoryId, array $attributeOrder)
    {
        $category = Category::findOrFail($categoryId);

        DB::transaction(function () use ($category, $attributeOrder) {
            foreach ($attributeOrder as $index => $attributeId) {
                $attributeExists = $category->attributes()->where('attributes.id', $attributeId)->exists();

                if (!$attributeExists) {
                    throw new Exception("Attribute ID {$attributeId} not found in this category.", ErrorCode::NOT_FOUND);
                }

                $category->attributes()->updateExistingPivot($attributeId, [
                    'sort_order' => $index,
                ]);
            }
        });

        return $category->attributes()
            ->select(
                'attributes.id',
                'attributes.name',
                'attributes.is_enabled',
                'category_attribute.sort_order',
                'category_attribute.is_active'
            )
            ->get();
    }
}
