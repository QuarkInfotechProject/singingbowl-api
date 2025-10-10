<?php

namespace Modules\OrderProcessing\Service;

use Carbon\Carbon;
use Modules\Order\App\Models\Order;

class OrderIndexForProcessingService
{
    function index($data)
    {
        $productName = $data['productName'] ?? null;
        $limit = $data['limit'] ?? 100;

        $ordersQuery = Order::with(['user', 'transaction', 'orderItems.product'])
        ->where('status', $data['status']);

        if ($productName) {
            $ordersQuery->whereHas('orderItems.product', function ($query) use ($productName) {
                $query->where('product_name', $productName);
            });
        }

        $orders = $ordersQuery->limit($limit)->get()
            ->transform(function ($order) {
                return [
                    'id' => $order->id,
                    'user' => $order->user->full_name,
                    'date' => $this->formatDate($order->created_at),
                    'status' => Order::$orderStatusMapping[$order->status],
                    'total' => $order->total,
                    'products' => $this->getProducts($order),
                    'isPaid' => $order->is_paid,
                ];
            });

        $allProductNames = $orders->flatMap(function ($order) {
            return collect($order['products'])->pluck('name');
        })->unique()->values()->toArray();

        return [
            'orders' => $orders->toArray(),
            'allProductNames' => $allProductNames
        ];
    }

    private function formatDate($date): string
    {
        $date = Carbon::parse($date);

        if ($date->isToday()) {
            return $date->diffForHumans();
        } else {
            return $date->isoFormat('D MMMM YYYY');
        }
    }

    private function getProducts($order)
    {
        return $order->orderItems->map(function ($orderItem) {
            return [
                'name' => $orderItem->product->product_name,
                'quantity' => $orderItem->quantity,
            ];
        })->toArray();
    }
}
