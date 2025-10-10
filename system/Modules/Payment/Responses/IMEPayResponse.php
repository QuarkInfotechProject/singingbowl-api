<?php

namespace Modules\Payment\Responses;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Cart\Service\ClearCartService;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayResponse;
use Modules\Payment\HasTransactionReference;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class IMEPayResponse extends GatewayResponse implements HasTransactionReference
{
    private Order $order;
    private array $response;

    function __construct(Order $order, $response)
    {
        $this->order = $order;
        $this->response = $response;
    }

    function getTransactionReference()
    {
        try {
            $token = $this->response['token'];
            $decodedResponse = base64_decode($token);
            $responseArray = explode('|', $decodedResponse);

            $processedResponse = [
                'ResponseCode' => $responseArray[0],
                'ResponseDescription' => $responseArray[1],
                'Msisdn' => $responseArray[2],
                'TransactionId' => $responseArray[3],
                'RefId' => $responseArray[4],
                'TranAmount' => $responseArray[5],
                'TokenId' => $responseArray[6]
            ];

            if ($processedResponse['ResponseCode'] === "0") {
                Log::info('Successful transaction', ['order_id' => $this->order->id]);
                return $this->handleSuccessfulTransaction($processedResponse);
            } elseif (in_array($processedResponse['ResponseCode'], ["1", "2"])) {
                Log::warning('Failed transaction', ['order_id' => $this->order->id, 'response_code' => $processedResponse['ResponseCode']]);
                return $this->handleFailedTransaction($processedResponse);
            }

            Log::error('Invalid Response Code', ['order_id' => $this->order->id, 'response_code' => $processedResponse['ResponseCode']]);
            throw new Exception('Invalid Response Code', ErrorCode::BAD_REQUEST);
        } catch (\Exception $exception) {
            Log::error('Error in getTransactionReference', [
                'order_id' => $this->order->id,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);
            throw $exception;
        }
    }

    private function handleFailedTransaction(array $processedResponse): array
    {
        return $this->makeApiRequest('Recheck', $processedResponse);
    }

    private function handleSuccessfulTransaction(array $processedResponse): array
    {
        $clearCartService = App::make(ClearCartService::class);
        $clearCartService->clearCart();
        return $this->makeApiRequest('Confirm', $processedResponse);
    }

    private function makeApiRequest(string $endpoint, array $processedResponse): array
    {
        try {
            $apiUrl = UrlConstant::IMEPAY_BASE_URL . $endpoint;
            $apiUser = config('services.IMEPay.username');
            $apiPassword = config('services.IMEPay.password');
            $merchantCode = config('services.IMEPay.merchant_code');
            $merchantModule = config('services.IMEPay.merchant_module');

            $encodedAuth = base64_encode("$apiUser:$apiPassword");

            $requestData = [
                "MerchantCode" => $merchantCode,
                "RefId" => $processedResponse['RefId'],
                "TokenId" => $processedResponse['TokenId'],
            ];

            if ($endpoint === 'Confirm') {
                $requestData["TransactionId"] = $processedResponse['TransactionId'];
                $requestData["Msisdn"] = $processedResponse['Msisdn'];
            }

            $response = Http::withHeaders([
                'Authorization' => "Basic $encodedAuth",
                'Module' => base64_encode($merchantModule),
            ])->post($apiUrl, $requestData);

            if ($response->failed()) {
                Log::error('API request failed', [
                    'order_id' => $this->order->id,
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception('API Request Failed: ' . $response->body(), ErrorCode::BAD_REQUEST);
            }

            $decodedResponse = $response->json();

            if (!isset($decodedResponse['TransactionId'], $decodedResponse['ResponseDescription'])) {
                Log::error('Invalid API Response', [
                    'order_id' => $this->order->id,
                    'endpoint' => $endpoint,
                    'response' => $decodedResponse,
                ]);
                throw new Exception('Invalid API Response.', ErrorCode::BAD_REQUEST);
            }

            $result = [
                'transaction_code' => $decodedResponse['TransactionId'],
                'status' => $decodedResponse['ResponseDescription'],
            ];

            return $result;
        } catch (Exception $e) {
            Log::error('Error in makeApiRequest', [
                'order_id' => $this->order->id,
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            throw $e;
        }
    }

    function getOrderId()
    {
        return $this->order->id;
    }

    function toArray()
    {
        return parent::toArray() + [
                'response' => $this->response,
            ];
    }
}
