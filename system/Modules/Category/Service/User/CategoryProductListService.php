<?php

namespace Modules\Category\Service\User;

use Modules\Category\App\Models\Category;

class CategoryProductListService
{
    function index()
    {
        // Eager load products and their base image
        $categoryItems = Category::with(['products.files'])
            ->orderBy('sort_order', 'asc')
            ->get();

        return $this->buildCategoryTree($categoryItems, 0);
    }

    function buildCategoryTree($categoryItems, $parentId)
    {
        $categoryTree = [];

        foreach ($categoryItems as $item) {
            if ($item->parent_id === $parentId) {
                $children = $this->buildCategoryTree($categoryItems, $item->id);

                $categoryItem = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'sortOrder' => $item->sort_order,
                    'parentId' => $item->parent_id,
                    'products' => $item->products->map(function ($product) {
                        // Get base image
                        $baseImage = $product->files->first()?->url ?? '';

                        return [
                            'id' => $product->id,
                            'productName' => $product->product_name,
                            'url' => $product->slug,
                            'brandId' => $product->brand_id ?? null,
                            'bestSeller' => (bool) $product->best_seller,
                            'isNew' => (bool) $product->is_new,
                            'onSale' => (bool) $product->on_sale,
                            'soldCount' => $product->sold_count ?? 0,
                            'inStock' => $product->stock_quantity > 0,
                            'originalPrice' => number_format($product->price, 2, '.', ''),
                            'specialPrice' => $product->special_price ? number_format($product->special_price, 2, '.', '') : '',
                            'priceDifferencePercentage' => $product->special_price ? round((($product->price - $product->special_price)/$product->price)*100, 0) : 0,
                            'reviewCount' => $product->reviews_count ?? 0,
                            'rating' => $product->average_rating ?? 0,
                            'baseImage' => $baseImage,
                            'productOption' => null // you can map options here if needed
                        ];
                    })->toArray(),
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
