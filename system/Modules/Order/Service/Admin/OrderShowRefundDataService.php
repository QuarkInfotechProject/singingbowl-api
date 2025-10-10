<?php

namespace Modules\Order\Service\Admin;

use Modules\Order\App\Models\Order;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderShowRefundDataService
{
    function show(int $orderId)
    {
        $order = Order::with(['user:id,uuid,full_name,email', 'coupons', 'transaction', 'orderAddress.address', 'orderItems'])
            ->select('id', 'user_id', 'subtotal', 'discount', 'total', 'payment_method', 'status', 'created_at')
            ->find($orderId);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        if ($order->is_refunded) {
            return $this->getRefundData($order);
        };
    }

    private function getRefundData($order)
    {
        $orderRefunds = $order->refunds;

        $refundData = [];

        foreach ($orderRefunds as $orderRefund) {
            $refundDetails = [
                'refundedAt' => $orderRefund->created_at->format('Y-m-d \a\t h:i A'),
                'amount' => $orderRefund->amount,
                'reason' => $orderRefund->reason ?? '',
            ];

            $refundedItems = [];

            foreach ($orderRefund->orderItems as $refundedItem) {
                $item = [
                    'orderItemId' => $refundedItem->id,
                    'id' => $refundedItem->product->uuid,
                    'name' => $refundedItem->product->product_name,
                    'quantity' => $refundedItem->pivot->quantity,
                    'amount' => $refundedItem->pivot->amount,
                    'baseImage' => $this->getProductImage($refundedItem)
                ];

                if ($refundedItem->product->has_variant) {
                    $this->addVariantDetails($item, $refundedItem);
                }

                $refundedItems[] = $item;
            }

            $refundData[] = [
                'totalRefunded' => $order->total_refunded,
                'refundDetails' => $refundDetails,
                'refundedItems' => $refundedItems,
            ];
        }

        return $refundData;
    }

    private function getProductImage($orderedItem)
    {
        $file = $orderedItem->product->filterfiles('additionalImage')->first();
        return $file ? $file->path . '/Thumbnail/' . $file->temp_filename : null;
    }

    private function addVariantDetails(&$item, $orderedItem)
    {
        $variant = ProductVariant::find($orderedItem->variant_id);
        $optionValues = $variant->optionValues;

        // Get image from first option value if available
        if ($optionValues->isNotEmpty()) {
            $optionValue = $optionValues->first();
            $file = $optionValue->filterFiles('baseImage')->first();

            if ($file) {
                $item['baseImage'] = $file->path . '/Thumbnail/' . $file->temp_filename;
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
