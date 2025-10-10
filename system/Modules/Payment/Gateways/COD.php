<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\NullResponse;

class COD implements GatewayInterface
{
    function purchase(Order $order, Request $request)
    {
        return new NullResponse($order);
    }

    function complete(Order $order)
    {
        return new NullResponse($order);
    }
}
