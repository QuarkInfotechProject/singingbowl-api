<?php

namespace Modules\Product\Service\User;

use Illuminate\Support\Facades\Auth;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderItem;
use Modules\Product\App\Models\ProductVariant;
use Modules\Review\App\Models\Review;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductPurchaseIndexService
{
    function index()
    {
        $userId = Auth::id();

        if (!$userId) {
            throw new Exception('User not logged in.', ErrorCode::UNAUTHORIZED);
        }

        $purchasedProducts = [];

        $completedOrders = Order::where('user_id', $userId)
            ->where('status', Order::DELIVERED)
            ->withTrashed()
            ->get();

        foreach ($completedOrders as $completedOrder) {
            $purchasedItems = OrderItem::where('order_id', $completedOrder->id)
                ->withTrashed()
                ->get();

            foreach ($purchasedItems as $purchasedItem) {
                $item = [
                    'orderItemId' => $purchasedItem->id,
                    'productId' => $purchasedItem->product->uuid,
                    'name' => $purchasedItem->product->product_name,
                    'slug' => $purchasedItem->product->slug,
                    'quantity' => $purchasedItem->quantity,
                    'unitPrice' => $purchasedItem->unit_price,
                    'lineTotal' => $purchasedItem->line_total,
                    'baseImage' => $this->getProductImage($purchasedItem),
                    'isReviewed' => $purchasedItem->is_reviewed,
                ];

                $review = Review::where('product_id', $purchasedItem->product_id)
                    ->where('user_id', $userId)
                    ->first();

                if ($review) {
                    $item['rating'] = $review->rating;
                }

                if ($purchasedItem->product->has_variant) {
                    $this->addVariantDetails($item, $purchasedItem);
                }

                $purchasedProducts[] = $item;
            }
        }

        return $purchasedProducts;
    }

    private function getProductImage($orderedItem)
    {
        $file = $orderedItem->product->filterfiles('additionalImage')->first();
        return $file ? $file->path . '/' . $file->temp_filename : null;
    }

    private function addVariantDetails(&$item, $orderedItem)
    {
        $variant = ProductVariant::find($orderedItem->variant_id);

        if ($variant) {
            $optionValues = $variant->optionValues;

            // Get image from first option value if available
            if ($optionValues->isNotEmpty()) {
                $optionValue = $optionValues->first();
                $file = $optionValue->filterFiles('baseImage')->first();

                if ($file) {
                    $item['baseImage'] = $file->path . '/' . $file->temp_filename;
                }
            }

            // Get option details from pivot table relationship
            $productOptions = $orderedItem->product->options;

            foreach ($productOptions as $index => $productOption) {
                $optionNumber = $index + 1;
                $item["optionName{$optionNumber}"] = $productOption->name;

                // Find matching option value for this option
                $matchingOptionValue = $optionValues->first(function ($optionValue) use ($productOption) {
                    return $optionValue->product_option_id === $productOption->id;
                });

                if ($matchingOptionValue) {
                    $item["optionValue{$optionNumber}"] = $matchingOptionValue->option_name;
                    if ($optionNumber === 1) {
                        $item["optionData{$optionNumber}"] = $matchingOptionValue->option_data ?? '';
                    }
                } else {
                    $item["optionValue{$optionNumber}"] = '';
                    if ($optionNumber === 1) {
                        $item["optionData{$optionNumber}"] = '';
                    }
                }
            }
        }
    }
}
