<?php

namespace Modules\Others\Service\DarazCount;

use Modules\Others\App\Models\DarazAnalytics;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class DarazCountShowService
{
    function show(int $id)
    {
        $count = DarazAnalytics::with('product')->find($id);

        if (!$count) {
            throw new Exception('Daraz count not found.', ErrorCode::NOT_FOUND);
        }

        return [
            'id' => $count->id,
            'productId' => $count->product->uuid,
            'productName' => $count->product->product_name,
            'unitsSold' => $count->units_sold,
            'reviewsCount' => $count->reviews_count,
            'link' => $count->link,
            'isActive' => $count->is_active
        ];
    }
}
