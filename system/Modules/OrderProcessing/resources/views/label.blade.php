<!DOCTYPE html>
<html lang="en" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; -webkit-print-color-adjust: exact;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <title>Order Labels</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body style="font-family: 'Open Sans', sans-serif; font-size: 15px; min-width: 320px; color: #555555; margin: 0;">

@foreach ($orders as $order)
    <div class="container">
    <table style="border-collapse: collapse; min-width: 320px; max-width: 400px; width: 100%; margin: 20px auto; border-bottom: 2px solid #072541;">
        <tbody>
        <tr>
            <td style="padding: 0;">
                <table style="border-collapse: collapse; width: 100%; background: #072541;">
                    <tbody>
                    <tr>
                        <td style="padding: 15px; text-align: left;">
                            <span style="font-family: 'Open Sans', sans-serif; font-weight: 600; font-size: 24px; color: #fafafa;">ULTIMA</span>
                        </td>
                        <td style="padding: 15px; text-align: right;">
                        <span style="font-family: 'Open Sans', sans-serif; font-size: 14px; color: #fafafa;">
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
                <table style="border-collapse: collapse; min-width: 320px; max-width: 360px; width: 100%; margin: auto;">
                    <tbody>
                    <tr>
                        <td style="padding: 0; text-align: center;">
                            <div style="margin-bottom: 10px;">{!! DNS1D::getBarcodeHTML("PAT-{$order->id}-1", 'C128', 2, 50) !!}</div>
                            <p style="text-align: center;">PAT-{{ $order->id }}-1</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0;">
                            <table class="shipping-address" style="border-collapse: collapse; width: 100%; margin-bottom: 25px;">
                                <tbody>
                                <tr>
                                    <td style="padding: 0;">
                                        <h5 style="font-family: 'Open Sans', sans-serif; font-weight: 600; font-size: 18px; line-height: 22px; margin: 0 0 8px; color: #444444;">
                                            Recipient Information
                                        </h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-family: 'Open Sans', sans-serif; font-weight: 400; font-size: 15px; padding: 0;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 4px 0; width: 30%;"><strong>Name:</strong></td>
                                                <td style="padding: 4px 0;">{{$order->user->full_name}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; width: 30%;"><strong>Address:</strong></td>
                                                <td style="padding: 4px 0;">{{$order->orderAddress->address}}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; width: 30%;"><strong>City:</strong></td>
                                                <td style="padding: 4px 0;">{{ $order->orderAddress->city_name }}, {{ $order->orderAddress->zone_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; width: 30%;"><strong>Primary Phone:</strong></td>
                                                <td style="padding: 4px 0;">{{ $order->orderAddress->mobile }}</td>
                                            </tr>
                                            @if($order->orderAddress->backup_mobile)
                                                <tr>
                                                    <td style="padding: 4px 0; width: 30%;"><strong>Secondary Phone:</strong></td>
                                                    <td style="padding: 4px 0;">{{ $order->orderAddress->backup_mobile }}</td>
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
                                    <td style="padding: 4px 0; width: 50%;"><strong>Sender:</strong> {{ env('STORENAME') }}</td>
                                    <td style="padding: 4px 0; width: 50%; text-align: right;"></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0;">
                            <table style="width: 100%; border-collapse: collapse; font-family: 'Open Sans', sans-serif;">
                                <thead>
                                <tr style="background-color: #f8f8f8;">
                                    <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Label</th>
                                    <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;">Quantity</td>
                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{$order->orderItems->sum('quantity')}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;">Order Date</td>
                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{ $order->created_at->format('D M d Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;">AWB Print Date</td>
                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{ now()->format('D M d Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        {{ $order->transaction ? 'NON-COD' : 'COD' }}
                                    </td>
                                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">
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
