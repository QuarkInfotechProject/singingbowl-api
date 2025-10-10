<?php

namespace Modules\OrderProcessing\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderItem;
use Modules\OrderProcessing\App\Models\Refund;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class RefundProcessService
{
    function refundProcess($data, int $orderId)
    {
        $order = Order::with(['refunds', 'orderItems'])
            ->find($orderId);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        if ($data['refundAmount'] > $order->remaining_refundable_amount) {
            throw new Exception('Refund amount exceeds available amount.', ErrorCode::UNPROCESSABLE_CONTENT);
        }

        DB::beginTransaction();

        try {
            $refund = Refund::create([
                'order_id' => $order->id,
                'amount' => $data['refundAmount'],
                'reason' => $data['reason'],
                'restock_items' => $data['restockItems'],
            ]);

            $totalRefundedQuantity = 0;
            $totalOrderQuantity = $order->orderItems->sum('quantity');

            foreach ($data['items'] as $item) {
                $orderItem = $order->orderItems()->find($item['id']);

                if ($orderItem && $item['quantity'] > 0) {
                    $refundAmount = $item['amount'] * $item['quantity'];
                    $refund->orderItems()->attach($orderItem->id, [
                        'quantity' => $item['quantity'],
                        'amount' => $refundAmount,
                    ]);

                    if ($data['restockItems']) {
                        $this->restoreItem($orderItem, $item['quantity']);
                    }

                    $totalRefundedQuantity += $item['quantity'];
                }
            }

            $alreadyRefundedQuantity = 0;

            foreach ($order->refunds as $refund) {
                $alreadyRefundedQuantity += $refund->orderItems()->sum('order_item_refund.quantity');
            }

            $totalRefundedQuantity += $alreadyRefundedQuantity;

            $newStatus = $this->determineOrderStatus($order, $totalRefundedQuantity, $totalOrderQuantity);

            $oldStatus = $order->status;
            $order->update(['status' => $newStatus]);

            Event::dispatch(
                new OrderLogEvent(
                    "Order status changed from " . Order::$orderStatusMapping[$oldStatus] . " to "
                    . Order::$orderStatusMapping[$newStatus] . ".",
                    $order->id,
                    $data['modifierId'] ?? null,
                )
            );

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Error processing order refund: ' . $exception->getMessage());
            DB::rollBack();
            throw $exception;
        }
    }

    private function determineOrderStatus(Order $order, int $totalRefundedQuantity, int $totalOrderQuantity)
    {
        if ($totalRefundedQuantity >= $totalOrderQuantity) {
            return Order::AWAITING_REFUND;
        }

        if ($totalRefundedQuantity > 0) {
            return Order::PARTIALLY_REFUNDED;
        }

        return $order->status;
    }

    public function restoreItem(OrderItem $orderItem, $quantity)
    {
        if ($orderItem->product->has_variant) {
            $productVariant = ProductVariant::find($orderItem->variant_id);
            if ($productVariant) {
                $productVariant->increment('quantity', $quantity);

                if ($productVariant->quantity > 0) {
                    $productVariant->markAsInStock();
                }
            } else {
                throw new Exception("Product variant not found for order item ID: {$orderItem->id}", ErrorCode::NOT_FOUND);
            }
        } else {
            $product = Product::find($orderItem->product_id);
            if ($product) {
                $product->increment('quantity', $quantity);

                if ($product->quantity > 0) {
                    $product->markAsInStock();
                }
            } else {
                throw new Exception("Product not found for order item ID: {$orderItem->id}", ErrorCode::NOT_FOUND);
            }
        }
    }
}
