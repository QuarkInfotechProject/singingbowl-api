<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Cart\App\Models\Cart;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\CardResponse;

class Card implements GatewayInterface
{
    function purchase(Order $order, Request $request)
    {
        try {
            $cart = Cart::getForCurrentUser();
            $order = Order::with(['orderAddress.address'])->findOrFail($order->id);

            $referenceNumber = $order->id . '|' . $cart->uuid;

            $cybersourceParams = [
                'access_key' => config('services.card.access_key'),
                'profile_id' => config('services.card.profile_id'),
                'transaction_uuid' => (string) Str::uuid(),
                'signed_field_names' => 'access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,payment_method,bill_to_forename,bill_to_surname,bill_to_email,bill_to_address_line1,bill_to_address_city,bill_to_address_country',
                'unsigned_field_names' => '',
                'signed_date_time' => gmdate("Y-m-d\TH:i:s\Z"),
                'locale' => 'en-us',
                'transaction_type' => 'sale',
                'reference_number' => $referenceNumber,
                'amount' => $order->total,
                'currency' => 'NPR',
                'payment_method' => $order->payment_method,
                'bill_to_forename' => $order->orderAddress->address->first_name,
                'bill_to_surname' => $order->orderAddress->address->last_name,
                'bill_to_email' => Auth::user()->email,
                'bill_to_address_line1' => $order->orderAddress->address->address,
                'bill_to_address_city' => $order->orderAddress->address->city_name,
                'bill_to_address_country' => 'NP',
            ];

            $cybersourceParams['signature'] = $this->sign($cybersourceParams);

            return new CardResponse($order, $cybersourceParams);
        } catch (\Exception $exception) {
            Log::error('Error in purchase process', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id ?? null,
                'user_id' => Auth::id() ?? null,
            ]);
            throw $exception;
        }
    }

    private function sign($params)
    {
        try {
            $signedFieldNames = implode(',', array_keys($params));

            $params['signed_field_names'] = $signedFieldNames;

            $signatureString = '';
            foreach (explode(',', $signedFieldNames) as $field) {
                $signatureString .= $field . '=' . $params[$field] . ',';
            }
            $signatureString = rtrim($signatureString, ',');

            return base64_encode(hash_hmac('sha256', $signatureString, config('services.card.secret_key'), true));
        } catch (\Exception $exception) {
            Log::error('Error generating signature', [
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    function complete(Order $order)
    {
        try {
            return new CardResponse($order, request()->all());
        } catch (\Exception $exception) {
            Log::error('Error completing order', [
                'message' => $exception->getMessage(),
                'order_id' => $order->id,
                'user_id' => Auth::id(),
            ]);
            throw $exception;
        }
    }
}
