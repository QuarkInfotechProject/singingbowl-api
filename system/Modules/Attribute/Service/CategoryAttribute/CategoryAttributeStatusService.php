<?php

namespace Modules\Attribute\Service\CategoryAttribute;

use Modules\Category\App\Models\Category;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoryAttributeStatusService
{
    function changeStatus(int $categoryId, int $attributeId, bool $isActive)
    {
        $category = Category::findOrFail($categoryId);

        $attributeExists = $category->attributes()->where('attributes.id', $attributeId)->exists();

        if (!$attributeExists) {
            throw new Exception('Attribute not found in this category.', ErrorCode::NOT_FOUND);
        }

        $category->attributes()->updateExistingPivot($attributeId, [
            'is_active' => $isActive,
        ]);

        return $category->attributes()
            ->where('attributes.id', $attributeId)
            ->select(
                'attributes.id',
                'attributes.name',
                'attributes.is_enabled',
                'category_attribute.sort_order',
                'category_attribute.is_active'
            )
            ->first();
    }
}
