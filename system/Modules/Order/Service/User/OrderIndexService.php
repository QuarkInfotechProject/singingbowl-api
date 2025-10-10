<?php

namespace Modules\Order\Service\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderItem;

class OrderIndexService
{
    function index()
    {
        $userId = Auth::id();

        $orders = Order::where('user_id', $userId)
            ->latest()
            ->paginate(10);

        $orders->getCollection()->transform(function ($order) {
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            $totalItemsQuantity = $orderItems->sum('quantity');

            $orderStatus = $order->status;

            return [
                'id' => $order->id,
                'date' => Carbon::parse($order->created_at)->toDateString(),
                'status' => Order::$orderStatusMapping[$orderStatus],
                'total' => $order->total,
                'itemsCount' => $totalItemsQuantity
            ];
        });

        return $orders;
    }
}
