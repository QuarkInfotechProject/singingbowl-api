<?php

namespace Modules\Order\Service\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Order\App\Models\Order;

class OrderGetStatusCountService
{
    function getStatusCount()
    {
        $statusMapping = Order::$orderFilterMapping;

        $totalOrders = Order::count();

        $statusCounts = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->whereIn('status', array_keys($statusMapping))
            ->pluck('total', 'status');

        $statusWithCounts = [];
        foreach ($statusMapping as $key => $name) {
            $statusWithCounts[] = [
                'name' => $name,
                'key' => $key,
                'count' => $statusCounts->get($key, 0),
            ];
        }

        array_unshift($statusWithCounts, [
            'name' => 'All',
            'key' => 'all',
            'count' => $totalOrders,
        ]);

        return $statusWithCounts;
    }
}
