<?php

namespace Modules\Payment\Responses;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Cart\Service\ClearCartService;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayResponse;
use Modules\Payment\HasTransactionReference;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class EsewaResponse extends GatewayResponse implements HasTransactionReference
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
            $dataArray = $this->decodeToken();
            $response = $this->prepareResponse($dataArray);
            $this->verifySignature($response);
            return $this->verifyTransaction($response);
        } catch (\Exception $exception) {
            Log::error('Error in getTransactionReference', [
                'order_id' => $this->order->id,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);
            throw $exception;
        }
    }

    private function decodeToken()
    {
        try {
            $decodedData = base64_decode($this->response['token']);
            return json_decode($decodedData, true);
        } catch (\Exception $exception) {
            Log::error('Error decoding token', [
                'order_id' => $this->order->id,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    private function prepareResponse($dataArray)
    {
        try {
            $amount = Arr::get($dataArray, 'total_amount') ?? throw new Exception('Invalid total amount.', ErrorCode::BAD_REQUEST);
            $amount = str_replace(',', '', $amount);

            $response = [
                "transaction_code" => $dataArray['transaction_code'],
                "status" => $dataArray['status'],
                "total_amount" => $amount,
                "transaction_uuid" => $dataArray['transaction_uuid'],
                "product_code" => $dataArray['product_code'],
                "signed_field_names" => $dataArray['signed_field_names'],
                "signature" => $dataArray['signature']
            ];

            return $response;
        } catch (\Exception $exception) {
            Log::error('Error preparing response', [
                'order_id' => $this->order->id,
                'message' => $exception->getMessage(),
                'dataArray' => $dataArray,
            ]);
            throw $exception;
        }
    }

    private function verifySignature($response)
    {
        try {
            $signedFieldNames = explode(',', $response['signed_field_names']);
            $dataToHash = implode(',', array_map(fn($field) => "{$field}={$response[$field]}", $signedFieldNames));

            $secretKey = Config::get('services.esewa.secret');
            $expectedSignature = base64_encode(hash_hmac('sha256', $dataToHash, $secretKey, true));

            if ($response['signature'] !== $expectedSignature) {
                Log::warning('Signature mismatch', [
                    'order_id' => $this->order->id,
                    'expected' => $expectedSignature,
                    'received' => $response['signature'],
                ]);
                throw new Exception('Signature Mismatch.', ErrorCode::BAD_REQUEST);
            }

            Log::info('Signature verified', ['order_id' => $this->order->id]);
        } catch (\Exception $exception) {
            Log::error('Error verifying signature', [
                'order_id' => $this->order->id,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    private function verifyTransaction($response)
    {
        try {
            $url = UrlConstant::ESEWA_ENQUIRY_URL;
            $queryParams = Arr::only($response, ['product_code', 'total_amount', 'transaction_uuid']);

            $apiResponse = Http::get($url, $queryParams);

            if ($apiResponse->failed()) {
                Log::error('API request failed', [
                    'order_id' => $this->order->id,
                    'status' => $apiResponse->status(),
                    'body' => $apiResponse->body(),
                ]);
                throw new Exception('API Request Failed: ' . $apiResponse->body(), ErrorCode::BAD_REQUEST);
            }

            $decodedResponse = $apiResponse->json();

            if (!isset($decodedResponse['ref_id'], $decodedResponse['status'])) {
                Log::error('Invalid API response', [
                    'order_id' => $this->order->id,
                    'response' => $decodedResponse,
                ]);
                throw new Exception('Invalid API Response.', ErrorCode::BAD_REQUEST);
            }

            if ($decodedResponse['status'] === 'COMPLETE') {
                $clearCartService = App::make(ClearCartService::class);
                $clearCartService->clearCart();
            }

            $result = [
                'transaction_code' => $decodedResponse['ref_id'],
                'status' => $decodedResponse['status'],
            ];

            return $result;
        } catch (\Exception $exception) {
            Log::error('Error verifying transaction', [
                'order_id' => $this->order->id,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
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
