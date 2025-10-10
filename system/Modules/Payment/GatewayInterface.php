<?php

namespace Modules\Payment;

use Illuminate\Http\Request;
use Modules\Order\App\Models\Order;

interface GatewayInterface
{
    public function purchase(Order $order, Request $request);

    public function complete(Order $order);
}
