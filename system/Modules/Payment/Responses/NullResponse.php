<?php

namespace Modules\Payment\Responses;

use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayResponse;

class NullResponse extends GatewayResponse
{
    private Order $order;

    function __construct(Order $order)
    {
        $this->order = $order;
    }

    function getOrderId()
    {
        return $this->order->id;
    }
}
