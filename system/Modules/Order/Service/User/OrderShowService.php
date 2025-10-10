<?php

namespace Modules\Order\Service\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderAddress;
use Modules\Order\App\Models\OrderItem;
use Modules\Order\App\Models\OrderLog;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Constant\GatewayConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderShowService
{
    function show(int $id)
    {
        $userId = Auth::id();

        $order = Order::with(['orderAddress.address', 'orderItems', 'orderLog'])->select(
            'id',
            'subtotal',
            'discount',
            'total',
            'payment_method',
            'status',
            'created_at',
        )
            ->where('user_id', $userId)
            ->find($id);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        if ($order->orderAddress) {
            $addressInformation = [
                'name' => $order->orderAddress->address->first_name . ' ' . $order->orderAddress->address->last_name,
                'address' => $order->orderAddress->address->address,
                'mobile' => $order->orderAddress->address->mobile,
                'backupMobile' => $order->orderAddress->address->backup_mobile,
                'email' => Auth::user()->email,
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
                'lineTotal' => $orderedItem->line_total,
                'baseImage' => $this->getProductImage($orderedItem)
            ];

            if ($orderedItem->product->has_variant) {
                $this->addVariantDetails($item, $orderedItem);
            }

            $itemsOrdered[] = $item;
        }

        $orderInformation = [
            'id' => $order->id,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'total' => $order->total,
            'paymentMethod' => GatewayConstant::$gatewayMapping[$order->payment_method],
            'status' => Order::$orderStatusMapping[$order->status],
            'createdAt' => Carbon::parse($order->created_at)->toDateString(),
            'addressInformation' => $addressInformation,
            'itemsOrdered' => $itemsOrdered,
            'orderNote' => $this->getOrderNote($order),
        ];

        return $orderInformation;
    }

    private function getProductImage($orderedItem)
    {
        $file = $orderedItem->product->filterfiles('additionalImage')->first();
        return $file ? $file->path . '/' . $file->temp_filename : null;
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

    public function getOrderNote(Order $order)
    {
        $notes = $order->orderLog()->where('note_type', OrderLog::CUSTOMER_NOTE)->get();

        if ($notes->isEmpty()) {
            return [];
        }

        $noteData = [];
        foreach ($notes as $note) {
            $noteData[] = [
                'note' => $note->description,
                'createdAt' => $order->created_at->format('l jS \o\f F Y, h:ia')
            ];
        }

        return $noteData;
    }
}
