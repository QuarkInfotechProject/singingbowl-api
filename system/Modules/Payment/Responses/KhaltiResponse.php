<?php

namespace Modules\Payment\Responses;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Cart\Service\ClearCartService;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Order\Trait\RestoreStock;
use Modules\Payment\GatewayResponse;
use Modules\Payment\HasTransactionReference;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class KhaltiResponse extends GatewayResponse implements HasTransactionReference
{
    use RestoreStock;
    private Order $order;
    private $response;

    function __construct(Order $order, $response)
    {
        $this->order = $order;
        $this->response = $response;
    }

    function getTransactionReference()
    {
        try {
            $secretKey = config('services.khalti.secret');
            $payload = [
                'pidx' => $this->response['token']
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $secretKey,
                'Content-Type' => 'application/json',
            ])->post(UrlConstant::KHALTI_LOOKUP_URL, $payload);

            $responseData = $response->json();
            $status = $responseData['status'];
            $transactionCode = $responseData['transaction_id'] ?? null;

            $result = [
                'transaction_code' => $transactionCode,
                'status' => $status,
                'statusCode' => $this->getStatusCode($status)
            ];

            switch ($result['statusCode']) {
                case 400:
                    $this->cancelOrder();
                    Log::info('Order cancelled due to failed transaction', ['order_id' => $this->response['orderId']]);
                    break;
                case 200:
                    if (in_array($status, ['Pending', 'Initiated', 'Refunded', 'Partially Refunded'])) {
                        $this->holdTransaction();
                        Log::info('Transaction held', ['order_id' => $this->response['orderId'], 'status' => $status]);
                    } else {
                        $clearCartService = App::make(ClearCartService::class);
                        $clearCartService->clearCart();
                        Log::info('Cart cleared for successful transaction', ['order_id' => $this->response['orderId']]);
                    }
                    break;
            }

            return $result;
        } catch (\Exception $exception) {
            Log::error('Error in Khalti getTransactionReference', [
                'order_id' => $this->response['orderId'] ?? null,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);
            throw $exception;
        }
    }

    private function getStatusCode($status)
    {
        $statusCodes = [
            'Completed' => 200,
            'Pending' => 200,
            'Expired' => 400,
            'Initiated' => 200,
            'Refunded' => 200,
            'User canceled' => 400,
            'Partially Refunded' => 200,
            'Failed' => 400
        ];

        return $statusCodes[$status] ?? 400;
    }

    private function holdTransaction()
    {
        try {
            $order = Order::find($this->response['orderId']);
            $order->update(['status' => Order::ON_HOLD]);
        } catch (\Exception $exception) {
            Log::error('Error holding transaction', [
                'order_id' => $this->response['orderId'],
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    private function cancelOrder()
    {
        try {
            $now = Carbon::now();
            $userId = Auth::id();
            $order = Order::find($this->response['orderId']);

            if (!$order || $order->user_id !== $userId) {
                Log::error('Order not found or does not belong to user', [
                    'order_id' => $this->response['orderId'],
                    'user_id' => $userId,
                ]);
                throw new Exception('Order not found.', ErrorCode::NOT_FOUND);
            }

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
                'cancelled_date' => $now
            ]);
        } catch (\Exception $exception) {
            Log::error('Error cancelling order', [
                'order_id' => $this->response['orderId'] ?? null,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
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
