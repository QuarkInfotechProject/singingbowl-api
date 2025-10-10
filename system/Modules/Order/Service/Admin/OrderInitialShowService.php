<?php

namespace Modules\Order\Service\Admin;

use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderAddress;
use Modules\Order\App\Models\OrderItem;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Constant\GatewayConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderInitialShowService
{
    function show(int $id)
    {
        $order = Order::with(['user:id,email', 'transaction', 'orderAddress.address', 'orderItems'])
        ->select(
            'id',
            'user_id',
            'subtotal',
            'discount',
            'total',
            'note',
            'payment_method',
            'status',
        )->find($id);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        if ($order->orderAddress) {
            $addressInformation = [
                'name' => $order->orderAddress->address->first_name . ' ' . $order->orderAddress->address->last_name,
                'address' => $order->orderAddress->address->address,
                'mobile' => $order->orderAddress->address->mobile,
                'backupMobile' => $order->orderAddress->address->backup_mobile,
                'province' => $order->orderAddress->address->province_name,
                'city' => $order->orderAddress->address->city_name,
                'zone' => $order->orderAddress->address->zone_name,
            ];
        }

        foreach ($order->orderItems as $orderedItem) {
            $item = [
                'name' => $orderedItem->product->product_name,
                'slug' => $orderedItem->product->slug,
                'quantity' => $orderedItem->quantity,
                'lineTotal' => $orderedItem->line_total
            ];

            if ($orderedItem->product->has_variant) {
                $variant = $orderedItem->variant;
                $optionValues = $variant->optionValues;
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

            $itemsOrdered[] = $item;
        }

        if ($order->transaction) {
            $transactionCode = $order->transaction->transaction_code;
        }

        return [
            'id' => $order->id,
            'email' => $order->user->email,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'total' => $order->total,
            'note' => $order->note,
            'paymentMethod' => GatewayConstant::$gatewayMapping[$order->payment_method],
            'transactionCode' => $transactionCode ?? '',
            'status' => Order::$orderStatusMapping[$order->status],
            'addressInformation' => $addressInformation,
            'itemsOrdered' => $itemsOrdered,
        ];
    }
}
