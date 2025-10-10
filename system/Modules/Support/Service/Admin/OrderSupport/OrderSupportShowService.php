<?php

namespace Modules\Support\Service\Admin\OrderSupport;

use Carbon\Carbon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Support\App\Models\OrderSupport;

class OrderSupportShowService
{
    function show(int $id)
    {
        $orderSupport = OrderSupport::select(
            'name',
            'email',
            'phone',
            'order_id',
            'payment_transaction_id',
            'message',
            'image',
            'created_at'
        )->find($id);

        if (!$orderSupport) {
            throw new Exception('Order support not found.', ErrorCode::NOT_FOUND);
        }

        $imageUrl = null;
        if ($orderSupport->image) {
            $imageUrl = url('OrderSupportImages/' . $orderSupport->image);
        }

        return [
            'name' => $orderSupport->name,
            'email' => $orderSupport->email,
            'phone' => $orderSupport->phone,
            'orderId' => $orderSupport->order_id,
            'paymentTransactionId' => $orderSupport->payment_transaction_id,
            'message' => $orderSupport->message,
            'image' => $imageUrl,
            'submittedAt' => Carbon::parse($orderSupport->created_at)->isoFormat('Do MMMM, YYYY @ h:mm A')
        ];
    }
}
