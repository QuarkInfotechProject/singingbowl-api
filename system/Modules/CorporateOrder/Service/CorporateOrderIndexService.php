<?php

namespace Modules\CorporateOrder\Service;

use Carbon\Carbon;
use Modules\CorporateOrder\App\Models\CorporateOrder;

class CorporateOrderIndexService
{
    function index()
    {
        $orders = CorporateOrder::select('id', 'first_name', 'last_name', 'email', 'phone', 'status')
            ->paginate(20);

        $formattedOrders = [];

        foreach ($orders as $order) {
            $formattedOrders[] = [
                'id' => $order->id,
                'firstName' => $order->first_name,
                'lastName' => $order->last_name,
                'email' => $order->email,
                'phone' => $order->phone,
                'status' => CorporateOrder::$corporateOrderStatusMapping[$order->status],
                'submittedAt' => Carbon::parse($order->created_at)->isoFormat('Do MMMM, YYYY @ h:mm A'),
            ];
        }

        return $formattedOrders;
    }
}
