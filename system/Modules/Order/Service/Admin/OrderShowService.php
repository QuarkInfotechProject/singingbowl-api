<?php

namespace Modules\Order\Service\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderLog;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Constant\GatewayConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderShowService
{
    public function show(int $id)
    {
        $order = $this->getOrder($id);

        return [
            'id' => $order->id,
            'isRefunded' => $order->is_refunded,
            'refundedAmount' => $order->total_refunded,
            'availableToRefund' => $order->remaining_refundable_amount,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'total' => $order->total,
            'note' => $order->note,
            'paid' => $order->is_paid,
            'totalPaid' => $this->getTotalPaid($order),
            'paymentMethod' => GatewayConstant::$gatewayMapping[$order->payment_method],
            'status' => Order::$orderStatusMapping[$order->status],
            'createdAt' => $this->formatDate($order->created_at),
            'transactionDetails' => $this->getTransactionDetails($order),
            'userDetails' => $this->getUserDetails($order),
            'addressInformation' => $this->getAddressInformation($order),
            'itemsOrdered' => $this->getItemsOrdered($order),
            'couponData' => $this->getCouponData($order),
            'orderInvoiceDownloadLink' => $this->getOrderInvoice($id)
        ];
    }

    private function getTotalPaid(Order $order)
    {
        if ($order->payment_method === 'cod' && $order->status === Order::DELIVERED) {
            return $order->total;
        }

        return $order->is_paid === 'Yes' ? $order->total : 0;
    }

    private function getOrder(int $id): Order
    {
        $order = Order::with(['user:id,uuid,full_name,email', 'coupons', 'transaction', 'orderAddress.address', 'orderItems'])
            ->select('id', 'user_id', 'subtotal', 'discount', 'total', 'note', 'payment_method', 'status', 'created_at')
            ->find($id);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        return $order;
    }

    private function formatDate($date): string
    {
        return Carbon::parse($date)->format('Y-m-d @ h:i A');
    }

    private function getTransactionDetails(Order $order): array
    {
        $transaction = $order->transaction;

        return [
            'paymentMethod' => GatewayConstant::$gatewayMapping[$transaction->payment_method ?? $order->payment_method],
            'transactionId' => $transaction->transaction_code ?? null,
            'initiatedDate' => $transaction ? $this->formatDate($transaction->created_at) : null,
        ];
    }

    private function getUserDetails(Order $order): ?array
    {
        if (!$order->user) {
            return null;
        }

        $deliveredOrders = $order->where('user_id', $order->user->id)
            ->where('status', Order::DELIVERED);

        return [
            'name' => $order->user->full_name,
            'userId' => $order->user->uuid,
            'email' => $order->user->email,
            'totalOrders' => $deliveredOrders->count(),
            'totalRevenue' => number_format($deliveredOrders->sum('total'), 2),
            'averageOrderValue' => number_format($deliveredOrders->avg('total'), 2)
        ];
    }

    private function getAddressInformation(Order $order): ?array
    {
        if (!$order->orderAddress) {
            return null;
        }

        return [
            'firstName' => $order->orderAddress->address->first_name,
            'lastName' => $order->orderAddress->address->last_name,
            'address' => $order->orderAddress->address->address,
            'mobile' => $order->orderAddress->address->mobile,
            'backupMobile' => $order->orderAddress->address->backup_mobile ?? '',
            'provinceId' => $order->orderAddress->address->province_id,
            'provinceName' => $order->orderAddress->address->province_name,
            'cityId' => $order->orderAddress->address->city_id,
            'cityName' => $order->orderAddress->address->city_name,
            'zoneId' => $order->orderAddress->address->zone_id,
            'zoneName' => $order->orderAddress->address->zone_name,
            'countryName' => $order->orderAddress->address->country_name,
        ];
    }

    private function getItemsOrdered(Order $order): array
    {
        $itemsOrdered = [];

        foreach ($order->orderItems as $orderedItem) {
            $item = [
                'orderItemId' => $orderedItem->id,
                'id' => $orderedItem->product->uuid,
                'name' => $orderedItem->product->product_name,
                'unitPrice' => $orderedItem->unit_price,
                'quantity' => $orderedItem->quantity,
                'lineTotal' => $orderedItem->line_total,
                'baseImage' => $this->getProductImage($orderedItem)
            ];

            if ($orderedItem->product->has_variant) {
                $this->addVariantDetails($item, $orderedItem);
            }

            $itemsOrdered[] = $item;
        }

        return $itemsOrdered;
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
            $file = $optionValue->filterFiles('additionalImage')->first();

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
                $item["optionData{$optionNumber}"] = $matchingOptionValue->option_data ?? '';
            } else {
                $item["optionValue{$optionNumber}"] = '';
                if ($optionNumber === 1) {
                    $item["optionData{$optionNumber}"] = '';
                }
            }
        }
    }

    private function getCouponData(Order $order): ?array
    {
        if (!$order->coupons) {
            return null;
        }

        return $order->coupons->map(function ($coupon) use ($order) {
            $discountAmount = $this->calculateDiscountAmount($coupon, $order->subtotal);

            return [
                'couponCode' => $coupon->code,
                'value' => $coupon->value,
                'discountAmount' => $discountAmount,
                'isPercent' => $coupon->isPercentageType(),
            ];
        })->toArray();
    }

    private function calculateDiscountAmount($coupon, $subTotal): float
    {
        $discountAmount = $coupon->isPercentageType()
            ? ($subTotal * $coupon->value / 100)
            : $coupon->value;

        return $coupon->max_discount
            ? min($discountAmount, $coupon->max_discount)
            : $discountAmount;
    }

    private function getOrderInvoice(int $id)
    {
        try {
            $filePath = 'modules/order/invoices/invoice_' . $id . '.pdf';
            if (File::exists($filePath)) {
                $orderInvoiceDownloadLink = asset($filePath);
            } else {
                throw new Exception('Invoice file not found.', ErrorCode::NOT_FOUND);
            }
        } catch (Exception $exception) {
            Log::error('Invoice download link error: ' . $exception->getMessage());
            $orderInvoiceDownloadLink = 'Invoice is currently unavailable. Please try again later.';
        }

        return $orderInvoiceDownloadLink;
    }
}
