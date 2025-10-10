<?php

namespace Modules\Others\Service\DarazCount;

use Modules\Others\App\Models\DarazAnalytics;

class DarazCountIndexService
{
    function index()
    {
        return DarazAnalytics::select('id', 'product_id', 'units_sold', 'reviews_count', 'is_active')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'productName' => $item->product->product_name,
                    'unitsSold' => $item->units_sold,
                    'reviewsCount' => $item->reviews_count,
                    'isActive' => $item->is_active
                ];
            });
    }
}
