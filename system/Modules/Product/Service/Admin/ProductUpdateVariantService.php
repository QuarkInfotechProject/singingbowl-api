<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Product\App\Events\ProductUpdated;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;
use Modules\Product\DTO\ProductUpdateVariantDTO;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductUpdateVariantService
{
    function update(ProductUpdateVariantDTO $productUpdateVariantDTO)
    {
        try {
            DB::beginTransaction();

            $product = Product::where('uuid', $productUpdateVariantDTO->productUuid)->first();

            if (!$product) {
                throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
            }

            $variant = ProductVariant::where('product_id', $product->id)
                ->where('uuid', $productUpdateVariantDTO->variantUuid)
                ->first();

            if (!$variant) {
                throw new Exception('Product variant not found.', ErrorCode::NOT_FOUND);
            }

            $variant->update([
                'status' => $productUpdateVariantDTO->status,
                'original_price' => $productUpdateVariantDTO->originalPrice,
                'special_price' => $productUpdateVariantDTO->specialPrice,
                'special_price_start' => $productUpdateVariantDTO->specialPriceStart,
                'special_price_end' => $productUpdateVariantDTO->specialPriceEnd,
                'quantity' => $productUpdateVariantDTO->quantity,
                'in_stock' => $productUpdateVariantDTO->inStock,
            ]);

            DB::commit();

            // Dispatch ProductUpdated event to trigger cache invalidation
            Event::dispatch(new ProductUpdated($product));
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error updating product variant: ' . $exception->getMessage(), [
                'exception' => $exception,
                'product_uuid' => $productUpdateVariantDTO->productUuid,
                'variant_uuid' => $productUpdateVariantDTO->variantUuid,
            ]);
            throw $exception;
        }
    }
}
