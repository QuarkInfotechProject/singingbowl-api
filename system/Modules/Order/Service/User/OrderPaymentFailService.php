<?php

namespace Modules\Order\Service\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Order\Trait\RestoreStock;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderPaymentFailService
{
    use RestoreStock;

    function handleFailure(array $data): void
    {
        $order = $this->getAndValidateOrder($data['orderId']);

        $this->validatePaymentData($order, $data);

        try {
            $transactionData = $this->getTransactionData($order, $data);
            $this->createTransaction($order, $transactionData);
            $this->cancelOrder($order);
        } catch (\Exception $exception) {
            $this->handleException($exception);
        }
    }

    private function validatePaymentData(Order $order, array $data): void
    {
        $rules = $this->getPaymentValidationRules($order->payment_method);

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function getPaymentValidationRules(string $paymentMethod): array
    {
        switch ($paymentMethod) {
            case 'esewa':
                return [
                    'productCode' => 'required|string',
                    'amount' => 'required|numeric',
                    'uuid' => 'required|string',
                ];
            case 'IMEPay':
                return [
                    'token' => 'required|string',
                ];
            default:
                return [];
        }
    }

    private function getAndValidateOrder(int $orderId): Order
    {
        $order = Order::find($orderId);
        $userId = Auth::id();

        if (!$order || $order->user_id !== $userId) {
            $message = 'Order not found or unauthorized access.';
            Log::error($message, ['orderId' => $orderId, 'userId' => $userId]);
            throw new Exception($message, ErrorCode::NOT_FOUND);
        }

        return $order;
    }

    private function getTransactionData(Order $order, array $data): array
    {
        switch ($order->payment_method) {
            case 'esewa':
                return $this->getEsewaTransactionData($data, $order->payment_method);
            case 'IMEPay':
                return $this->getIMEPayTransactionData($data, $order->payment_method);
            default:
                $message = 'Unsupported payment method.';
                Log::error($message, ['paymentMethod' => $order->payment_method]);
                throw new Exception($message, ErrorCode::BAD_REQUEST);        }
    }

    private function getEsewaTransactionData(array $data, string $paymentMethod): array
    {
        $url = UrlConstant::ESEWA_ENQUIRY_URL;
        $queryParams = [
            'product_code' => $data['productCode'],
            'total_amount' => $data['amount'],
            'transaction_uuid' => $data['uuid']
        ];

        try {
            $response = Http::get($url, $queryParams);
            $response->throw();
            $decodedResponse = $response->json();

            return [
                'transaction_code' => $decodedResponse['ref_id'],
                'status' => $decodedResponse['status'],
                'payment_method' => $paymentMethod,
            ];
        } catch (\Throwable $exception) {
            Log::error('Esewa transaction enquiry failed.', [
                'url' => $url,
                'queryParams' => $queryParams,
                'exception' => $exception
            ]);
            throw $exception;
        }
    }

    private function getIMEPayTransactionData(array $data, string $paymentMethod): array
    {
        $decodedResponse = base64_decode($data['token']);
        $responseArray = explode('|', $decodedResponse);

        $this->getAndValidateOrder($responseArray[4]);

        return [
            'transaction_code' => $responseArray[3] === '000' ? null : $responseArray[3],
            'status' => $responseArray[1],
            'payment_method' => $paymentMethod,
        ];
    }

    private function createTransaction(Order $order, array $transactionData): void
    {
        $order->transaction()->create($transactionData);
    }

    private function cancelOrder(Order $order): void
    {
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
    }

    private function handleException(\Exception $exception): void
    {
        if ($exception->getCode() === '23000') {
            $message = 'Duplicate transaction committed.';
            Log::error($message, ['exception' => $exception]);
            throw new Exception($message, ErrorCode::BAD_REQUEST);
        }
        Log::error('Order handling failed.', ['exception' => $exception]);
        throw $exception;
    }
}
