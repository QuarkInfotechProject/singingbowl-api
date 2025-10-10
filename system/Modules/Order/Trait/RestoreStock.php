<?php

namespace Modules\Order\Trait;

use Modules\Order\App\Models\Order;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

trait RestoreStock
{
    function restoreStock(Order $order)
    {
        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->product->has_variant) {
                $productVariant = ProductVariant::find($orderItem->variant_id);
                if ($productVariant) {
                    $productVariant->increment('quantity', $orderItem->quantity);

                    if ($productVariant->quantity > 0) {
                        $productVariant->markAsInStock();
                    }
                } else {
                    throw new Exception("Product variant not found for order item ID: {$orderItem->id}", ErrorCode::NOT_FOUND);
                }
            } else {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->increment('quantity', $orderItem->quantity);

                    if ($product->quantity > 0) {
                        $product->markAsInStock();
                    }
                } else {
                    throw new Exception("Product not found for order item ID: {$orderItem->id}", ErrorCode::NOT_FOUND);
                }
            }
        }
    }
}
