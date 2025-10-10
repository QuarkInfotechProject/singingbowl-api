<?php

namespace Modules\Product\Service\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Category\App\Models\Category;
use Modules\Product\App\Models\Product;
use Modules\Product\Trait\GetBasicProductInformationTrait;

class ProductShopService
{
    use GetBasicProductInformationTrait;

    private const RESERVED_PARAMS = [
        'category', 'brands', 'min_price', 'max_price',
        'onSale', 'isNew', 'availability', 'sortBy',
        'perPage', 'page'
    ];

    private const DEFAULT_PER_PAGE = 12;
    private const SORT_OPTIONS = ['price_high_to_low', 'price_low_to_high'];

    public function index(Request $request)
    {
        $query = Product::active();

        $categoryName = $request->query('category');

        $categoryId = null;

        if ($categoryName) {
            $categoryId = Category::where('slug', $categoryName)->value('id');
        }

        $query = $this->applyFilters($query, $request, $categoryId);

        $categoriesWithImages = $categoryId ? $this->getFilteredCategoriesWithImages($categoryId) : [];

        try {
            $query = $this->applySorting($query, $request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $productsPaginator = $query->paginate($request->query('perPage', self::DEFAULT_PER_PAGE));

        $productData = $this->getBasicProductInformation($productsPaginator);

        $pagination = [
            'current_page' => $productsPaginator->currentPage(),
            'last_page'    => $productsPaginator->lastPage(),
            'per_page'     => $productsPaginator->perPage(),
            'total'        => $productsPaginator->total(),
            'next_page_url'=> $productsPaginator->nextPageUrl(),
            'prev_page_url'=> $productsPaginator->previousPageUrl(),
        ];

        return [
            'category' => $categoriesWithImages,
            'products' => $productData,
            'pagination' => $pagination,
        ];
    }

    private function applyFilters(Builder $query, Request $request, $categoryId): Builder
    {
        $this->applyOnSaleFilter($query, $request);
        $this->applyNewProductFilter($query, $request);
        $this->applyAvailabilityFilter($query, $request);
        $this->applyCategoryFilter($query, $categoryId);
        $this->applyPriceRangeFilter($query, $request);
        $this->applyAttributeFilters($query, $request);
        $this->applyBrandFilter($query, $request);

        return $query;
    }

    private function applyBrandFilter(Builder &$query, Request $request): void
    {
        $brandSlugs = $request->input('brands');

        if (is_string($brandSlugs)) {
            $brandSlugs = explode(',', $brandSlugs);
        }

        if (is_array($brandSlugs) && !empty($brandSlugs)) {

            $validatedSlugs = collect($brandSlugs)->map(function ($slug) {
                return is_string($slug) ? trim($slug) : null;
            })->filter()->toArray();

            if (!empty($validatedSlugs)) {
                $query->whereHas('brand', function ($q) use ($validatedSlugs) {
                    $q->whereIn('slug', $validatedSlugs);
                });
            }
        }
    }


    private function applyPriceRangeFilter(Builder &$query, Request $request): void
    {
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $hasMin = is_numeric($minPrice);
        $hasMax = is_numeric($maxPrice);

        if ($hasMin || $hasMax) {
            $priceSubquery = $this->getPriceSubquery();

            if ($hasMin && $hasMax && $maxPrice >= $minPrice) {
                $query->whereRaw("({$priceSubquery}) BETWEEN ? AND ?", [$minPrice, $maxPrice]);
            } elseif ($hasMin) {
                $query->whereRaw("({$priceSubquery}) >= ?", [$minPrice]);
            } elseif ($hasMax) {
                $query->whereRaw("({$priceSubquery}) <= ?", [$maxPrice]);
            }
        }
    }

    private function applyAttributeFilters(Builder &$query, Request $request): void
    {
        // Get all request parameters excluding reserved system parameters
        $allParams = $request->all();

        foreach ($allParams as $param => $value) {
            if (!in_array($param, self::RESERVED_PARAMS) && !empty($value) && is_string($value)) {
                $this->applyAttributeFilter($query, $param, $value);
            }
        }
    }

    private function applyAttributeFilter(Builder &$query, string $attributeName, string $attributeValue): void
    {
        $query->whereHas('attributes.attribute', function ($q) use ($attributeName) {
            $q->where('name', 'LIKE', '%' . str_replace('_', ' ', $attributeName) . '%');
        })->whereHas('attributes.values.attributeValue', function ($q) use ($attributeValue) {
            $q->where('value', 'LIKE', '%' . $attributeValue . '%');
        });
    }

    private function applyCategoryFilter(Builder &$query, $categoryId): void
    {
        if (!empty($categoryId)) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }
    }

    private function applyOnSaleFilter(Builder &$query, Request $request): void
    {
        if ($request->has('onSale')) {
            $onSale = filter_var($request->query('onSale'), FILTER_VALIDATE_BOOLEAN);
            $query->when($onSale, function ($q) {
                $q->whereDate('sale_start', '<=', now())
                    ->whereDate('sale_end', '>=', now());
            }, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->whereNull('sale_start')
                        ->orWhere('sale_start', '>', now())
                        ->orWhereNull('sale_end')
                        ->orWhere('sale_end', '<', now());
                });
            });
        }
    }

    private function applyNewProductFilter(Builder &$query, Request $request): void
    {
        if ($request->has('isNew')) {
            $isNew = filter_var($request->query('isNew'), FILTER_VALIDATE_BOOLEAN);
            $query->when($isNew, function ($q) {
                $q->whereDate('new_from', '<=', now())
                    ->whereDate('new_to', '>=', now());
            }, function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->whereNull('new_from')
                        ->orWhere('new_from', '>', now())
                        ->orWhereNull('new_to')
                        ->orWhere('new_to', '<', now());
                });
            });
        }
    }

    private function applyAvailabilityFilter(Builder &$query, Request $request): void
    {
        if ($request->has('availability')) {
            match ($request->query('availability')) {
                'in_stock' => $query->inStock(),
                'out_of_stock' => $query->outOfStock(),
                default => $query,
            };
        }
    }

    private function applySorting(Builder $query, Request $request): Builder
    {
        $sortBy = $request->query('sortBy');

        if (in_array($sortBy, self::SORT_OPTIONS)) {
            $query->selectRaw("products.*, {$this->getPriceSubquery()} AS calculated_price");

            return match ($sortBy) {
                'price_high_to_low' => $query->orderBy('calculated_price', 'desc'),
                'price_low_to_high' => $query->orderBy('calculated_price', 'asc'),
                default => $query,
            };
        }

        return match ($sortBy) {
            'date_old_to_new' => $query->orderBy('created_at', 'asc'),
            'date_new_to_old' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('sort_order', 'asc'),
        };
    }

    private function getPriceSubquery()
    {
        return "
        CASE
            WHEN has_variant = false THEN
                IF(
                    special_price IS NOT NULL
                    AND NOW() BETWEEN special_price_start AND special_price_end,
                    special_price,
                    original_price
                )
            ELSE
                (
                    SELECT MIN(
                        IF(
                            pv.special_price IS NOT NULL
                            AND NOW() BETWEEN pv.special_price_start AND pv.special_price_end,
                            pv.special_price,
                            pv.original_price
                        )
                    )
                    FROM product_variants pv
                    WHERE pv.product_id = products.id
                )
        END
    ";
    }

    function getFilteredCategoriesWithImages($categoryId)
    {
        $category = Category::where('id', $categoryId)
            ->select('id', 'name')
            ->where('is_active', true)
            ->where('is_displayed', true)
            ->first();

        if (!$category) {
            return null;
        }

        $banner = $category->filterFiles('banner')->first();

        return [
            'id' => $category->id,
            'name' => $category->name,
            'banner' => $banner ? $banner->url : null,
        ];
    }
}
