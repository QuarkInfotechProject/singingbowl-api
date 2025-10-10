<?php

namespace Modules\Product\Service\User;

use Carbon\Carbon; // Added Carbon for date operations
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\App\Models\Product;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductGetRelatedProductService
{
    // Score weight constants
    private const CATEGORY_WEIGHT = 30;
    private const ATTRIBUTE_WEIGHT = 50;
    private const PRICE_WEIGHT = 20;

    // Price range constants
    private const PRICE_LOWER_RATIO = 0.3;
    private const PRICE_UPPER_RATIO = 1.7;

    public function getRelatedProducts(string $slug, int $maxResults = 10): array
    {
        if ($maxResults <= 0) {
            return [];
        }

        $product = $this->getProduct($slug);

        if ($product->categories->isEmpty()) {
            return [];
        }

        $categoryIds = $product->categories->pluck('id')->toArray();
        $productAttributes = $product->attributes?->pluck('attribute_id')->toArray() ?? [];
        $originalPrice = (float) $product->original_price;

        $relatedProducts = $this->fetchRelatedProducts($product, $categoryIds, $originalPrice, $maxResults);

        if ($relatedProducts->isEmpty()) {
            return [];
        }

        $formattedProducts = $relatedProducts->map(function ($relatedProduct) use ($product, $categoryIds, $productAttributes) {
            $matchingScore = $this->calculateMatchingScore($relatedProduct, $product, $categoryIds, $productAttributes);
            $reviewCount = $relatedProduct->reviews->count();
            $rating = $relatedProduct->reviews->avg('rating');

            $currentOriginalPrice = null;
            $currentSpecialPrice = null;

            if ($relatedProduct->has_variant && $relatedProduct->relationLoaded('variants') && $relatedProduct->variants->isNotEmpty()) {
                // NOTE: For products with variants, prices are currently sourced from the *first* variant.
                // If a different variant's price is considered more "accurate" or representative (e.g., lowest price),
                // this logic would need to be adjusted.
                $variant = $relatedProduct->variants->first();
                if ($variant) {
                    $currentOriginalPrice = (float) $variant->original_price;
                    $currentSpecialPrice = $this->getValidatedSpecialPrice($variant);
                } else {
                    // Fallback if variants collection is empty despite has_variant = true
                    $currentOriginalPrice = (float) $relatedProduct->original_price;
                    $currentSpecialPrice = $this->getValidatedSpecialPrice($relatedProduct);
                }
            } else {
                // Product without variants or variants not loaded/empty
                $currentOriginalPrice = (float) $relatedProduct->original_price;
                $currentSpecialPrice = $this->getValidatedSpecialPrice($relatedProduct);
            }

            return [
                'name'          => $relatedProduct->product_name,
                'slug'          => $relatedProduct->slug,
                'originalPrice' => $currentOriginalPrice,
                'specialPrice'  => $currentSpecialPrice,
                'baseImage'     => $this->findImage($relatedProduct),
                'reviewCount'   => $reviewCount,
                'rating'        => $rating ? round($rating, 2) : null,
                'bestSeller'    => (bool)$relatedProduct->best_seller,

            ];
        })->sortByDesc('matchingScore')
            ->take($maxResults)
            ->values()
            ->all();

        return $formattedProducts;
    }

    private function fetchRelatedProducts(Product $product, array $categoryIds, float $originalPrice, int $maxResults): Collection
    {
        $query = Product::with([
            'categories',
            'attributes',
            'files',
            'reviews',
            'options', // Eager load options
            'variants.optionValues.files' // Eager load variants, their option values, and their files
        ])
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds));

        if ($originalPrice > 0) {
            $priceBounds = [
                max(0, $originalPrice * self::PRICE_LOWER_RATIO),
                $originalPrice * self::PRICE_UPPER_RATIO,
            ];

            $priceFilteredProducts = (clone $query)
                ->whereBetween('original_price', $priceBounds)
                ->limit($maxResults * 2)
                ->get();

            if ($priceFilteredProducts->count() >= $maxResults) {
                return $priceFilteredProducts;
            }
        }

        return $query->limit($maxResults * 2)->get();
    }

    private function calculateMatchingScore($relatedProduct, $product, array $categoryIds, array $productAttributes): float
    {
        // Calculate category matching score
        $relatedCategoryIds = $relatedProduct->categories->pluck('id')->toArray();
        $categoryMatchCount = count(array_intersect($relatedCategoryIds, $categoryIds));
        $categoryScore = $categoryMatchCount * self::CATEGORY_WEIGHT;

        // Calculate attribute matching score
        $relatedAttributes = $relatedProduct->attributes->pluck('attribute_id')->toArray();
        $attributeScore = !empty($productAttributes)
            ? round((count(array_intersect($relatedAttributes, $productAttributes)) / count($productAttributes)) * self::ATTRIBUTE_WEIGHT, 2)
            : 0;

        // Calculate price matching score
        $priceScore = $product->original_price > 0
            ? (1 - abs($relatedProduct->original_price - $product->original_price) / $product->original_price) * self::PRICE_WEIGHT
            : 0;

        return $categoryScore + $attributeScore + $priceScore;
    }

    private function getValidatedSpecialPrice($entity): ?float
    {
        if (!$entity || !isset($entity->special_price) || $entity->special_price <= 0) {
            return null;
        }

        $now = Carbon::now();
        $startDateString = $entity->special_price_start ?? null;
        $endDateString = $entity->special_price_end ?? null;

        $startDate = $startDateString ? Carbon::parse($startDateString) : null;
        $endDate = $endDateString ? Carbon::parse($endDateString) : null;

        if ((!$startDate || $now->gte($startDate)) && (!$endDate || $now->lte($endDate))) {
            return (float) $entity->special_price;
        }

        return null;
    }

    private function formatImagePath($file): string // Helper to construct image URL
    {
        if ($file && isset($file->path) && isset($file->temp_filename)) {
            // Assuming 'Thumbnail' is the desired size. This could be a parameter if needed.
            return "{$file->path}/Thumbnail/{$file->temp_filename}";
        }
        return '';
    }

    private function findVariantImage($product): string
    {
        // Assumes 'options', 'variants', 'variants.optionValues', 'variants.optionValues.files' are eager loaded.
        if ($product->options->isEmpty() || $product->variants->isEmpty()) {
            return '';
        }

        $colorOption = $product->options->firstWhere('name', 'Color');
        if (!$colorOption) {
            return ''; // No "Color" option found for this product.
        }

        $productOptionId = $colorOption->id;

        foreach ($product->variants as $variant) {
            if ($variant->optionValues->isEmpty()) {
                continue;
            }

            $variantColorOptionValue = $variant->optionValues->firstWhere('product_option_id', $productOptionId);

            if ($variantColorOptionValue) {
                // 1. Try ProductOptionValue->baseImage() method (preferred)
                if (method_exists($variantColorOptionValue, 'baseImage')) {
                    $file = $variantColorOptionValue->baseImage();
                    $imageUrl = $this->formatImagePath($file);
                    if (!empty($imageUrl)) return $imageUrl;
                }

                // 2. Fallback: Check 'files' relation on ProductOptionValue for 'baseImage' zone
                if ($variantColorOptionValue->files->isNotEmpty()) {
                    $file = $variantColorOptionValue->files->first(function ($f) {
                        return isset($f->pivot) && $f->pivot->zone === 'baseImage';
                    });
                    $imageUrl = $this->formatImagePath($file);
                    if (!empty($imageUrl)) return $imageUrl;
                }
            }
        }
        return ''; // No suitable variant image found
    }

    private function findProductImage($product): string
    {
        // Assumes 'files' relation is eager loaded on $product.

        // 1. Try Product->baseImage() method (if defined, preferred)
        if (method_exists($product, 'baseImage')) {
            $file = $product->baseImage();
            $imageUrl = $this->formatImagePath($file);
            if (!empty($imageUrl)) return $imageUrl;
        }

        // 2. Fallback: Check 'files' relation on Product for 'baseImage' zone
        if ($product->files->isNotEmpty()) {
            $file = $product->files->first(function ($f) {
                return isset($f->pivot) && $f->pivot->zone === 'baseImage';
            });
            $imageUrl = $this->formatImagePath($file);
            if (!empty($imageUrl)) return $imageUrl;

            // 3. Final fallback: Get the first file if no 'baseImage' zone found
            $file = $product->files->first();
            $imageUrl = $this->formatImagePath($file);
            if (!empty($imageUrl)) return $imageUrl;
        }
        return ''; // No suitable product image found
    }

    private function findImage($relatedProduct): string
    {
        $baseImageUrl = '';

        // Prioritize variant image if the product has variants
        if ($relatedProduct->has_variant) {
            $baseImageUrl = $this->findVariantImage($relatedProduct);
        }

        // If no variant image is found (or product has no variants), try the product's own image
        if (empty($baseImageUrl)) {
            $baseImageUrl = $this->findProductImage($relatedProduct);
        }

        return $baseImageUrl;
    }

    private function getProduct(string $slug): Product
    {
        $product = Product::with(['categories', 'attributes', 'files'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->first();

        if (!$product) {
            throw new Exception('Product not found', ErrorCode::NOT_FOUND);
        }

        return $product;
    }
}
