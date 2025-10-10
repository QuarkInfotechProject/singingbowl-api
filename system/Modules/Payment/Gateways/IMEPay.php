<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\IMEPayResponse;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class IMEPay implements GatewayInterface
{
    function purchase(Order $order, Request $request): IMEPayResponse
    {
        try {
            $tokenResponse = $this->getToken($order->total, $order->id);

            if (!isset($tokenResponse['TokenId'])) {
                Log::error('Failed to get TokenId from IME Pay', [
                    'order_id' => $order->id,
                    'response' => $tokenResponse,
                ]);
                throw new Exception("Failed to get TokenId from IME Pay", ErrorCode::BAD_REQUEST);
            }

            $responseData = $this->prepareResponseData($order, $request, $tokenResponse['TokenId']);
            return new IMEPayResponse($order, $responseData);
        } catch (\Exception $exception) {
            Log::error('Error in IME Pay purchase process', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => Auth::id() ?? null,
            ]);
            throw $exception;
        }

    }

    private function getToken(float $amount, int $refId): array
    {
        try {
            $merchantCode = config('services.IMEPay.merchant_code');
            $apiUser = config('services.IMEPay.username');
            $password = config('services.IMEPay.password');
            $module = config('services.IMEPay.merchant_module');

            $data = [
                "MerchantCode" => $merchantCode,
                "Amount" => $amount,
                "RefId" => $refId
            ];

            $response = Http::withHeaders([
                "Authorization" => "Basic " . base64_encode("$apiUser:$password"),
                "Module" => base64_encode($module),
            ])->post(UrlConstant::IMEPAY_BASE_URL . 'GetToken', $data);

            if ($response->failed()) {
                Log::error('Failed to get token from IME Pay', [
                    'status_code' => $response->status(),
                    'body' => $response->body(),
                    'ref_id' => $refId,
                ]);
                throw new Exception("Failed to get token from IME Pay: " . $response->body(), ErrorCode::BAD_REQUEST);
            }

            return $response->json();
        } catch (\Exception $exception) {
            Log::error('Error getting token from IME Pay', [
                'message' => $exception->getMessage(),
                'ref_id' => $refId,
            ]);
            throw $exception;
        }
    }

    private function prepareResponseData(Order $order, Request $request, string $tokenId): array
    {
        try {
            $merchantCode = config('services.IMEPay.merchant_code');
            $respUrl = UrlConstant::PAYMENT_SUCCESS_URL . '?paymentMethod=' . $request->get('paymentMethod') . '&orderId=' . $order->id . '&';
            $cancelUrl = UrlConstant::PAYMENT_FAILURE_URL . '?orderId=' . $order->id . '&';

            $responseData = [
                'TokenId' => $tokenId,
                'MerchantCode' => $merchantCode,
                'RefId' => $order->id,
                'TranAmount' => $order->total,
                'Method' => 'GET',
                'RespUrl' => $respUrl,
                'CancelUrl' => $cancelUrl
            ];

            $payloadString = implode('|', array_values($responseData));
            $encodedPayload = base64_encode($payloadString);
            $urlEncodedPayload = urlencode($encodedPayload);

            $responseData['data'] = $urlEncodedPayload;

            return $responseData;
        } catch (\Exception $exception) {
            Log::error('Error preparing IME Pay response data', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id,
                'token_id' => $tokenId,
            ]);
            throw $exception;
        }
    }

    function complete(Order $order): IMEPayResponse
    {
        try {
            return new IMEPayResponse($order, request()->all());
        } catch (\Exception $exception) {
            Log::error('Error completing IME Pay order', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'response' => request()->all(),
            ]);
            throw $exception;
        }
    }
}
