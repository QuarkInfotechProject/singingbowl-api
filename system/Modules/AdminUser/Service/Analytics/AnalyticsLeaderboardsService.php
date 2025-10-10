<?php

namespace Modules\AdminUser\Service\Analytics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Category\App\Models\Category;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderItem;

class AnalyticsLeaderboardsService extends BaseAnalyticsService
{
    public function index($data)
    {
        try {
            $dateRange = $this->getDateRange($data['filter']);

            return [
                'categories' => $this->formatCollection($this->getTopCategories($dateRange), 'category'),
                'products' => $this->formatCollection($this->getTopProducts($dateRange), 'product'),
                'customers' => $this->formatCollection($this->getTopCustomers($dateRange), 'customer'),
                'coupons' => $this->formatCollection($this->getTopCoupons($dateRange), 'coupon')
            ];
        } catch (\Exception $exception) {
            Log::error('Error fetching analytics for leaderboards: ' . $exception->getMessage());
            throw $exception;
        }
    }

    private function formatCollection($collection, $type)
    {
        $formatters = [
            'category' => fn($item) => [
                'name' => $item->name,
                'itemsSold' => (int)$item->itemsSold,
                'netSales' => (float)$item->netSales
            ],
            'product' => fn($item) => [
                'name' => $item->name,
                'itemsSold' => (int)$item->itemsSold,
                'netSales' => (float)$item->netSales
            ],
            'customer' => fn($item) => [
                'name' => $item->name,
                'orders' => (int)$item->orders,
                'totalSpend' => (float)$item->totalSpend
            ],
            'coupon' => fn($item) => [
                'code' => $item->code,
                'orders' => (int)$item->orders,
                'amountDiscounted' => (float)$item->discountAmount
            ]
        ];

        return $collection->map($formatters[$type])->toArray();
    }

    private function getTopCategories($dateRange)
    {
        return Category::select(
            'categories.name',
            DB::raw('SUM(order_items.quantity) as itemsSold'),
            DB::raw('SUM(order_items.line_total) as netSales')
        )
            ->join('product_categories', 'categories.id', '=', 'product_categories.category_id')
            ->join('products', 'product_categories.product_id', '=', 'products.id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('order_items.created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('orders.status', $this->allowedOrderStatuses)
            ->groupBy('categories.name')
            ->orderByDesc('itemsSold')
            ->take(10)
            ->get();
    }

    private function getTopProducts($dateRange)
    {
        return OrderItem::select(
            'products.product_name as name',
            DB::raw('SUM(order_items.quantity) as itemsSold'),
            DB::raw('SUM(order_items.line_total) as netSales')
        )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('order_items.created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('orders.status', $this->allowedOrderStatuses)
            ->groupBy('products.product_name')
            ->orderByDesc('itemsSold')
            ->take(10)
            ->get();
    }

    private function getTopCustomers($dateRange)
    {
        return Order::select(
            'users.full_name as name',
            DB::raw('COUNT(orders.id) as orders'),
            DB::raw('SUM(orders.total) as totalSpend')
        )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('orders.status', $this->allowedOrderStatuses)
            ->groupBy('users.full_name')
            ->orderByDesc('totalSpend')
            ->take(10)
            ->get();
    }

    private function getTopCoupons($dateRange)
    {
        return Order::select(
            'coupons.code as code',
            DB::raw('COUNT(order_coupons.order_id) as orders'),
            DB::raw('SUM(orders.discount) as discountAmount')
        )
            ->join('order_coupons', 'orders.id', '=', 'order_coupons.order_id')
            ->join('coupons', 'order_coupons.coupon_id', '=', 'coupons.id')
            ->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('orders.status', $this->allowedOrderStatuses)
            ->groupBy('coupons.code')
            ->orderByDesc('orders')
            ->take(10)
            ->get();
    }
}
