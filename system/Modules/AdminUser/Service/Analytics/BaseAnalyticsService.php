<?php

namespace Modules\AdminUser\Service\Analytics;

use Illuminate\Support\Facades\DB;
use Modules\AdminUser\Trait\AnalyticsDateRangeTrait;
use Modules\Order\App\Models\Order;
use Modules\Order\App\Models\OrderItem;

abstract class BaseAnalyticsService
{
    use AnalyticsDateRangeTrait;

    protected $allowedOrderStatuses = [
        Order::ORDER_PLACED,
        Order::ON_HOLD,
        Order::DELIVERED,
        // Order::NCELL_ORDER,
        Order::SHIPPED,
        Order::READY_TO_SHIP
    ];

    protected function getBasicOrderStats($dateRange)
    {
        return [
            'totalSales' => (float)$this->getTotalSales($dateRange),
            'netSales' => (float)$this->getNetSales($dateRange),
            'totalOrders' => (int)$this->getOrders($dateRange),
            'productsSold' => (int)$this->getProductsSold($dateRange),
            'variationsSold' => (int)$this->getVariationsSold($dateRange)
        ];
    }

    protected function getTotalSales($dateRange)
    {
        return Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', $this->allowedOrderStatuses)
            ->sum('total');
    }

    protected function getNetSales($dateRange)
    {
        return Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', $this->allowedOrderStatuses)
            ->sum('total');
    }

    protected function getOrders($dateRange)
    {
        return Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', $this->allowedOrderStatuses)
            ->count();
    }

    protected function getProductsSold($dateRange)
    {
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', $this->allowedOrderStatuses)
            ->whereBetween('order_items.created_at', [$dateRange['start'], $dateRange['end']])
            ->distinct('order_items.product_id')
            ->count('product_id');
    }

    protected function getVariationsSold($dateRange)
    {
        return OrderItem::whereBetween('order_items.created_at', [$dateRange['start'], $dateRange['end']])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', $this->allowedOrderStatuses)
            ->count();
    }

    protected function getChartData($dateRange)
    {
        return [
            'netSales' => $this->getNetSalesChart($dateRange),
            'orders' => $this->getOrdersChart($dateRange)
        ];
    }

    protected function getNetSalesChart($dateRange)
    {
        $allDates = collect();
        $currentDate = $dateRange['start']->copy();

        while ($currentDate->lte($dateRange['end'])) {
            $allDates->push($currentDate->copy());
            $currentDate->addDay();
        }

        $netSalesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as net_sales')
        )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', $this->allowedOrderStatuses)
            ->groupBy('date')
            ->pluck('net_sales', 'date');

        return $allDates->map(function($date) use ($netSalesData) {
            return [
                'date' => $date->format('F d, Y'),
                'netSales' => (float)$netSalesData->get($date->format('Y-m-d'), 0),
            ];
        })->toArray();
    }

    protected function getOrdersChart($dateRange)
    {
        $allDates = collect();
        $currentDate = $dateRange['start']->copy();

        while ($currentDate->lte($dateRange['end'])) {
            $allDates->push($currentDate->copy());
            $currentDate->addDay();
        }

        $ordersData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as order_count')
        )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', $this->allowedOrderStatuses)
            ->groupBy('date')
            ->pluck('order_count', 'date');

        return $allDates->map(function($date) use ($ordersData) {
            return [
                'date' => $date->format('F d, Y'),
                'orders' => (int)$ordersData->get($date->format('Y-m-d'), 0),
            ];
        })->toArray();
    }
}
