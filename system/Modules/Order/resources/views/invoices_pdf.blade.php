<!DOCTYPE html>
<html lang="en" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; -webkit-print-color-adjust: exact;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Open+Sans:400,600" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            font-size: 15px;
            min-width: 320px;
            color: #444444;
            margin: 0;
            background-color: #f9f9f9;
        }

        .invoice-container {
            border-collapse: collapse;
            min-width: 320px;
            max-width: 900px;
            width: 100%;
            margin: auto;
            border-bottom: 2px solid #6A3BAF;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .header {
            background: linear-gradient(135deg, #6A3BAF 0%, #8854D0 100%);
            padding: 25px 0;
        }

        .header-content {
            padding: 0 30px;
        }

        .company-info {
            color: #ffffff;
        }

        .logo-container {
            margin-right: 15px;
        }

        .logo {
            max-width: 120px;
            height: auto;
        }

        .company-text {
            padding-left: 15px;
        }

        .company-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 1px;
            margin: 0 0 5px;
        }

        .company-details {
            font-size: 14px;
            line-height: 1.6;
        }

        .invoice-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 44px;
            line-height: 54px;
            color: #ffffff;
            text-align: right;
            margin: 3px 0 10px;
        }

        .invoice-details {
            width: 250px;
            margin: 15px 0 0 auto;
            color: #ffffff;
        }

        .invoice-detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .invoice-body {
            padding: 30px;
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 18px;
            margin: 0 0 15px;
            color: #6A3BAF;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 8px;
        }

        .address-info {
            margin-bottom: 30px;
        }

        .billing-address,
        .shipping-address {
            flex: 1;
            min-width: 250px;
        }

        .address-table {
            width: 100%;
            border-collapse: collapse;
        }

        .address-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .address-label {
            width: 35%;
            font-weight: 600;
            color: #555555;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: 'Open Sans', sans-serif;
        }

        .products-table th {
            background-color: #6A3BAF;
            color: #ffffff;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #e0e0e0;
        }

        .products-table td {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
        }

        .products-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #333333;
            word-break: break-word;
        }

        .product-option {
            font-size: 14px;
            margin-top: 5px;
            color: #555555;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .color-swatch {
            display: inline-block;
            width: 12px;
            height: 12px;
            vertical-align: middle;
            margin-right: 5px;
            border-radius: 50%;
            border: 1px solid #d0d0d0;
        }

        .totals-table {
            width: 350px;
            margin-left: auto;
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .totals-table td {
            padding: 8px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .totals-table tr:last-child td {
            font-weight: 700;
            font-size: 16px;
            background-color: rgba(106, 59, 175, 0.1);
            border-bottom: 2px solid #6A3BAF;
            color: #6A3BAF;
        }

        .total-label {
            text-align: right;
        }

        .total-value {
            text-align: right;
            width: 120px;
        }

        .footer {
            text-align: center;
            padding: 20px 30px;
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #666666;
        }

        .thank-you {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 16px;
            color: #DB5E18;
            margin-bottom: 10px;
        }

        .payment-note {
            margin-top: 15px;
            padding: 10px;
            background-color: rgba(219, 94, 24, 0.05);
            border-left: 3px solid #DB5E18;
            font-size: 14px;
        }

        @media screen and (max-width: 767px) {
            .header-content {
                flex-direction: column;
            }

            .invoice-title {
                text-align: left;
                margin-top: 20px;
            }

            .invoice-details {
                margin: 15px 0 0 0;
            }

            .invoice-body {
                padding: 20px 15px;
            }

            .address-info {
                flex-direction: column;
                gap: 20px;
            }

            .billing-address,
            .shipping-address {
                width: 100%;
            }

            .totals-table {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <table class="invoice-container">
        <tbody>
            <tr>
                <td class="header">
                    <table style="width: 100%;">
                        <tr>
                            <td style="vertical-align: middle;">
                                <div class="company-info">
                                    <div class="logo-container">
                                        <img src="https://uat.zolpastore.com/_next/image?url=%2Fimages%2FLayer_1.png&w=1080&q=75"
                                            alt="ZOLPA STORE" class="logo">
                                    </div>
                                    <div class="company-text">
                                        <div class="company-details">
                                            Putalisadak, Kathmandu, Nepal<br>
                                            Phone: +977-01-4123456<br>
                                            Email: info@zolpastore.com<br>
                                            PAN: 123456789
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align: middle; text-align: right;">
                                <div class="invoice-title">INVOICE</div>
                                <div class="invoice-details">
                                    <div class="invoice-detail-row" style="display: flex; justify-content: space-between;">
                                        <span>Invoice No:</span>
                                        <span>#{{$orderData['id']}}</span>
                                    </div>
                                    <div class="invoice-detail-row" style="display: flex; justify-content: space-between;">
                                        <span>Date Issued:</span>
                                        <span>{{ $orderData['createdAt'] }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="invoice-body">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="billing-address">
                                    <h5 class="section-title">Billing Address</h5>
                                    <table class="address-table">
                                        <tr>
                                            <td class="address-label">Name:</td>
                                            <td>{{ $orderData['addressInformation']['name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">Mobile:</td>
                                            <td>{{ $orderData['addressInformation']['mobile'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">Email:</td>
                                            <td>{{ $orderData['addressInformation']['email'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">Address:</td>
                                            <td>{{ $orderData['addressInformation']['address'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">Zone:</td>
                                            <td>{{ $orderData['addressInformation']['zone'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">City/Province:</td>
                                            <td>{{ $orderData['addressInformation']['city'] }},
                                                {{ $orderData['addressInformation']['province'] }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <div class="shipping-address">
                                    <h5 class="section-title">Order Details</h5>
                                    <table class="address-table">
                                        <tr>
                                            <td class="address-label">Order Date:</td>
                                            <td>{{ $orderData['createdAt'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">Order ID:</td>
                                            <td>#{{ $orderData['id'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">Payment Method:</td>
                                            <td>{{ $orderData['paymentMethod'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="address-label">Payment Status:</td>
                                            <td><span
                                                    style="background-color: {{ isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'paid' ? '#c8e6c9' : (isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'pending' ? '#fff3cd' : '#f8d7da') }}; color: {{ isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'paid' ? '#155724' : (isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'pending' ? '#856404' : '#721c24') }}; padding: 3px 8px; border-radius: 3px; font-size: 13px;">{{ isset($orderData['paymentStatus']) ? ucfirst($orderData['paymentStatus']) : ($orderData['paymentMethod'] == 'Cash on Delivery' ? 'Pending' : 'Unknown') }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <h5 class="section-title">Order Items</h5>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th style="width: 55%;">Product</th>
                                <th style="width: 15%;" class="text-center">Quantity</th>
                                <th style="width: 15%;" class="text-right">Unit Price</th>
                                <th style="width: 15%;" class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderData['itemsOrdered'] as $product)
                                <tr>
                                    <td>
                                        <div class="product-name">{{ $product['name'] }}</div>
                                        @for ($i = 1; $i <= 3; $i++)
                                            @if (!empty($product['optionName' . $i]))
                                                <div class="product-option">
                                                    <strong>{{ $product['optionName' . $i] }}:</strong>
                                                    @if (!empty($product['optionData' . $i]))
                                                        <span class="color-swatch"
                                                            style="background-color: {{ $product['optionData' . $i] }};"></span>
                                                    @endif
                                                    {{ $product['optionValue' . $i] }}
                                                </div>
                                            @endif
                                        @endfor
                                        <div style="margin-top: 8px; font-size: 12px;">
                                            <span
                                                style="display: inline-block; padding: 2px 6px; background-color: rgba(219, 94, 24, 0.15); color: #DB5E18; border-radius: 3px;">Genuine
                                                Product</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $product['quantity'] }}</td>
                                    <td class="text-right">Rs
                                        {{ number_format($product['lineTotal'] / $product['quantity'], 2) }}
                                    </td>
                                    <td class="text-right">Rs {{ number_format($product['lineTotal'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="totals-table">
                        <tr>
                            <td class="total-label">Subtotal:</td>
                            <td class="total-value">Rs {{ number_format($orderData['subtotal'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">Discount:</td>
                            <td class="total-value">-Rs {{ number_format($orderData['discount'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">Shipping Cost:</td>
                            <td class="total-value">Rs 0.00</td>
                        </tr>
                        <tr>
                            <td class="total-label">Tax (13% VAT):</td>
                            <td class="total-value">Rs {{ number_format($orderData['total'] * 0.13, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">Total Amount:</td>
                            <td class="total-value">Rs {{ number_format($orderData['total'], 2) }}</td>
                        </tr>
                    </table>

                    <div class="payment-note">
                        <strong>Payment Information:</strong> Please ensure the payment reference includes your invoice
                        number when making a payment.
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    @php
                        $status = $orderData['status'] ?? 'Order Placed';
                        $thankYouMessage = 'Thank You!';
                        $footerMessage = 'If you have any questions about this invoice, please contact us at:';

                        // Dynamic messages based on order status
                        switch ($status) {
                            case 'Delivered':
                                $thankYouMessage = 'Order Delivered Successfully!';
                                $footerMessage = 'Thank you for choosing ZOLPA STORE. We hope you love your purchase!';
                                break;
                            case 'Cancelled':
                                $thankYouMessage = 'Order Cancelled';
                                $footerMessage = 'Your order has been cancelled. If you have any questions, please contact us at:';
                                break;
                            case 'Failed':
                                $thankYouMessage = 'Order Processing Failed';
                                $footerMessage = 'There was an issue processing your order. Please contact us for assistance at:';
                                break;
                            case 'Failed Delivery':
                                $thankYouMessage = 'Delivery Attempt Failed';
                                $footerMessage = 'We were unable to deliver your order. Please contact us to reschedule delivery at:';
                                break;
                            case 'Refunded':
                                $thankYouMessage = 'Refund Processed';
                                $footerMessage = 'Your refund has been processed. If you have any questions, please contact us at:';
                                break;
                            case 'Awaiting Refund':
                                $thankYouMessage = 'Refund in Progress';
                                $footerMessage = 'Your refund is being processed. For updates, please contact us at:';
                                break;
                            case 'Partially Refunded':
                                $thankYouMessage = 'Partial Refund Processed';
                                $footerMessage = 'A partial refund has been processed for your order. For details, contact us at:';
                                break;
                            case 'Shipped':
                                $thankYouMessage = 'Order Shipped!';
                                $footerMessage = 'Your order is on its way! For tracking information, please contact us at:';
                                break;
                            case 'Ready To Ship':
                                $thankYouMessage = 'Order Ready for Shipment';
                                $footerMessage = 'Your order is being prepared for shipment. For updates, contact us at:';
                                break;
                            case 'Pending Payment':
                                $thankYouMessage = 'Payment Pending';
                                $footerMessage = 'Please complete your payment to process this order. For assistance, contact us at:';
                                break;
                            case 'On Hold':
                                $thankYouMessage = 'Order on Hold';
                                $footerMessage = 'Your order is temporarily on hold. For more information, please contact us at:';
                                break;
                            default:
                                $thankYouMessage = 'Thank You!';
                                $footerMessage = 'Thank you for your order! If you have any questions, please contact us at:';
                                break;
                        }
                    @endphp

                    <div class="thank-you">{{ $thankYouMessage }}</div>
                    <div>{{ $footerMessage }}</div>
                    <div>Phone: +977-01-4123456 | Email: support@zolpastore.com</div>
                    <div style="margin-top: 10px;">
                        <strong style="color: #6A3BAF;">ZOLPA STORE</strong> | Putalisadak, Kathmandu, Nepal |
                        www.zolpastore.com
                    </div>
                    <div style="margin-top: 15px; font-size: 12px; color: #888;">
                        All prices are in Nepalese Rupees (Rs). Payment terms as per agreed conditions.
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>