<?php

namespace Modules\Payment\Responses;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Cart\Service\CartClearService;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayResponse;
use Modules\Payment\HasTransactionReference;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class GetPayResponse extends GatewayResponse implements HasTransactionReference
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
            $decodedTokenData = $this->decodeToken();
            $preparedResponse = $this->prepareResponse($decodedTokenData);
            $this->verifySignature($preparedResponse);

            // Optional: Server-side verification
            // $this->verifyTransaction($preparedResponse);

            Log::info('GetPay payment verification successful', [
                'order_id'       => $this->order->id,
                'transaction_id' => $preparedResponse['id']
            ]);

            // Clear cart on successful verification
            App::make(CartClearService::class)->clearCart('user', $this->order->user_id);

            return [
                'transaction_code' => $preparedResponse['id'],
                'status'           => 'SUCCESS'
            ];

        } catch (\Exception $exception) {
            Log::error('Error in GetPay getTransactionReference', [
                'order_id' => $this->order->id,
                'message'  => $exception->getMessage(),
                'code'     => $exception->getCode(),
            ]);
            throw $exception;
        }
    }

    /**
     * Decode Base64 token - supports both JSON and URL-encoded formats
     */
    private function decodeToken()
    {
        try {
            $token = $this->response['token'] ?? null;

            if (empty($token)) {
                throw new Exception('Payment was cancelled or the token was not provided by GetPay.', ErrorCode::BAD_REQUEST);
            }

            // Validate Base64 format
            if (base64_encode(base64_decode($token, true)) !== $token) {
                Log::warning('GetPay received a token with an invalid Base64 format.', [
                    'order_id'       => $this->order->id,
                    'token_received' => $token,
                ]);
                throw new Exception('Payment verification failed: The provided token has an invalid format.', ErrorCode::BAD_REQUEST);
            }

            $decodedData = base64_decode($token);
            $jsonData = json_decode($decodedData, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                Log::info('GetPay token decoded as JSON', [
                    'order_id'   => $this->order->id,
                    'token_keys' => array_keys($jsonData)
                ]);
                return $jsonData;
            }

            // Fallback to URL-encoded format
            parse_str($decodedData, $parsedData);

            if (empty($parsedData)) {
                throw new Exception('Failed to parse the decoded GetPay token.', ErrorCode::BAD_REQUEST);
            }

            Log::info('GetPay token decoded as URL-encoded string', [
                'order_id'   => $this->order->id,
                'token_keys' => array_keys($parsedData)
            ]);
            return $parsedData;

        } catch (\Exception $exception) {
            Log::error('Error decoding GetPay token', [
                'order_id' => $this->order->id,
                'message'  => $exception->getMessage(),
                'token'    => $token ?? 'null',
            ]);
            throw $exception;
        }
    }

    private function prepareResponse($decodedTokenData)
    {
        try {
            $transactionId = $decodedTokenData['id'] ?? null;
            $oprSecret = $decodedTokenData['oprSecret'] ?? null;

            if (!$transactionId || !$oprSecret) {
                throw new Exception('Payment verification failed: Invalid or incomplete token data.', ErrorCode::BAD_REQUEST);
            }

            return ['id' => $transactionId, 'oprSecret' => $oprSecret];

        } catch (\Exception $exception) {
            Log::error('Error preparing GetPay response', [
                'order_id'  => $this->order->id,
                'message'   => $exception->getMessage(),
                'dataArray' => $decodedTokenData
            ]);
            throw $exception;
        }
    }

    /**
     * Verify signature using SHA256
     * Formula: base64_encode(sha256_raw(oprKey + transactionId)) === oprSecret
     */
    private function verifySignature($response)
    {
        try {
            $oprKey = Config::get('services.getpay.opr_key');
            $generatedSignatureRaw = hash('sha256', $oprKey . $response['id'], true);
            $expectedSignature = base64_encode($generatedSignatureRaw);

            if (!hash_equals($expectedSignature, $response['oprSecret'])) {
                Log::warning('GetPay Signature Mismatch', [
                    'order_id' => $this->order->id,
                    'expected' => $expectedSignature,
                    'received' => $response['oprSecret']
                ]);
                throw new Exception('Payment verification failed: Invalid signature.', ErrorCode::FORBIDDEN);
            }

            Log::info('GetPay Signature Verified for order ' . $this->order->id);

        } catch (\Exception $exception) {
            Log::error('Error verifying GetPay signature', [
                'order_id' => $this->order->id,
                'message'  => $exception->getMessage()
            ]);
            throw $exception;
        }
    }

    /**
     * Optional: Verify transaction status with GetPay server
     */
    private function verifyTransaction($response)
    {
        try {
            $url = UrlConstant::GETPAY_BASE_URL . UrlConstant::GETPAY_MERCHANT_STATUS_URL;
            $config = Config::get('services.getpay');

            $apiResponse = Http::post($url, [
                'id'      => $response['id'],
                'papInfo' => $config['pap_info']
            ]);

            if ($apiResponse->failed()) {
                Log::error('GetPay API request failed', [
                    'order_id' => $this->order->id,
                    'status'   => $apiResponse->status(),
                    'body'     => $apiResponse->body()
                ]);
                throw new Exception('GetPay API Request Failed: ' . $apiResponse->body(), ErrorCode::BAD_REQUEST);
            }

            $decodedResponse = $apiResponse->json();

            if (!isset($decodedResponse['status'])) {
                Log::error('Invalid API response from GetPay', [
                    'order_id' => $this->order->id,
                    'response' => $decodedResponse
                ]);
                throw new Exception('Invalid API Response from GetPay.', ErrorCode::BAD_REQUEST);
            }

            if (strtoupper($decodedResponse['status']) !== 'SUCCESS') {
                throw new Exception('Payment on server was not successful.', ErrorCode::UNPROCESSABLE_CONTENT);
            }

            return ['transaction_code' => $response['id'], 'status' => strtoupper($decodedResponse['status'])];

        } catch (\Exception $exception) {
            Log::error('Error verifying GetPay transaction', [
                'order_id' => $this->order->id,
                'message'  => $exception->getMessage()
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
        return parent::toArray() + ['response' => $this->response];
    }
}
