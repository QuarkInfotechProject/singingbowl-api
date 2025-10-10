<?php

namespace Modules\Product\Service\User;

use Modules\Category\App\Models\Category;
use Modules\Product\Trait\GetBasicProductInformationTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductByCategoryService
{
    use GetBasicProductInformationTrait;

    function index(string $categoryName)
    {
        $category = Category::with(['products' => function ($query) {
            $query->select(
                'products.id',
                'products.uuid',
                'products.product_name',
                'products.slug',
                'products.new_from',
                'products.new_to',
                'products.sale_start',
                'products.sale_end',
                'products.in_stock',
                'products.original_price',
                'products.special_price',
                'products.special_price_start',
                'products.special_price_end',
                'products.has_variant',
                'products.created_at'
            )->where('products.status', true)
            ->latest();
        }])
            ->where('name', $categoryName)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            throw new Exception('Category not found.', ErrorCode::NOT_FOUND);
        }

        return $this->getBasicProductInformation($category->products);
    }
}
