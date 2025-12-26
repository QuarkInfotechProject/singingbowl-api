<?php

namespace Modules\Product\Service\User;

use Illuminate\Http\Request;
use Modules\Product\App\Models\Product;
use Modules\Product\Trait\GetBasicProductInformationTrait;

class ProductSimpleSearchService
{
    use GetBasicProductInformationTrait;

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        $perPage = min($request->input('per_page', 12), 50);
        $page = $request->input('page', 1);

        if (empty($query)) {
            return [
                'products' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                    'last_page' => 1,
                    'has_more_pages' => false,
                ],
                'query' => '',
            ];
        }

        $products = Product::query()
            ->select(
                'id',
                'uuid',
                'product_name',
                'slug',
                'brand_id',
                'best_seller',
                'new_from',
                'new_to',
                'sale_start',
                'sale_end',
                'in_stock',
                'original_price',
                'special_price',
                'special_price_start',
                'special_price_end',
                'has_variant',
                'created_at'
            )
            ->where('status', true)
            ->where('product_name', 'ILIKE', '%' . $query . '%')
            ->orderByRaw("
                CASE 
                    WHEN LOWER(product_name) LIKE ? THEN 1
                    WHEN LOWER(product_name) LIKE ? THEN 2
                    ELSE 3 
                END
            ", [strtolower($query) . '%', '%' . strtolower($query) . '%'])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedProducts = $this->getBasicProductInformation($products->getCollection());

        return [
            'products' => $formattedProducts,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'has_more_pages' => $products->hasMorePages(),
            ],
            'query' => $query,
        ];
    }

    public function suggestions(Request $request)
    {
        $query = trim($request->input('q', ''));
        $limit = min($request->input('limit', 10), 20);

        if (empty($query)) {
            return [
                'suggestions' => []
            ];
        }

        $products = Product::query()
            ->select('product_name')
            ->where('status', true)
            ->where('product_name', 'ILIKE', '%' . $query . '%')
            ->orderByRaw("
                CASE 
                    WHEN LOWER(product_name) LIKE ? THEN 1
                    WHEN LOWER(product_name) LIKE ? THEN 2
                    ELSE 3 
                END
            ", [strtolower($query) . '%', '%' . strtolower($query) . '%'])
            ->limit($limit)
            ->get()
            ->pluck('product_name')
            ->unique()
            ->values()
            ->toArray();

        return [
            'suggestions' => $products
        ];
    }
}
