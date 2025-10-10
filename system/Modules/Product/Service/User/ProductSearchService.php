<?php

namespace Modules\Product\Service\User;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Product\App\Models\Product;
use Modules\Brand\App\Models\Brand;
use Modules\Category\App\Models\Category;
use Modules\Shared\Services\CacheService; // Added CacheService

class ProductSearchService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        $perPage = min($request->input('per_page', 12), 50);
        $page = $request->input('page', 1);
        $filters = $this->getFilters($request);

        $params = array_merge(['q' => $query, 'page' => $page, 'per_page' => $perPage], $filters);
        $searchIdCacheKey = $this->cacheService->generateKey('search:ids', $params);
        $searchMetaCacheKey = $this->cacheService->generateKey('search:meta', $params);
        $cacheTags = ['searches'];

        // 1. Get the list of product IDs from cache or from a new search
        $productIds = $this->cacheService->remember($searchIdCacheKey, function () use ($query, $filters, $perPage, $page, $searchMetaCacheKey) {
            if (empty($query)) {
                $results = $this->getFilteredProducts($filters, $perPage, $page);
                $this->cacheService->put($searchMetaCacheKey, $results['pagination'], $this->cacheService->getSearchResultsTtl(), ['searches']);
                return $results['products']->pluck('id')->toArray();
            }

            $searchBuilder = Product::search($query);
            $searchBuilder = $this->applySearchFilters($searchBuilder, $filters);
            $results = $searchBuilder->paginate($perPage, 'page', $page);

            $paginationData = [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
                'has_more_pages' => $results->hasMorePages(),
            ];

            $this->cacheService->put($searchMetaCacheKey, $paginationData, $this->cacheService->getSearchResultsTtl(), ['searches']);

            return $results->pluck('id')->toArray();
        }, $this->cacheService->getSearchResultsTtl(), $cacheTags);

        if (empty($productIds)) {
            return [
                'products' => [],
                'pagination' => $this->cacheService->get($searchMetaCacheKey, $cacheTags) ?? [],
                'query' => $query,
            ];
        }

        // 2. Fetch the detailed product data for the list view from a separate cache
        $products = Product::whereIn('id', $productIds)
            ->with(['files', 'variants.optionValues.files', 'variants.optionValues.option'])
            ->get()
            ->keyBy('id');

        $formattedProducts = [];
        foreach ($productIds as $id) {
            if (isset($products[$id])) {
                $formattedProducts[] = $this->formatSingleProduct($products[$id]);
            }
        }

        return [
            'products' => $formattedProducts,
            'pagination' => $this->cacheService->get($searchMetaCacheKey, $cacheTags),
            'query' => $query,
        ];
    }

    public function getSuggestions(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);

        if (empty(trim($query))) {
            return [];
        }

        $productSuggestions = Product::search($query)
            ->where('status', true)
            ->take($limit)
            ->get()
            ->map(function ($product) {
                return $product->product_name;
            });

        return [
            'suggestions' => $productSuggestions->values()->all()
        ];
    }

    private function formatSingleProduct(Product $product)
    {
        $originalPrice = $product->original_price;
        $specialPrice = $product->special_price;

        if ($product->has_variant && $product->variants->isNotEmpty()) {
            $variant = $product->variants->first();
            $originalPrice = $variant->original_price ?? $product->original_price;
            $specialPrice = $this->getValidSpecialPrice($variant) ?? $this->getValidSpecialPrice($product);
        } else {
            $specialPrice = $this->getValidSpecialPrice($product);
        }

        return [
            'product_name' => $product->product_name,
            'slug' => $product->slug,
            'original_price' => (float) $originalPrice,
            'special_price' => $specialPrice !== null ? (float) $specialPrice : null,
            'base_image' => $this->getProductImage($product),
        ];
    }

    /**
     * Get valid special price if it's within the date range
     */
    private function getValidSpecialPrice($entity): ?float
    {
        $now = now();

        if (
            isset($entity->special_price) &&
            $entity->special_price > 0 &&
            (!isset($entity->special_price_start) || $now->gte($entity->special_price_start)) &&
            (!isset($entity->special_price_end) || $now->lte($entity->special_price_end))
        ) {
            return (float) $entity->special_price;
        }

        return null;
    }
    private function getFilteredProducts($filters, $perPage, $page)
    {
        $queryBuilder = Product::query()
            ->select([
                'id', 'product_name', 'slug', 'original_price', 'special_price', 'has_variant'
            ])
            ->with([
                'files',
                'variants' => function ($query) {
                    $query->select('id', 'product_id', 'original_price', 'special_price', 'special_price_start', 'special_price_end')
                          ->with([
                              'files',
                              'optionValues' => function ($q) {
                                  $q->whereHas('option', function ($optionQuery) {
                                      $optionQuery->where('name', 'Color');
                                  })->with(['files', 'option']);
                              }
                          ])
                          ->limit(1);
                }
            ])
            ->where('status', true);

        $queryBuilder = $this->applyDatabaseFilters($queryBuilder, $filters);

        if (!empty($filters['sort_by'])) {
            $queryBuilder = $this->applySorting($queryBuilder, $filters['sort_by']);
        } else {
            $queryBuilder->orderByDesc('created_at');
        }

        $results = $queryBuilder->paginate($perPage, ['*'], 'page', $page);

        $formattedProducts = $results->map(function ($product) {
            return $this->formatSingleProduct($product);
        });

        return [
            'products' => $formattedProducts,
            'pagination' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
                'has_more_pages' => $results->hasMorePages(),
            ],
            'query' => '',
        ];
    }

    private function applySearchFilters($searchBuilder, $filters)
    {
        $searchBuilder->where('status', true);

        if (!empty($filters['brand_ids'])) {
            $searchBuilder->whereIn('brand_id', $filters['brand_ids']);
        }

        if (!empty($filters['category_ids'])) {
            $searchBuilder->whereIn('category_ids', $filters['category_ids']);
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $searchBuilder->where('special_price', '>=', (float)$filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $searchBuilder->where('special_price', '<=', (float)$filters['max_price']);
        }

        if (!empty($filters['in_stock_only']) && $filters['in_stock_only']) {
            $searchBuilder->where('in_stock', true);
        }

        if (!empty($filters['sort_by'])) {
            $sortField = null;
            $sortDirection = 'asc';
            switch ($filters['sort_by']) {
                case 'newest':
                    $sortField = 'created_at';
                    $sortDirection = 'desc';
                    break;
                case 'price_low_high':
                    $sortField = 'special_price';
                    $sortDirection = 'asc';
                    break;
                case 'price_high_low':
                    $sortField = 'special_price';
                    $sortDirection = 'desc';
                    break;
                case 'popular':
                    break;
            }
            if ($sortField) {
                $searchBuilder->orderBy($sortField, $sortDirection);
            }
        }

        return $searchBuilder;
    }

    private function applyDatabaseFilters($query, $filters)
    {
        if (!empty($filters['brand_ids'])) {
            $query->whereIn('brand_id', $filters['brand_ids']);
        }

        if (!empty($filters['category_ids'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->whereIn('categories.id', $filters['category_ids']);
            });
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $minPrice = (float)$filters['min_price'];
            $query->where(function ($q) use ($minPrice) {
                $q->where(function ($subQ) use ($minPrice) {
                    $subQ->where('has_variant', false)
                         ->where(function ($priceQ) use ($minPrice) {
                             $priceQ->where('special_price', '>=', $minPrice)
                                    ->orWhere(function ($fallbackQ) use ($minPrice) {
                                        $fallbackQ->whereNull('special_price')
                                                 ->where('original_price', '>=', $minPrice);
                                    });
                         });
                })->orWhere(function ($subQ) use ($minPrice) {
                    $subQ->where('has_variant', true)
                         ->whereHas('variants', function ($variantQ) use ($minPrice) {
                             $variantQ->where(function ($priceQ) use ($minPrice) {
                                 $priceQ->where('special_price', '>=', $minPrice)
                                        ->orWhere(function ($fallbackQ) use ($minPrice) {
                                            $fallbackQ->whereNull('special_price')
                                                     ->where('original_price', '>=', $minPrice);
                                        });
                             });
                         });
                });
            });
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $maxPrice = (float)$filters['max_price'];
            $query->where(function ($q) use ($maxPrice) {
                $q->where(function ($subQ) use ($maxPrice) {
                    $subQ->where('has_variant', false)
                         ->where(function ($priceQ) use ($maxPrice) {
                             $priceQ->where('special_price', '<=', $maxPrice)
                                    ->orWhere(function ($fallbackQ) use ($maxPrice) {
                                        $fallbackQ->whereNull('special_price')
                                                 ->where('original_price', '<=', $maxPrice);
                                    });
                         });
                })->orWhere(function ($subQ) use ($maxPrice) {
                    $subQ->where('has_variant', true)
                         ->whereHas('variants', function ($variantQ) use ($maxPrice) {
                             $variantQ->where(function ($priceQ) use ($maxPrice) {
                                 $priceQ->where('special_price', '<=', $maxPrice)
                                        ->orWhere(function ($fallbackQ) use ($maxPrice) {
                                            $fallbackQ->whereNull('special_price')
                                                     ->where('original_price', '<=', $maxPrice);
                                        });
                             });
                         });
                });
            });
        }

        if (!empty($filters['in_stock_only']) && $filters['in_stock_only']) {
            $query->where('in_stock', true);
        }

        return $query;
    }

    private function applySorting($queryBuilder, $sortBy)
    {
        switch ($sortBy) {
            case 'newest':
                $queryBuilder->orderByDesc('created_at');
                break;
            case 'price_low_high':
                $queryBuilder->orderByRaw('
                    CASE
                        WHEN has_variant = 1 THEN (
                            SELECT COALESCE(pv.special_price, pv.original_price)
                            FROM product_variants pv
                            WHERE pv.product_id = products.id
                            ORDER BY COALESCE(pv.special_price, pv.original_price) ASC
                            LIMIT 1
                        )
                        ELSE COALESCE(special_price, original_price)
                    END ASC
                ');
                break;
            case 'price_high_low':
                $queryBuilder->orderByRaw('
                    CASE
                        WHEN has_variant = 1 THEN (
                            SELECT COALESCE(pv.special_price, pv.original_price)
                            FROM product_variants pv
                            WHERE pv.product_id = products.id
                            ORDER BY COALESCE(pv.special_price, pv.original_price) DESC
                            LIMIT 1
                        )
                        ELSE COALESCE(special_price, original_price)
                    END DESC
                ');
                break;
            case 'popular':
                $queryBuilder->orderByDesc('created_at');
                break;
            default:
                $queryBuilder->orderByDesc('created_at');
        }
        return $queryBuilder;
    }


    private function getFilters(Request $request): array
    {
        return [
            'brand_ids' => $request->input('brand_ids', []),
            'category_ids' => $request->input('category_ids', []),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'in_stock_only' => $request->boolean('in_stock_only', false),
            'sort_by' => $request->input('sort_by', 'relevance'),
        ];
    }

    private function formatProductResults(iterable $products): array
    {
        $formatted = [];
        foreach ($products as $product) {
            if ($product instanceof Product) {
                 $formatted[] = $this->formatSingleProduct($product);
            } else {
                $formatted[] = [
                    'product_name' => $product->product_name ?? 'N/A',
                    'slug' => $product->slug ?? 'n-a',
                ];
            }
        }
        return $formatted;
    }

    private function getAvailableFilters(iterable $productsFromSearch): array
    {
        $brandIds = [];
        $categoryIds = [];

        foreach ($productsFromSearch as $product) {
            if (isset($product->brand_id)) {
                $brandIds[] = $product->brand_id;
            }
            if (isset($product->category_ids) && is_array($product->category_ids)) {
                $categoryIds = array_merge($categoryIds, $product->category_ids);
            } elseif (isset($product->categories) && $product->categories instanceof \Illuminate\Support\Collection) {
                 foreach ($product->categories as $category) {
                    $categoryIds[] = $category->id;
                }
            }
        }

        $uniqueBrandIds = array_values(array_unique($brandIds));
        $uniqueCategoryIds = array_values(array_unique($categoryIds));

        $brands = Brand::whereIn('id', $uniqueBrandIds)->select('id', 'name', 'slug')->get();
        $categories = Category::whereIn('id', $uniqueCategoryIds)->select('id', 'name', 'slug')->get();

        return [
            'brands' => $brands,
            'categories' => $categories,
        ];
    }

    private function getAvailableFiltersFromProducts(iterable $products): array
    {
        $brandIds = [];
        $categoryIds = [];
        $prices = [];

        foreach ($products as $product) {
            if ($product->brand_id) {
                $brandIds[] = $product->brand_id;
            }
            if ($product->relationLoaded('categories')) {
                foreach ($product->categories as $category) {
                    $categoryIds[] = $category->id;
                }
            }
            if ($product->special_price !== null) {
                $prices[] = (float)$product->special_price;
            } elseif ($product->original_price !== null) {
                $prices[] = (float)$product->original_price;
            }
        }

        $uniqueBrandIds = array_values(array_unique($brandIds));
        $uniqueCategoryIds = array_values(array_unique($categoryIds));

        $brands = Brand::whereIn('id', $uniqueBrandIds)->select('id', 'name', 'slug')->get();
        $categories = Category::whereIn('id', $uniqueCategoryIds)->select('id', 'name', 'slug')->get();

        $priceRange = [];
        if (!empty($prices)) {
            $priceRange = ['min' => min($prices), 'max' => max($prices)];
        }

        return [
            'brands' => $brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'slug' => $b->slug]),
            'categories' => $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug]),
            'price_range' => $priceRange,
        ];
    }


    private function getProductImage(Product $product): ?string
    {
        if ($product->has_variant && $product->variants->isNotEmpty()) {
            $variant = $product->variants->first();

            if ($variant && $variant->optionValues && $variant->optionValues->isNotEmpty()) {
                $colorOption = $variant->optionValues->first(function ($optionValue) {
                    if (!$optionValue->option) {
                        return false;
                    }
                    return $optionValue->option->is_color ||
                           strtolower($optionValue->option->name) === 'color';
                });

                if ($colorOption) {
                    if ($colorOption->files && $colorOption->files->isNotEmpty()) {
                        $baseImageFile = $colorOption->files->whereIn('file_type', ['baseImage', 'base_image', 'image', 'main'])->first()
                                       ?? $colorOption->files->first();
                        if ($baseImageFile) {
                            return $baseImageFile->path . '/Thumbnail/' . $baseImageFile->temp_filename;
                        }
                    }
                }

                foreach ($variant->optionValues as $optionValue) {
                    if ($optionValue->files && $optionValue->files->isNotEmpty()) {
                        $variantImage = $optionValue->files->whereIn('file_type', ['baseImage', 'base_image', 'image', 'main'])->first();
                        if ($variantImage) {
                            return $variantImage->path . '/Thumbnail/' . $variantImage->temp_filename;
                        }
                        $firstImage = $optionValue->files->first();
                        if ($firstImage) {
                            return $firstImage->path . '/Thumbnail/' . $firstImage->temp_filename;
                        }
                    }
                }
            }

            if ($variant->files && $variant->files->isNotEmpty()) {
                $variantImage = $variant->files->whereIn('file_type', ['baseImage', 'base_image', 'image', 'main'])->first()
                              ?? $variant->files->first();
                if ($variantImage) {
                    return $variantImage->path . '/Thumbnail/' . $variantImage->temp_filename;
                }
            }
        }

        $baseImage = null;
        if (method_exists($product, 'filterFiles')) {
            $baseImage = $product->filterFiles('baseImage')->first() ?? $product->filterFiles('base_image')->first();
        }

        if (!$baseImage && $product->files && $product->files->isNotEmpty()) {
            $baseImage = $product->files->whereIn('file_type', ['baseImage', 'base_image', 'image', 'main'])->first()
                        ?? $product->files->first();
        }

        if ($baseImage) {
            return $baseImage->path . '/Thumbnail/' . $baseImage->temp_filename;
        }

        return null;
    }



    public function getPopularSearches(): array
    {
        return [
            'smartphone',
            'laptop',
            'headphones',
            'camera',
            'smartwatch',
            'gaming console',
        ];
    }
}
