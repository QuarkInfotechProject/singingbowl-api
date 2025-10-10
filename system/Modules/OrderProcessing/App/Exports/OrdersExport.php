<?php

namespace Modules\OrderProcessing\App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Order\App\Models\Order;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrdersExport implements FromCollection, WithHeadings, WithColumnWidths
{

    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    function collection()
    {
        $counter = 1;
        return collect($this->orders)->map(function ($orderId) use (&$counter) {
            $order = Order::with('user', 'orderItems', 'orderAddress.address', 'transaction')->find($orderId);

            if (!$order) {
                throw new Exception("Order #{$orderId} not found.", ErrorCode::NOT_FOUND);
            }

            return [
                'SN' => $counter++,
                'Order ID' => $order->id,
                'Customer Name' => $order->user->full_name,
                'Address' => $order->orderAddress->address->address,
                'City ID' => $order->orderAddress->address->city_id,
                'City' => $order->orderAddress->address->city_name,
                'Zone ID' => $order->orderAddress->address->zone_id,
                'Zone' => $order->orderAddress->address->zone_name,
                'Primary Phone' => $order->orderAddress->address->mobile,
                'Secondary Phone' => $order->orderAddress->address->backup_mobile,
                'Products' => $this->getProducts($order),
                'Quantities' => $this->getQuantities($order),
                'Net Total' => number_format($order->total, 2),
                'Payment Method' => $order->transaction ? 'COD' : 'NON-COD',
                'COD Amount' => number_format($order->total, 2)
            ];
        });
    }

    function headings(): array
    {
        return [
            'SN',
            'Order ID',
            'Customer Name',
            'Address',
            'City ID',
            'City',
            'Zone ID',
            'Zone',
            'Primary Phone',
            'Secondary Phone',
            'Products',
            'Quantities',
            'Net Total',
            'Payment Method',
            'COD Amount'
        ];
    }

    function columnWidths(): array
    {
        return [
            'A' => 10,   // SN
            'B' => 10,   // Order ID
            'C' => 15,   // Customer Name
            'D' => 20,   // Address
            'E' => 10,   // City ID
            'F' => 20,   // City
            'G' => 10,   // Zone ID
            'H' => 15,   // Zone
            'I' => 15,   // Primary Phone
            'J' => 15,   // Secondary Phone
            'K' => 40,   // Products
            'L' => 10,   // Quantities
            'M' => 10,   // Net Total
            'N' => 15,   // Payment Method
            'O' => 15,   // COD Amount
        ];
    }

    private function getProducts($order)
    {
        return $order->orderItems->pluck('product.product_name')->implode(', ');
    }

    private function getQuantities($order)
    {
        return $order->orderItems->pluck('quantity')->implode(', ');
    }
}
