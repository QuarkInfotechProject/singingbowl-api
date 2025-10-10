<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\KhaltiResponse;
use Modules\Shared\Constant\UrlConstant;

class Khalti implements GatewayInterface
{
    function purchase(Order $order, Request $request)
    {
        try {
            $secretKey = config('services.khalti.secret');
            $payload = $this->preparePayload($order, $request);

            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $secretKey,
                'Content-Type' => 'application/json',
            ])->post(UrlConstant::KHALTI_BASE_URL, $payload);

            $response->throw();

            return new KhaltiResponse($order, $response->object());
        } catch (\Exception $exception) {
            Log::error('Error in Khalti purchase process', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => Auth::id() ?? null,
            ]);
            throw $exception;
        }
    }

    private function preparePayload(Order $order, Request $request): array
    {
        try {
            $payload = [
                'return_url' => UrlConstant::PAYMENT_SUCCESS_URL . '?paymentMethod=' . $request->get('paymentMethod') . '&',
                'website_url' => 'https://example.com/',
                'amount' => $order->total * 100,
                'purchase_order_id' => $order->id,
                'purchase_order_name' => 'Order ' . $order->id,
                'product_details' => $this->getProductDetails($order),
            ];

            return $payload;
        } catch (\Exception $exception) {
            Log::error('Error preparing Khalti payload', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id,
            ]);
            throw $exception;
        }
    }

    private function getProductDetails(Order $order): array
    {
        try {
            $details = $order->orderItems->map(function ($item) {
                return [
                    'identity' => $item->has_varaint ? $item->variant->uuid : $item->product->uuid,
                    'name' => $item->has_varaint ? $item->variant->name : $item->product->product_name,
                    'total_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->line_total
                ];
            })->toArray();

            return $details;
        } catch (\Exception $exception) {
            Log::error('Error getting product details for Khalti', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id,
            ]);
            throw $exception;
        }
    }

    function complete(Order $order)
    {
        try {
            return new KhaltiResponse($order, request()->all());
        } catch (\Exception $exception) {
            Log::error('Error completing Khalti order', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'response' => request()->all(),
            ]);
            throw $exception;
        }
    }
}
