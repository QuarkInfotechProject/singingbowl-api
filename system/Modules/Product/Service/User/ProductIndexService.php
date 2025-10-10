<?php

namespace Modules\Product\Service\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Product\App\Models\Product;
use Modules\Product\Trait\GetBasicProductInformationTrait;
use Modules\Shared\Services\CacheService; // Added CacheService
use Illuminate\Http\Request; // Added Request

class ProductIndexService
{
    use GetBasicProductInformationTrait;

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    // Modified index to accept Request object for pagination and filters
    public function index(Request $request): array
    {
        $page = $request->input('page', 1);
        // Assuming other filters might come from the request, collect them
        $filters = $request->except('page'); // Example: get all request params except page as filters
        $productName = $request->input('q', ''); // Assuming 'q' for product name search as per original logic

        if (!empty($productName)) {
            $filters['q'] = $productName; // Add product name to filters for cache key generation
        }

        $cacheKey = $this->cacheService->generateProductIndexKey((int)$page, $filters);
        $cacheTags = ['products'];

        $cachedProducts = $this->cacheService->get($cacheKey, $cacheTags);

        if ($cachedProducts !== null) {
            return $cachedProducts; // Return cached data directly
        }

        $query = Product::active();

        if (!empty($productName)) {
            $this->saveProductSearchKeywords($productName);
            $query->where('product_name', 'like', '%' . $productName . '%')
                ->orWhereHas('categories', function ($categoryQuery) use ($productName) {
                    $categoryQuery->where('name', 'like', '%' . $productName . '%');
                });
        }

        // Apply other filters from $filters array to $query if necessary
        // For example:
        // if (isset($filters['category_id'])) {
        //     $query->where('category_id', $filters['category_id']);
        // }
        // This part needs to be adapted based on actual filterable fields

        // Paginate results - assuming a default pagination size or get from config/request
        $perPage = config('pagination.per_page', 15);
        $products = $query->paginate($perPage);

        // Pass the Eloquent Collection from the paginator to the trait
        $transformedProducts = $this->getBasicProductInformation($products->getCollection());

        // Cache only the transformed products array
        $this->cacheService->put($cacheKey, $transformedProducts, $this->cacheService->getProductIndexTtl(), $cacheTags);

        return $transformedProducts; // Return only the array of transformed products
    }

    private function saveProductSearchKeywords($keyword)
    {
        $now = Carbon::now();

        try {
            DB::table('product_search_keywords')->updateOrInsert(
                ['keyword' => $keyword],
                [
                    'count' => DB::raw('COALESCE(`count`, 0) + 1'),
                ]
            );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
