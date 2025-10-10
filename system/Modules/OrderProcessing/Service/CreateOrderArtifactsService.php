<?php

namespace Modules\OrderProcessing\Service;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Models\Order;
use Modules\OrderProcessing\App\Events\OrderShipped;
use Modules\OrderProcessing\App\Exports\OrdersExport;
use Modules\OrderProcessing\App\Jobs\CreateOrderArtifactsJob;
use Modules\OrderProcessing\App\Models\OrderArtifact;
use Modules\OrderProcessing\App\Models\OrderPathaoConsignment;
use Modules\Shared\Constant\UrlConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use ZipArchive;

class CreateOrderArtifactsService
{
    private $pathaoConfig;

    public function __construct()
    {
        $this->pathaoConfig = [
            'base_url' => UrlConstant::PATHAO_BASE_URL,
            'client_id' => config('services.pathao.client_id'),
            'client_secret' => config('services.pathao.client_secret'),
            'username' => config('services.pathao.username'),
            'password' => config('services.pathao.password'),
            'store_id' => config('services.pathao.store_id'),
        ];
    }

    private function getAccessToken()
    {
        return Cache::remember('pathao_access_token', now()->addMinutes(55), function () {
            $response = Http::post($this->pathaoConfig['base_url'] . '/aladdin/api/v1/issue-token', [
                'client_id' => $this->pathaoConfig['client_id'],
                'client_secret' => $this->pathaoConfig['client_secret'],
                'username' => $this->pathaoConfig['username'],
                'password' => $this->pathaoConfig['password'],
                'grant_type' => 'password',
            ]);

            if (!$response->successful()) {
                Log::error('Failed to obtain Pathao access token: ' . $response->body());
                throw new Exception('Failed to obtain Pathao access token', ErrorCode::BAD_REQUEST);
            }

            return $response->json('access_token');
        });
    }

    public function createPathaoParcelOrder(Order $order)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post($this->pathaoConfig['base_url'] . '/aladdin/api/v1/orders', [
                'store_id' => $this->pathaoConfig['store_id'],
                'merchant_order_id' => $order->id,
                'recipient_name' => $order->orderAddress->address->first_name . $order->orderAddress->address->last_name,
                'recipient_phone' => $order->orderAddress->address->mobile,
                'recipient_address' => $order->orderAddress->address->address,
                'recipient_city' => $order->orderAddress->address->city_id,
                'recipient_zone' => $order->orderAddress->address->zone_id,
                'delivery_type' => 48,
                'item_type' => 2,
                'special_instruction' => $order->note ?? '',
                'item_quantity' => $order->orderItems->sum('quantity'),
                'item_weight' => 0.5,
                'amount_to_collect' => $order->payment_method === 'cod' ? (int) ($order->total) : 0,
                'item_description' => $order->orderItems->pluck('product_name')->implode(', '),
            ]);

        if (!$response->successful()) {
            Log::error('Failed to create Pathao order for order ID: ' . $order->id . '. Response: ' . $response->body());
            throw new Exception('Failed to create Pathao order: ' . $response->body(), ErrorCode::BAD_REQUEST);
        }

        $pathaoOrderDetails = $response->json();
        $consignmentId = $pathaoOrderDetails['data']['consignment_id'];

        try {
            OrderPathaoConsignment::create([
                'order_id' => $order->id,
                'pathao_consignment_id' => $consignmentId
            ]);

            $order->update(['status' => Order::READY_TO_SHIP]);

             Event::dispatch(new OrderShipped($order->orderAddress->address->first_name, $order->id, $order->orderAddress->address->mobile));
        } catch (\Exception $exception) {
            if ($exception->getCode() == 23000) {
                throw new Exception('Duplicate order processing committed.', ErrorCode::UNPROCESSABLE_CONTENT);
            }
            throw $exception;
        }

        Log::info('Pathao order created successfully for order ID: ' . $order->id);
    }

    public function create(array $data)
    {
        CreateOrderArtifactsJob::dispatch($data);
    }

    public function processOrders(array $data)
    {
        $orders = Order::with('user', 'orderItems.product', 'orderAddress.address', 'transaction', 'coupons')
            ->whereIn('id', $data['orders'])
            ->where('status', Order::ORDER_PLACED)
            ->get();

        $now = now();
        $zipFileName = 'Order_Artifacts_' . $now->format('Y_m_d, h_i A') . '.zip';
        $directoryPath = public_path('modules/orderArtifacts');
        $tempDirectoryPath = sys_get_temp_dir() . '/temp_order_artifacts_' . uniqid();

        if (!is_dir($tempDirectoryPath)) {
            mkdir($tempDirectoryPath, 0755, true);
        }

        $tempZipFilePath = $tempDirectoryPath . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($tempZipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception('Failed to create temporary zip file', ErrorCode::UNPROCESSABLE_CONTENT);
        }

        DB::beginTransaction();

        try {
            if ($data['shippingCompany'] === 'Pathao') {
                $orders->each([$this, 'createPathaoParcelOrder']);
            } else {
                $orders->each([$this, 'updateOrderStatus']);
            }

            $excelFileName = $this->addExcelToZip($zip, $data['orders'], $now);
            $orderArtifact = $this->createOrderArtifact($zipFileName, $directoryPath . '/' . $zipFileName, count($data['orders']), $data['shippingCompany']);
            $this->addPDFsToZip($zip, $orders, $orderArtifact->date, $now);

            $zip->close();

            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

            if (!rename($tempZipFilePath, $directoryPath . '/' . $zipFileName)) {
                throw new Exception('Failed to move zip file to final location', ErrorCode::UNPROCESSABLE_CONTENT);
            }

            $this->logOrderStatusChange($orders);

            DB::commit();

            $this->cleanupTempDirectory($tempDirectoryPath);
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->cleanupTempDirectory($tempDirectoryPath);
            Log::error('Failed to create order artifact: ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function updateOrderStatus(Order $order)
    {
        try {
            $order->update(['status' => Order::READY_TO_SHIP]);
        } catch (\Exception $exception) {
            Log::error('Error updating order status for Order ID ' . $order->id . ': ' . $exception->getMessage());
            throw $exception;
        }
    }

    private function cleanupTempDirectory($directory)
    {
        if (is_dir($directory)) {
            $files = glob($directory . '/*');
            foreach ($files as $file) {
                is_dir($file) ? $this->cleanupTempDirectory($file) : unlink($file);
            }
            rmdir($directory);
        }
    }

    private function addExcelToZip(ZipArchive $zip, array $orderIds, $now)
    {
        $excelFileName = 'Billing_Excel_Sheet_' . $now->format('Y_m_d, h_i A') . '.xlsx';
        Excel::store(new OrdersExport($orderIds), $excelFileName);
        $zip->addFile(storage_path('app/' . $excelFileName), $excelFileName);
        return $excelFileName;
    }

    private function createOrderArtifact($zipFileName, $zipFilePath, $orderCount, $shippingCompany)
    {
        return OrderArtifact::create([
            'date' => now(),
            'file_name' => $zipFileName,
            'file_path' => $zipFilePath,
            'order_count' => $orderCount,
            'shipping_company' => $shippingCompany
        ]);
    }

    private function addPDFsToZip(ZipArchive $zip, $orders, $date, $now)
    {
        $summaryPdf = $this->generateOrderSummaryPdf($orders, $date);
        $zip->addFromString('Order_Summary_&_Label_' . $now->format('Y_m_d, h_i A') . '.pdf', $summaryPdf->output());

        $packagingPdf = $this->generatePackagingPdf($orders, $date);
        $zip->addFromString('Packaging_' . $now->format('Y_m_d, h_i A') . '.pdf', $packagingPdf->output());
    }

    private function generateOrderSummaryPdf($orders, $date)
    {
        return PDF::loadView('orderprocessing::order_summary', compact('orders', 'date'));
    }

    private function generatePackagingPdf($orders, $date)
    {
        $preparedData = $this->preparePackagingData($orders);
        return PDF::loadView('orderprocessing::packaging', compact('preparedData', 'date'));
    }

    public function preparePackagingData($orders)
    {
        $preparedData = [];

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $productName = $item->product->product_name;
                $variantId = $item->variant_id;
                $quantity = $item->quantity;

                $variantKey = $this->getVariantKey($variantId);

                $preparedData[$productName] ??= [
                    'product_name' => $productName,
                    'total' => 0,
                    'types' => [],
                ];

                $preparedData[$productName]['total'] += $quantity;
                $preparedData[$productName]['types'][$variantKey] = ($preparedData[$productName]['types'][$variantKey] ?? 0) + $quantity;
            }
        }

        return $preparedData;
    }

    private function getVariantKey($variantId)
    {
        $variantOptions = DB::table('product_option_variants')
            ->join('product_option_values', 'product_option_variants.product_option_value_id', '=', 'product_option_values.id')
            ->join('product_options', 'product_option_values.product_option_id', '=', 'product_options.id')
            ->where('product_option_variants.product_variant_id', $variantId)
            ->select('product_options.name as option_name', 'product_option_values.option_name as option_value')
            ->get();

        if ($variantOptions->isEmpty()) {
            return 'N/A';
        }

        return $variantOptions->map(function ($option) {
            return "{$option->option_name}: {$option->option_value}";
        })->implode(', ');
    }

    public function logOrderStatusChange($orders)
    {
        foreach ($orders as $order) {
            Event::dispatch(
                new OrderLogEvent(
                    "Order status changed from " . Order::$orderStatusMapping[Order::ORDER_PLACED] .
                    " to " . Order::$orderStatusMapping[Order::READY_TO_SHIP] . ".",
                    $order->id,
                    $modifierId ?? null,
                )
            );
        }
    }
}
