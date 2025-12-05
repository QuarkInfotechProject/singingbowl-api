<?php

namespace Modules\Product\Service\User;

use Modules\Category\App\Models\Category;
use Modules\Product\Trait\GetBasicProductInformationTrait;

class ProductListByCategoryService
{
    use GetBasicProductInformationTrait;

    public function index()
    {
        $categories = Category::with(['products' => function ($query) {
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
            ->where('is_active', true)
            ->get();

        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'uuid' => $category->uuid,
                'name' => $category->name,
                'slug' => $category->slug,
                'products' => $this->getBasicProductInformation($category->products)
            ];
        });
    }
}
