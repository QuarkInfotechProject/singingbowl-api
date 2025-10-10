<?php
namespace Modules\Others\Service\NewArrival\User;

use Illuminate\Http\Request;
use Modules\Category\App\Models\Category;
use Modules\Product\App\Models\Product;
use Carbon\Carbon;

class NewArrivalShowService
{
    /**
     * Get new arrival products by category with pagination.
     *
     * @param Request $request
     * @return array
     */
    public function getProductsByCategory(Request $request): array
    {
        $slug = $request->query('slug');
        $perPage = $request->input('per_page', 6);

        if (!$slug || !Category::query()->where([
            ['slug', $slug],
            ['is_active', true],
            ['show_in_new_arrivals', true]
        ])->exists()) {
            return [
                'products' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage,
                    'total' => 0,
                ],
            ];
        }

        $now = Carbon::now();
        $products = Product::query()
            ->whereHas('categories', fn($query) => $query->where('categories.slug', $slug))
            ->where('status', true)
            ->where(function ($query) use ($now) {
                $query->where('new_from', '<=', $now)
                    ->where(fn($subQuery) => $subQuery->whereNull('new_to')->orWhere('new_to', '>=', $now));
            })
            ->with([
                'files',
                'reviews' => function ($query) {
                    $query->where('type', 'review')
                          ->where('is_approved', true);
                },
                'variants' => function ($query) {
                    $query->select(['id', 'product_id', 'original_price', 'special_price', 'special_price_start', 'special_price_end', 'in_stock', 'quantity'])
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
            ->select(['id','uuid', 'product_name', 'slug', 'original_price', 'best_seller', 'special_price', 'special_price_start', 'special_price_end', 'status', 'in_stock', 'quantity', 'sort_order', 'has_variant'])
            ->orderBy('sort_order', 'asc')
            ->paginate($perPage);

        return $this->formatResponse($products);
    }

    private function formatResponse($products): array
    {
        return [
            'products' => collect($products->items())->map(function($product) {
                $productVariant = $this->getOptimizedProductVariant($product);

                $validatedSpecialPrice = $this->calculateDiscountPrice($product, $productVariant);

                return [
                    'uuid' => $product->uuid,
                    'name' => $product->product_name,
                    'slug' => $product->slug,
                    'original_price' => $this->getPrice($product, $productVariant, 'original_price'),
                    'special_price' => $validatedSpecialPrice,
                    'discount_percentage' => $this->calculateDiscountPercentage($product, $productVariant, $validatedSpecialPrice),
                    'in_stock' => $this->getStock($product, $productVariant, 'in_stock'),
                    'quantity' => $this->getStock($product, $productVariant, 'quantity'),
                    'best_seller' => $product->best_seller,
                    'review_average' => $this->calculateReviewData($product->reviews)['average'],
                    'review_count' => $this->calculateReviewData($product->reviews)['count'],
                    'soldCount' => $product->total_completed_sold ?? 0,
                    'image' => $this->getOptimizedImageUrl($product, $productVariant),
                ];
            })->toArray(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ];
    }

    /**
     * Calculate the effective discount price for a product
     */
    private function calculateDiscountPrice($product, $variant = null): ?float
    {
        $now = Carbon::now();
        $specialPrice = $this->getPrice($product, $variant, 'special_price');
        $specialPriceStart = $product->special_price_start ?? $variant?->special_price_start;
        $specialPriceEnd = $product->special_price_end ?? $variant?->special_price_end;

        // Check if special price is valid and active
        if ($specialPrice > 0 &&
            (!$specialPriceStart || $now->gte($specialPriceStart)) &&
            (!$specialPriceEnd || $now->lte($specialPriceEnd))) {
            return (float) $specialPrice;
        }

        return null;
    }

    /**
     * Calculate the discount percentage for a product
     */
    private function calculateDiscountPercentage($product, $variant = null, $validatedSpecialPrice = null): int
    {
        $discountPrice = $validatedSpecialPrice ?? $this->calculateDiscountPrice($product, $variant);
        $originalPrice = $this->getPrice($product, $variant, 'original_price');

        if ($discountPrice && $originalPrice && $originalPrice > $discountPrice) {
            $difference = $originalPrice - $discountPrice;
            return (int) round(($difference / $originalPrice) * 100);
        }

        return 0;
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
}
