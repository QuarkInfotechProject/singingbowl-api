<?php

namespace Modules\Product\Service\Admin;

use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\Services\CacheService;
use Modules\Shared\StatusCode\ErrorCode;

class ProductShowVariantService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    function show(string $uuid)
    {
        $cacheKey = "product:variant:{$uuid}";
        // Find the product_id for tagging
        $variantForTag = ProductVariant::where('uuid', $uuid)->select('product_id')->first();
        if (!$variantForTag) {
            throw new Exception('Product variant not found.', ErrorCode::NOT_FOUND);
        }
        $cacheTags = ["product:{$variantForTag->product_id}"];

        return $this->cacheService->remember($cacheKey, function () use ($uuid) {
            $variant = ProductVariant::select(
                'name',
                'sku',
                'status',
                'original_price',
                'special_price',
                'special_price_start',
                'special_price_end',
                'quantity',
                'in_stock',
            )->where('uuid', $uuid)
                ->first();

            if (!$variant) {
                // This part of the code will not be reached if the variant is not found for the tag
                // but it is kept for safety.
                throw new Exception('Product variant not found.', ErrorCode::NOT_FOUND);
            }

            return [
                'name' => $variant->name,
                'sku' => $variant->sku,
                'status' => $variant->status,
                'originalPrice' => $variant->original_price,
                'specialPrice' => $variant->special_price ?? '',
                'specialPriceStart' => $variant->special_price_start ?? '',
                'specialPriceEnd' => $variant->special_price_end ?? '',
                'quantity' => $variant->quantity,
                'inStock' => $variant->in_stock,
            ];
        }, $this->cacheService->getProductDetailTtl(), $cacheTags);
    }
}
