<?php

namespace Modules\Product\Service\User;

use Modules\Category\App\Models\Category;
use Modules\Product\Trait\GetBasicProductInformationTrait;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductListByCategoryService
{
    use GetBasicProductInformationTrait;
    
    function index()
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
            ->select('id', 'name', 'slug', 'description', 'is_active')
            ->where('is_active', true)
            ->get();
            
        if ($categories->isEmpty()) {
            throw new Exception('No categories found.', ErrorCode::NOT_FOUND);
        }
        
        // Format the response
        $result = [];
        foreach ($categories as $category) {
            $products = $this->getBasicProductInformation($category->products);
            
            $result[] = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'products' => $products
            ];
        }
        
        return $result;
    }
}
