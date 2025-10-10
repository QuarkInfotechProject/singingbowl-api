<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\EsewaResponse;
use Modules\Shared\Constant\UrlConstant;
use Ramsey\Uuid\Uuid;

class Esewa implements GatewayInterface
{
    function purchase(Order $order, Request $request)
    {
        try {
            $uuid = Uuid::uuid4();
            $productCode = config('services.esewa.product_code');
            $secretKey = config('services.esewa.secret');
            $dataToHash = sprintf(
                'total_amount=%s,transaction_uuid=%s,product_code=%s',
                $order->total,
                $uuid,
                $productCode
            );
            $hashData = hash_hmac('sha256', $dataToHash, $secretKey, true);
            $signature = base64_encode($hashData);

            $response = [
                "orderId" => $order->id,
                "amount" => $order->subtotal - $order->discount,
                "failure_url" => UrlConstant::PAYMENT_FAILURE_URL .
                    '?orderId=' . $order->id .
                    '&productCode=' . $productCode .
                    '&amount=' . $order->total .
                    '&uuid=' . $uuid,
                "product_delivery_charge" => $order->delivery_charge ?? "0",
                "product_service_charge" => "0",
                "product_code" => $productCode,
                "signature" => $signature,
                "signed_field_names" => "total_amount,transaction_uuid,product_code",
                "success_url" => UrlConstant::PAYMENT_SUCCESS_URL . '?paymentMethod=' . $request->get('paymentMethod') . '&orderId=' . $order->id . '&',
                "tax_amount" => "0",
                "total_amount" => $order->total,
                "transaction_uuid" => $uuid
            ];

            return new EsewaResponse($order, $response);
        } catch (\Exception $exception) {
            Log::error('Error in Esewa purchase process', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => Auth::id() ?? null,
            ]);
            throw $exception;
        }
    }

    function complete(Order $order)
    {
        try {
            return new EsewaResponse($order, request()->all());
        } catch (\Exception $exception) {
            Log::error('Error completing Esewa order', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'response' => request()->all(),
            ]);
            throw $exception;
        }
    }
}
