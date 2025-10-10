<?php

namespace Modules\Order\Service\Admin;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Modules\Order\App\Models\Order;
use Modules\Shared\Constant\GatewayConstant;
use Modules\User\App\Models\User;

class OrderIndexService
{
    function index($data) {
        if (
            isset($data['orderId']) ||
            isset($data['status']) ||
            isset($data['month']) ||
            isset($data['year']) ||
            isset($data['userId'])) {
            $page = 1;
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $ordersQuery = Order::with(['user:id,full_name,email', 'coupons', 'orderAddress'])
            ->select('id', 'user_id', 'status', 'total', 'payment_method', 'created_at')
            ->latest('created_at');

        if (isset($data['userId'])) {
            $userId = User::where('uuid', $data['userId'])->first()->id;
            $ordersQuery->where('user_id', $userId);
        }

        if (isset($data['orderId'])) {
            $ordersQuery->where('id', $data['orderId']);
        }

        if (isset($data['email'])) {
            $ordersQuery->whereHas('user', function ($query) use ($data) {
                $query->where('email', 'like', '%' . $data['email'] . '%');
            });
        }

        if (isset($data['month']) && isset($data['year'])) {
            $ordersQuery->whereMonth('created_at', $data['month'])
                ->whereYear('created_at', $data['year']);
        }

        if (isset($data['status'])) {
            $ordersQuery->where('status', $data['status']);
        }

        $orders = $ordersQuery->paginate(20);

        $orders->getCollection()->transform(function ($order) {
            $firstName = $order->orderAddress->address['first_name'];
            $lastName = $order->orderAddress->address['last_name'];
            $address = $order->orderAddress->address['address'];

            return [
                'id' => $order->id,
                'isRefunded' => $order->is_refunded,
                'customerName' => $order->user->full_name,
                'date' => $this->formatDate($order->created_at),
                'status' => Order::$orderStatusMapping[$order->status],
                'total' => $order->total,
                'paymentMethod' => GatewayConstant::$gatewayMapping[$order->payment_method],
                'shipTo' => $firstName . ' ' . $lastName,
                'shipToAddress' => $address,
                'coupons' => $order->coupons->map(function ($coupon) use ($order) {
                    $discountAmount = $this->calculateDiscountAmount($coupon, $order->subtotal);

                    return [
                        'couponCode' => $coupon->code,
                        'value' => $coupon->value,
                        'discountAmount' => $discountAmount,
                        'type' => $coupon->type,
                    ];
                })->toArray(),
            ];
        });

        return $orders;
    }

    private function calculateDiscountAmount($coupon, $subTotal) {
        $discountAmount = $coupon->isPercentageType()
            ? ($subTotal * $coupon->value / 100)
            : $coupon->value;

        if ($coupon->max_discount && $discountAmount > $coupon->max_discount) {
            $discountAmount = $coupon->max_discount;
        }

        return $discountAmount;
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
}
