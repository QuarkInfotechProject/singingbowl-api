<?php

namespace Modules\Payment\Responses;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Cart\App\Models\Cart;
use Modules\Cart\Service\ClearCartService;
use Modules\Order\App\Models\Order;
use Modules\Order\Trait\RestoreStock;
use Modules\Payment\GatewayResponse;
use Modules\Payment\HasTransactionReference;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CardResponse extends GatewayResponse implements HasTransactionReference
{
    use RestoreStock;

    private Order $order;
    private array $response;
    private const DECISION_MAP = [
        'ACCEPT' => ['status' => 'COMPLETE', 'validCodes' => ['100', '110']],
        'DECLINE' => ['status' => 'DECLINED', 'restoreStock' => true],
        'ERROR' => ['status' => 'ERROR', 'restoreStock' => true],
        'CANCEL' => ['status' => 'CANCELLED', 'restoreStock' => true],
        'REVIEW' => ['status' => 'PENDING'],
    ];

    function __construct(Order $order, array $response)
    {
        $this->order = $order;
        $this->response = $response;
    }

    function getTransactionReference(): array
    {
        try {
            $this->validateResponse();
            $this->verifyCybersourceSignature();

            $decision = $this->response['decision'];
            $cartUuid = explode('|', $this->response['req_reference_number'])[1];
            $transactionCode = $this->response['bill_trans_ref_no'] ?? null;
            $decisionInfo = self::DECISION_MAP[$decision] ?? ['status' => 'UNKNOWN'];

            if ($decisionInfo['restoreStock'] ?? false) {
                $this->restoreStock($this->order);
            }

            if ($decision === 'ACCEPT' && !in_array($this->response['reason_code'], $decisionInfo['validCodes'])) {
                Log::warning('Unknown status for ACCEPT decision', [
                    'order_id' => $this->order->id,
                    'reason_code' => $this->response['reason_code'],
                ]);
                return $this->getUnknownStatusResponse($transactionCode);
            }

            $result = [
                'transaction_code' => $transactionCode,
                'status' => $decisionInfo['status'],
                'reason' => $this->getReasonMessage($decision),
                'cardName' => $this->response['card_type_name']
            ];

            if ($decision === 'ACCEPT' || $decisionInfo['status'] === 'COMPLETE') {
                $this->clearCart($cartUuid);
            }

            return $result;
        } catch (\Exception $exception) {
            Log::error('Error processing transaction', [
                'message' => $exception->getMessage(),
                'order_id' => $this->order->id ?? null,
            ]);
            throw $exception;
        }
    }

    private function clearCart($cartUuid): void
    {
        try {
            DB::beginTransaction();

            $cart = Cart::where('uuid', $cartUuid)->first();
            if (!$cart) {
                throw new \Exception('Cart not found.');
            }

            $cart->delete();
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to clear cart.', [
                'error' => $exception->getMessage(),
            ]);
            DB::rollBack();
            throw $exception;
        }
    }

    private function validateResponse(): void
    {
        if (!isset($this->response['decision'])) {
            Log::error('Invalid response from payment gateway', [
                'response' => $this->response,
            ]);
            throw new Exception('Invalid response from payment gateway.', ErrorCode::BAD_REQUEST);
        }
    }

    private function verifyCybersourceSignature(): void
    {
        try {
            if (empty($this->response['signature']) || empty($this->response['signed_field_names'])) {
                Log::error('Missing signature or signed field names', [
                    'response' => $this->response,
                ]);
                throw new Exception('Signature Mismatch.', ErrorCode::BAD_REQUEST);
            }

            $signedFieldNames = explode(',', $this->response['signed_field_names']);
            $signatureString = implode(',', array_map(
                fn($field) => "$field=" . ($this->response[$field] ?? ''),
                $signedFieldNames
            ));

            $secretKey = config('services.card.secret_key');
            $expectedSignature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

            if (!hash_equals($expectedSignature, $this->response['signature'])) {
                Log::error('Signature mismatch', [
                    'expected' => $expectedSignature,
                    'received' => $this->response['signature'],
                ]);
                throw new Exception('Signature Mismatch.', ErrorCode::BAD_REQUEST);
            }
        } catch (\Exception $exception) {
            Log::error('Error verifying Cybersource signature', [
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    private function getReasonMessage(string $decision): string
    {
        return $this->response['message']
            ?? match($decision) {
                'DECLINE' => 'Transaction was declined.',
                'ERROR' => 'An error occurred during the transaction.',
                'CANCEL' => 'The transaction was cancelled.',
                'REVIEW' => 'Transaction is under review.',
                default => 'Unknown transaction status.'
            };
    }

    private function getUnknownStatusResponse(?string $transactionCode): array
    {
        return [
            'transaction_code' => $transactionCode,
            'status' => 'UNKNOWN',
            'reason' => 'Unknown transaction status.'
        ];
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
