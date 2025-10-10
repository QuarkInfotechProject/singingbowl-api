<?php

namespace Modules\Order\Service\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Order\Trait\RestoreStock;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderFailWithCardService
{
    use RestoreStock;

    function handleFailure($data)
    {
        $referenceNumber = $data['req_reference_number'];
        $orderId = explode('|', $referenceNumber)[0];

        if (!$orderId) {
            $message = 'Order reference number is missing.';
            Log::error($message, ['data' => $data]);
            throw new Exception($message, ErrorCode::BAD_REQUEST);
        }

        $order = Order::find($orderId);

        if (!$order) {
            $message = 'Order not found.';
            Log::error($message, ['orderId' => $orderId]);
            throw new Exception($message, ErrorCode::NOT_FOUND);
        }

        if (!$this->verifyCybersourceSignature($data)) {
            $message = 'Signature Mismatch.';
            Log::error($message, ['data' => $data]);
            throw new Exception($message, ErrorCode::BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $order->transaction()->create([
                'transaction_code' => null,
                'status' => $data['decision'],
                'payment_method' => $data['req_payment_method'] . (isset($data['card_type_name']) ? '/' . $data['card_type_name'] : ''),
            ]);

            $this->restoreStock($order);

            Event::dispatch(
                new OrderLogEvent(
                    "Order cancelled by customer. Order status changed from " . Order::$orderStatusMapping[Order::PENDING_PAYMENT] . " to "
                    . Order::$orderStatusMapping[Order::CANCELLED] . ".",
                    $order->id,
                    $modifierId ?? null,
                )
            );

            $order->update([
                'status' => Order::CANCELLED,
                'cancelled_date' => now(),
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Order failure handling failed: ' . $exception->getMessage(), [
                'orderId' => $orderId,
                'exception' => $exception,
            ]);
            DB::rollBack();
            throw $exception;
        }

        return $orderId;
    }

    private function verifyCybersourceSignature(array $params): bool
    {
        if (empty($params['signature']) || empty($params['signed_field_names'])) {
            return false;
        }

        $signedFieldNames = explode(',', $params['signed_field_names']);
        $signatureString = implode(',', array_map(
            fn($field) => $field . '=' . ($params[$field] ?? ''),
            $signedFieldNames
        ));

        $secretKey = config('services.card.secret_key');
        $expectedSignature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

        return hash_equals($expectedSignature, $params['signature']);
    }
}
