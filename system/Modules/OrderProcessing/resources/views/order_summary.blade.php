<!DOCTYPE html>
<html lang="en" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; -webkit-print-color-adjust: exact;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <style>
        .page-break {
            page-break-after: always;
        }

        td {
            vertical-align: top;
        }

        @media screen and (max-width: 767px) {

            .order-details,
            .shipping-address,
            .billing-address {
                width: 100% !important;
            }
        }
    </style>
</head>

<body style="font-family: 'Open Sans', sans-serif; font-size: 15px; min-width: 320px; color: #555555; margin: 0;">
    @foreach($orders as $order)
        <table
            style="border-collapse: collapse; min-width: 320px; max-width: 900px; width: 100%; margin: auto; border-bottom: 2px solid #072541;">
            <tbody>
                <tr>
                    <td style="padding: 0;">
                        <table style="border-collapse: collapse; width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="padding: 15px; text-align: left;">
                                        <img src="https://ultima.com.np/wp-content/uploads/2024/01/logo-black-350x79.png"
                                            alt="image">
                                    </td>
                                    <td style="padding: 15px; text-align: right;">
                                        <span
                                            style="font-family: 'Open Sans', sans-serif; font-size: 14px; color: #fafafa;">
                                            House 11, Newa Colony<br>
                                            Tahachal Marg, Kathmandu<br>
                                            01-5313291
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 30px 15px;">
                        <table
                            style="border-collapse: collapse; min-width: 320px; max-width: 760px; width: 100%; margin: auto;">
                            <tbody>
                                <tr>
                                    <td style="padding: 0;">
                                        <h2
                                            style="font-family: 'Open Sans', sans-serif; font-weight: 600; font-size: 24px; margin: 0 0 20px; color: #072541;">
                                            Order Summary</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0;">
                                        <table class="billing-address"
                                            style="border-collapse: collapse; width: 100%; margin-bottom: 25px;">
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 0;">
                                                        <h5
                                                            style="font-family: 'Open Sans', sans-serif; font-weight: 600; font-size: 18px; line-height: 22px; margin: 0 0 8px; color: #444444;">
                                                            Customer Information
                                                        </h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="font-family: 'Open Sans', sans-serif; font-weight: 400; font-size: 15px; padding: 0;">
                                                        <table style="width: 100%; border-collapse: collapse;">
                                                            <tr>
                                                                <td style="padding: 4px 0; width: 30%;">
                                                                    <strong>Name:</strong></td>
                                                                <td style="padding: 4px 0;">{{ $order->user->full_name }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 4px 0; width: 30%;">
                                                                    <strong>Address:</strong></td>
                                                                <td style="padding: 4px 0;">
                                                                    {{ $order->orderAddress->address->address }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 4px 0; width: 30%;">
                                                                    <strong>City:</strong></td>
                                                                <td style="padding: 4px 0;">
                                                                    {{ $order->orderAddress->address->city_name }},
                                                                    {{ $order->orderAddress->address->zone_name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 4px 0; width: 30%;">
                                                                    <strong>Phone:</strong></td>
                                                                <td style="padding: 4px 0;">
                                                                    {{ $order->orderAddress->address->mobile }}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0;">
                                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 4px 0; width: 50%;"><strong>Order
                                                            #</strong>{{ $order->id }}</td>
                                                    <td style="padding: 4px 0; width: 50%; text-align: right;">
                                                        <strong>Date:</strong>
                                                        {{ $order->created_at->format('m/d/Y h:i A') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 4px 0;"><strong>Payment Method:</strong>
                                                        {{ \Modules\Shared\Constant\GatewayConstant::$gatewayMapping[$order->payment_method] }}
                                                    </td>

                                                    @if($order->payment_method !== 'cod')
                                                        <td style="padding: 4px 0; text-align: right;">
                                                            <strong>Reference:</strong>
                                                            {{ $order->transaction->transaction_code }}</td>
                                                    @endif

                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0;">
                                        <table
                                            style="width: 100%; border-collapse: collapse; font-family: 'Open Sans', sans-serif;">
                                            <thead>
                                                <tr style="background-color: #f8f8f8;">
                                                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">SN
                                                    </th>
                                                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">
                                                        Product</th>
                                                    <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                                                        Type</th>
                                                    <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                                                        Qty</th>
                                                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        Price</th>
                                                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        Total</th>
                                                </tr>
                                            </thead>

                                            @foreach($order->orderItems as $item)
                                                <tbody>
                                                    <tr>
                                                        <td style="padding: 10px; border: 1px solid #ddd;">
                                                            {{ $loop->iteration }}</td>
                                                        <td style="padding: 10px; border: 1px solid #ddd;">
                                                            {{ $item->product->product_name }}</td>
                                                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                                                            @if($item->variant)
                                                                @php
                                                                    $options = [];
                                                                    // Use the new pivot table relationship
                                                                    foreach ($item->product->options as $index => $productOption) {
                                                                        $matchingOptionValue = $item->variant->optionValues->first(function ($optionValue) use ($productOption) {
                                                                            return $optionValue->product_option_id === $productOption->id;
                                                                        });

                                                                        if ($matchingOptionValue) {
                                                                            $options[] = $productOption->name . ': ' . $matchingOptionValue->option_name;
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{ implode(', ', $options) }}
                                                            @endif
                                                        </td>
                                                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                                                            {{ $item->quantity }}</td>
                                                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                            {{ $item->unit_price }}</td>
                                                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                            {{ $item->line_total }}</td>
                                                    </tr>
                                                </tbody>
                                            @endforeach

                                            <tfoot>
                                                <tr>
                                                    <td colspan="5"
                                                        style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        Subtotal:</td>
                                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        {{ $order->subtotal }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"
                                                        style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        Shipping Cost:</td>
                                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        0.00</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"
                                                        style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        Discount

                                                        @foreach($order->coupons as $coupon)
                                                            ({{ $coupon->code }}{{ !$loop->last ? ',' : '' }})
                                                        @endforeach:
                                                    </td>
                                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        {{ $order->discount }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"
                                                        style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        Amount Paid:</td>
                                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        {{ $order->total }}</td>
                                                </tr>
                                                <tr style="font-weight: 600;">
                                                    <td colspan="5"
                                                        style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        Total Payable:</td>
                                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                        {{ $order->payment_method === 'cod' ? $order->total : '0.00' }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <p>Customer Note: {{$order->note}}</p>
                                        <p>Thank you for shopping with Ultima Lifestyle</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="page-break"></div>

        {{-- For order label--}}

        <div class="container">
            <table
                style="border-collapse: collapse; min-width: 320px; max-width: 400px; width: 100%; margin: 20px auto; border-bottom: 2px solid #072541;">
                <tbody>
                    <tr>
                        <td style="padding: 0;">
                            <table style="border-collapse: collapse; width: 100%; background: #072541;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 15px; text-align: left;">
                                            <span
                                                style="font-family: 'Open Sans', sans-serif; font-weight: 600; font-size: 24px; color: #fafafa;">ULTIMA</span>
                                        </td>
                                        <td style="padding: 15px; text-align: right;">
                                            <span
                                                style="font-family: 'Open Sans', sans-serif; font-size: 14px; color: #fafafa;">
                                                Order #{{$order->id}}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px 15px;">
                            <table
                                style="border-collapse: collapse; min-width: 320px; max-width: 360px; width: 100%; margin: auto;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 0; text-align: center;">
                                            <div style="margin-bottom: 10px;">
                                                {!! DNS1D::getBarcodeHTML("PAT-{$order->id}-1", 'C128', 2, 50) !!}</div>
                                            <p style="text-align: center;">PAT-{{ $order->id }}-1</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0;">
                                            <table class="shipping-address"
                                                style="border-collapse: collapse; width: 100%; margin-bottom: 25px;">
                                                <tbody>
                                                    <tr>
                                                        <td style="padding: 0;">
                                                            <h5
                                                                style="font-family: 'Open Sans', sans-serif; font-weight: 600; font-size: 18px; line-height: 22px; margin: 0 0 8px; color: #444444;">
                                                                Recipient Information
                                                            </h5>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style="font-family: 'Open Sans', sans-serif; font-weight: 400; font-size: 15px; padding: 0;">
                                                            <table style="width: 100%; border-collapse: collapse;">
                                                                <tr>
                                                                    <td style="padding: 4px 0; width: 30%;">
                                                                        <strong>Name:</strong></td>
                                                                    <td style="padding: 4px 0;">{{$order->user->full_name}}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding: 4px 0; width: 30%;">
                                                                        <strong>Address:</strong></td>
                                                                    <td style="padding: 4px 0;">
                                                                        {{$order->orderAddress->address->address}}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding: 4px 0; width: 30%;">
                                                                        <strong>City:</strong></td>
                                                                    <td style="padding: 4px 0;">
                                                                        {{ $order->orderAddress->address->city_name }},
                                                                        {{ $order->orderAddress->address->zone_name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding: 4px 0; width: 30%;"><strong>Primary
                                                                            Phone:</strong></td>
                                                                    <td style="padding: 4px 0;">
                                                                        {{ $order->orderAddress->address->mobile }}</td>
                                                                </tr>
                                                                @if($order->orderAddress->address->backup_mobile)
                                                                    <tr>
                                                                        <td style="padding: 4px 0; width: 30%;">
                                                                            <strong>Secondary Phone:</strong></td>
                                                                        <td style="padding: 4px 0;">
                                                                            {{ $order->orderAddress->address->backup_mobile }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0;">
                                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
                                                <tbody>
                                                    <tr>
                                                        <td style="padding: 4px 0; width: 50%;"><strong>Sender:</strong>
                                                            {{ env('STORENAME') }}</td>
                                                        <td style="padding: 4px 0; width: 50%; text-align: right;"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0;">
                                            <table
                                                style="width: 100%; border-collapse: collapse; font-family: 'Open Sans', sans-serif;">
                                                <thead>
                                                    <tr style="background-color: #f8f8f8;">
                                                        <th
                                                            style="padding: 10px; text-align: left; border: 1px solid #ddd;">
                                                            Label</th>
                                                        <th
                                                            style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                            Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="padding: 10px; border: 1px solid #ddd;">Quantity</td>
                                                        <td
                                                            style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                            {{$order->orderItems->sum('quantity')}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 10px; border: 1px solid #ddd;">Order Date</td>
                                                        <td
                                                            style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                            {{ $order->created_at->format('D M d Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 10px; border: 1px solid #ddd;">AWB Print Date
                                                        </td>
                                                        <td
                                                            style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                            {{ $date->format('D M d Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 10px; border: 1px solid #ddd;">
                                                            {{ $order->transaction ? 'NON-COD' : 'COD' }}
                                                        </td>
                                                        <td
                                                            style="padding: 10px; text-align: right; border: 1px solid #ddd;">
                                                            {{ $order->transaction ? '0.00 NPR' : $order->total . ' NPR' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="page-break"></div>
    @endforeach
</body>

</html>