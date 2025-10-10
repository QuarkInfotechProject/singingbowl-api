<?php

namespace Modules\Order\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'orders:check-status';

    /**
     * The console command description.
     */
    protected $description = 'Check and update order statuses using Pathao Parcel API';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::with('consignmentId')
            ->where(function ($query) {
                $query->where('status', Order::READY_TO_SHIP)
                    ->orWhere('status', Order::SHIPPED);
            })
            ->whereHas('consignmentId', function ($query) {
                $query->whereNotNull('pathao_consignment_id');
            })
            ->get();

        foreach ($orders as $order) {
            $consignmentId = $order->consignmentId->pathao_consignment_id;
            $response = $this->checkOrderStatus($consignmentId);

            if ($response && $response['code'] == 200) {
                $this->updateOrderStatus($order, $response['data']['order_status']);
            }
        }

        $this->info('Order statuses checked and updated successfully.');
    }

    private function getAccessToken()
    {
        return Cache::remember('pathao_access_token', now()->addMinutes(55), function () {
            $response = Http::post(UrlConstant::PATHAO_BASE_URL . '/aladdin/api/v1/issue-token', [
                'client_id' => config('services.pathao.client_id'),
                'client_secret' => config('services.pathao.client_secret'),
                'username' => config('services.pathao.username'),
                'password' => config('services.pathao.password'),
                'grant_type' => 'password',
            ]);

            if (!$response->successful()) {
                Log::error('Failed to obtain Pathao access token: ' . $response->body());
                throw new Exception('Failed to obtain Pathao access token', ErrorCode::BAD_REQUEST);
            }

            return $response->json('access_token');
        });
    }

    private function checkOrderStatus($consignmentId)
    {
        $baseUrl = UrlConstant::PATHAO_BASE_URL;
        $accessToken = $this->getAccessToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get("{$baseUrl}/aladdin/api/v1/orders/$consignmentId/info");

        return $response->json();
    }

    private function updateOrderStatus($order, $pathaoStatus)
    {
        $newStatus = $this->mapPathaoStatusToOrderStatus($pathaoStatus);

        if ($newStatus !== $order->status) {

            Event::dispatch(new OrderLogEvent(
                "Order status changed from " . Order::$orderStatusMapping[$order['status']] .
                " to " . Order::$orderStatusMapping[$newStatus] . ".",
                $order->id,
                $modifierId ?? null
            ));

            $order->status = $newStatus;
            $order->save();
            $this->info("Updated order {$order->id} status to {$newStatus}");
        }
    }

    private function mapPathaoStatusToOrderStatus($pathaoStatus)
    {
        $deliveredStatuses = ['Delivered', 'Partial_Delivery'];
        $shippedStatuses = [
            'Pending',
            'Picked', 'At_the_Sorting_HUB', 'In_Transit',
            'Received_at_Last_Mile_HUB', 'Assigned_for_Delivery'
        ];

        if (in_array($pathaoStatus, $deliveredStatuses)) {
            return Order::DELIVERED;
        } elseif (in_array($pathaoStatus, $shippedStatuses)) {
            return Order::SHIPPED;
        }

        return Order::READY_TO_SHIP;
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
