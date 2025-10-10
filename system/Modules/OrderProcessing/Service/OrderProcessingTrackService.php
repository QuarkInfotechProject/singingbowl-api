<?php

namespace Modules\OrderProcessing\Service;

use Modules\Order\App\Models\Order;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderProcessingTrackService
{
    function trackOrderProcessing(int $orderId, int $mobile)
    {
        $order = Order::find($orderId);

        if (!$order) {
            throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
        }

        if ($order->consignmentId) {
            $consignmentId = $order->consignmentId->pathao_consignment_id;
            return UrlConstant::PATHAO_TRACK_URL . "?consignment_id=$consignmentId&phone=$mobile";
        };

        return null;
    }
}
