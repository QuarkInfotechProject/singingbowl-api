<?php
namespace Modules\Others\Service\LimitedTimeDeals\User;

use Modules\Others\App\Models\LimitedTimeDeals;
use Carbon\Carbon;

class LimitedTimeDealIndexService
{
    public function getAll()
    {
        $now = Carbon::now();

        $deals = LimitedTimeDeals::with([
                'product',
                'product.files',
                'product.reviews' => function ($query) {
                    $query->where('type', 'review')
                          ->where('is_approved', true);
                },
                'product.variants' => function ($query) {
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
                'product.options:id,product_id,name'
            ])
            ->where('status', true)
            ->whereHas('product', function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('special_price_end')
                      ->orWhere('special_price_end', '>=', $now);
                });
            })
            ->orderBy('sort_order', 'asc')
            ->get();

        return [
            'data' => $deals->map(function ($deal) {
                $product = $deal->product;
                $productVariant = $this->getOptimizedProductVariant($product);

                return [
                    'id' => $deal->id,
                    'product_uuid' => $product->uuid,
                    'product_name' => $product->product_name,
                    'product_slug' => $product->slug,
                    'original_price' => $this->getPrice($product, $productVariant, 'original_price'),
                    'special_price' => $this->getPrice($product, $productVariant, 'special_price'),
                    'discount_percentage' => $this->calculateDiscountPercentage($product, $productVariant),
                    'special_price_start_date' => $this->getSpecialPriceDate($product, $productVariant, 'special_price_start'),
                    'special_price_end_date' => $this->getSpecialPriceDate($product, $productVariant, 'special_price_end'),
                    'review_average' => $product->reviews->avg('rating') ?? 0,
                    'review_count' => $product->reviews->count(),
                    'soldCount' => $product->total_completed_sold ?? 0,
                    'in_stock' => $this->getStock($product, $productVariant, 'in_stock'),
                    'quantity' => $this->getStock($product, $productVariant, 'quantity'),
                    'status' => $deal->status,
                    'best_seller' => $product->best_seller,
                    'sort_order' => $deal->sort_order,
                    'image' => $this->getOptimizedImageUrl($product, $productVariant),
                ];
            })
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

    private function getSpecialPriceDate($product, $variant, $dateType)
    {
        if ($product->has_variant && $variant) {
            return $variant->{$dateType};
        }
        return $product->{$dateType};
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

    private function calculateDiscountPercentage($product, $variant = null): int
    {
        $originalPrice = $this->getPrice($product, $variant, 'original_price');
        $specialPrice = $this->getPrice($product, $variant, 'special_price');

        if ($specialPrice && $originalPrice && $originalPrice > $specialPrice) {
            $difference = $originalPrice - $specialPrice;
            return (int) round(($difference / $originalPrice) * 100);
        }

        return 0;
    }
}