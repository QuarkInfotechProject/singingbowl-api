<?php
namespace Modules\Others\Service\CategoriesTrending\User;

use Modules\Others\App\Models\CategoriesTrending;
use Modules\Product\App\Models\Product;
use Modules\Shared\Services\CacheService;

class CategoriesTrendingShowService
{
    public function __construct(private CacheService $cacheService)
    {
    }

    public function getById($id)
    {
        $cacheKey = $this->cacheService->getTrendingCategoriesDetailKey($id);
        $tags = ['trending-category:' . $id, 'trending-categories'];

        return $this->cacheService->remember(
            $cacheKey,
            function () use ($id) {
                return $this->fetchTrendingCategoryData($id);
            },
            $this->cacheService->getTrendingCategoriesDetailTtl(),
            $tags
        );
    }

    private function fetchTrendingCategoryData($id)
    {
        $trendingCategory = CategoriesTrending::query()
            ->where('id', $id)
            ->with([
                'category' => function ($query) {
                    $query->select('id', 'name', 'slug')
                        ->with('files');
                }
            ])
            ->select([
                'id',
                'category_id',
                'is_active as isActive',
                'sort_order as sortOrder'
            ])
            ->firstOrFail();

        $category = $trendingCategory->category;
        $categoryId = $trendingCategory->category_id;

        $result = [
            'id'          => $trendingCategory->id,
            'category_id' => $categoryId,
            'isActive'    => $trendingCategory->isActive,
            'sortOrder'   => $trendingCategory->sortOrder,
            'category'    => [
                'id'    => $category->id,
                'name'  => $category->name,
                'slug'  => $category->slug,
                'files' => $this->formatCategoryFiles($category),
            ],
        ];

        $result['products'] = $this->getProductsForCategory($categoryId);

        return $result;
    }

    private function formatCategoryFiles($category)
    {
        if (!$category || $category->files->isEmpty()) {
            return [];
        }
        if ($category->files->first()->type) {
            return $category->files->keyBy('type')
                ->map([$this, 'transformFile'])
                ->toArray();
        }

        $files = $category->files->values();
        $categoryFiles = [];

        if ($files->count() === 1) {
            $categoryFiles['logo'] = $this->transformFile($files->first());
        } elseif ($files->count() >= 2) {
            $categoryFiles['logo'] = $this->transformFile($files[0]);
            $categoryFiles['banner'] = $this->transformFile($files[1]);
        }

        return $categoryFiles;
    }

    private function getProductsForCategory($categoryId)
    {
        $products = Product::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('product_categories.category_id', $categoryId);
        })
            ->select([
                'id', 'uuid', 'product_name', 'slug', 'best_seller',
                'original_price', 'special_price', 'in_stock', 'quantity',
                'has_variant', 'new_from', 'new_to', 'created_at'
            ])
            ->with([
                'reviews:product_id,rating',
                'files' => function ($query) {
                    $query->select(['files.id', 'files.path', 'files.temp_filename'])->wherePivot('zone', 'baseImage');
                },
                'variants' => function ($query) {
                    $query->select(['product_variants.id', 'product_variants.product_id', 'product_variants.original_price', 'product_variants.special_price', 'product_variants.special_price_start', 'product_variants.special_price_end', 'product_variants.in_stock', 'product_variants.quantity'])
                        ->with([
                            'optionValues' => function ($subQuery) {
                                $subQuery->select(['product_option_values.id', 'product_option_values.product_option_id', 'product_option_values.option_name'])
                                    ->whereHas('option', function ($optionQuery) {
                                        $optionQuery->where('name', 'Color');
                                    })
                                    ->with([
                                        'files' => function ($fileQuery) {
                                            $fileQuery->select(['files.id', 'files.path', 'files.temp_filename'])->wherePivot('zone', 'baseImage');
                                        }
                                    ]);
                            }
                        ]);
                },
                'options:id,product_id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->take(9)
            ->get();

        return $products->map(function ($product) {
            $productVariant = $this->getOptimizedProductVariant($product);
            $reviewData = $this->calculateReviewData($product->reviews);

            return [
                'id'             => $product->id,
                'uuid'           => $product->uuid,
                'name'           => $product->product_name,
                'slug'           => $product->slug,
                'best_seller'    => (bool) $product->best_seller,
                'original_price' => $this->getPrice($product, $productVariant, 'original_price'),
                'special_price'  => $this->getPrice($product, $productVariant, 'special_price'),
                'in_stock'       => $this->getStock($product, $productVariant, 'in_stock'),
                'quantity'       => $this->getStock($product, $productVariant, 'quantity'),
                'hasVarient'     => (bool) $product->has_variant,
                'new_from'       => $product->new_from,
                'new_to'         => $product->new_to,
                'review_count'   => $reviewData['count'],
                'average_rating' => $reviewData['average'],
                'soldCount'      => $this->calculateSoldCount($product),
                'image'          => $this->getOptimizedImageUrl($product, $productVariant),
            ];
        })->toArray();
    }

    private function transformFile($file)
    {
        return [
            'id'  => $file->id,
            'url' => $file->url ?? null,
        ];
    }

    private function getOptimizedProductVariant($product)
    {
        if (!$product->has_variant || $product->variants->isEmpty()) {
            return null;
        }

        $colorOption = $product->options->firstWhere('name', 'Color');
        if ($colorOption) {
            $variant = $product->variants->first(function ($variant) use ($colorOption) {
                return $variant->optionValues->contains('product_option_id', $colorOption->id);
            });

            if ($variant) {
                return $variant;
            }
        }

        return $product->variants->first();
    }

    private function calculateReviewData($reviews)
    {
        if ($reviews->isEmpty()) {
            return ['count' => 0, 'average' => 0];
        }

        return [
            'count' => $reviews->count(),
            'average' => round($reviews->avg('rating'), 1)
        ];
    }

    private function getPrice($product, $variant, $priceType)
    {
        if ($product->has_variant && $variant) {
            return $variant->{$priceType};
        }
        return $product->{$priceType};
    }

    private function getStock($product, $variant, $stockType)
    {
        if ($product->has_variant && $variant) {
            return $variant->{$stockType};
        }
        return $product->{$stockType};
    }

    private function getOptimizedImageUrl($product, $variant = null)
    {
        if ($variant && $variant->optionValues->isNotEmpty()) {
            $optionValue = $variant->optionValues->first();
            if ($optionValue && $optionValue->files->isNotEmpty()) {
                $file = $optionValue->files->first();
                if ($file) {
                    return $file->url ?? null;
                }
            }
        }

        if ($product->files->isNotEmpty()) {
            $file = $product->files->first();
            return $file->url ?? null;
        }

        return null;
    }

    private function calculateSoldCount($product)
    {
        try {
            return $product->total_completed_sold ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}

