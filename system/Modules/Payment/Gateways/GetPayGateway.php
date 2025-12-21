<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Models\Order;
use Modules\Payment\GatewayInterface;
use Modules\Payment\Responses\GetPayResponse;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Ramsey\Uuid\Uuid;

class GetPayGateway implements GatewayInterface
{
    public function getName(): string
    {
        return 'getPay';
    }

    /**
     * Prepare data for the frontend to initialize GetPay Checkout.
     * GetPay uses a client-side JS SDK, so we return options instead of a redirect URL.
     */
    public function purchase(Order $order, Request $request)
    {
        $config = config('services.getpay');
        $user = $order->user;
        $address = $order->orderAddress->address ?? null;
        $uuid = Uuid::uuid4();

        // Build user info for pre-filling checkout form
        $userInfo = [
            'name'    => $address ? ($address->first_name . ' ' . $address->last_name) : $user->name,
            'email'   => $user->email,
            'state'   => $address->state_division ?? null,
            'country' => $address->country ?? null,
            'zipcode' => $address->zip_code ?? null,
            'city'    => $address->city ?? null,
            'address' => $address->address_line_1 ?? null,
        ];

        // GetPay SDK options
        $options = [
            'userInfo'        => $userInfo,
            'clientRequestId' => (string) $order->id,
            'papInfo'         => $config['pap_info'],
            'oprKey'          => $config['opr_key'],
            'insKey'          => $config['ins_key'],
            'websiteDomain'   => $config['website_domain'],
            'baseUrl'         => $config['base_api_url'],
            'price'           => (float) $order->total,
            'businessName'    => $config['business_name'],
            'imageUrl'        => $config['logo_url'],
            'currency'        => 'NPR',
            'prefill'         => [
                'name'    => false,
                'email'   => false,
                'state'   => false,
                'city'    => false,
                'address' => false,
                'zipcode' => false,
                'country' => false
            ],
            'disableFields'   => [],
            'callbackUrl'     => [
                'successUrl' => UrlConstant::PAYMENT_SUCCESS_URL . '?paymentMethod=' . $request->get('paymentMethod') . '&orderId=' . $order->id . '&',
                'failUrl'    => UrlConstant::PAYMENT_FAILURE_URL . '?orderId=' . $order->id . '&amount=' . $order->total . '&uuid=' . $uuid,
            ],
            'themeColor'      => "#5662FF",
        ];

        return [
            'paymentMethod'  => $this->getName(),
            'orderId'        => $order->id,
            'getPayOptions'  => $options,
            'isRedirect'     => false,
            'oprKey'         => $config['opr_key'],
        ];
    }

    /**
     * Complete/verify payment after callback from GetPay.
     */
    public function complete(Order $order)
    {
        return new GetPayResponse($order, request()->all());
    }

    /**
     * Optional: Verify transaction status directly with GetPay API
     */
    private function verifyStatusOnServer(string $transactionId): array
    {
        $config = config('services.getpay');
        $url = UrlConstant::GETPAY_BASE_URL . UrlConstant::GETPAY_MERCHANT_STATUS_URL;

        try {
            $response = Http::post($url, [
                'id'      => $transactionId,
                'papInfo' => $config['pap_info'],
            ]);

            if (!$response->successful()) {
                throw new \Exception('API call failed with status: ' . $response->status());
            }
            return $response->json();

        } catch (\Throwable $th) {
            Log::error('GetPay Status API Call Failed', [
                'transaction_id' => $transactionId,
                'error' => $th->getMessage()
            ]);
            throw new Exception('Could not verify payment status with the provider.', ErrorCode::UNPROCESSABLE_CONTENT);
        }
    }
}
