<?php

namespace Modules\Support\Service\Admin\OrderSupport;

use Modules\Support\App\Models\OrderSupport;

class OrderSupportIndexService
{
    function index()
    {
        return OrderSupport::select('id', 'name', 'email', 'phone', 'order_id as orderId')
            ->get();
    }
}
