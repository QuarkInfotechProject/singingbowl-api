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
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
        }

        .company-info {
            color: #ffffff;
            display: flex;
            align-items: center;
        }

        .logo-container {
            margin-right: 20px;
        }

        .logo {
            max-width: 150px;
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
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
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
                    <div class="header-content">
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
                        <div>
                            <div class="invoice-title">INVOICE</div>
                            <div class="invoice-details">
                                <div class="invoice-detail-row">
                                    <span>Invoice No:</span>
                                    <span>#<?php echo e($orderData['id']); ?></span>
                                </div>
                                <div class="invoice-detail-row">
                                    <span>Date Issued:</span>
                                    <span><?php echo e($orderData['createdAt']); ?></span>
                                </div>
                                <div class="invoice-detail-row">
                                    <span>Due Date:</span>
                                    <span>Immediate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="invoice-body">
                    <div class="address-info">
                        <div class="billing-address">
                            <h5 class="section-title">Billing Address</h5>
                            <table class="address-table">
                                <tr>
                                    <td class="address-label">Name:</td>
                                    <td><?php echo e($orderData['addressInformation']['name']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">Mobile:</td>
                                    <td><?php echo e($orderData['addressInformation']['mobile']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">Email:</td>
                                    <td><?php echo e($orderData['addressInformation']['email']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">Address:</td>
                                    <td><?php echo e($orderData['addressInformation']['address']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">Zone:</td>
                                    <td><?php echo e($orderData['addressInformation']['zone']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">City/Province:</td>
                                    <td><?php echo e($orderData['addressInformation']['city']); ?>,
                                        <?php echo e($orderData['addressInformation']['province']); ?>

                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="shipping-address">
                            <h5 class="section-title">Order Details</h5>
                            <table class="address-table">
                                <tr>
                                    <td class="address-label">Order Date:</td>
                                    <td><?php echo e($orderData['createdAt']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">Order ID:</td>
                                    <td>#<?php echo e($orderData['id']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">Payment Method:</td>
                                    <td><?php echo e($orderData['paymentMethod']); ?></td>
                                </tr>
                                <tr>
                                    <td class="address-label">Payment Status:</td>
                                    <td><span
                                            style="background-color: <?php echo e(isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'paid' ? '#c8e6c9' : (isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'pending' ? '#fff3cd' : '#f8d7da')); ?>; color: <?php echo e(isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'paid' ? '#155724' : (isset($orderData['paymentStatus']) && $orderData['paymentStatus'] == 'pending' ? '#856404' : '#721c24')); ?>; padding: 3px 8px; border-radius: 3px; font-size: 13px;"><?php echo e(isset($orderData['paymentStatus']) ? ucfirst($orderData['paymentStatus']) : ($orderData['paymentMethod'] == 'Cash on Delivery' ? 'Pending' : 'Unknown')); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h5 class="section-title">Order Items</h5>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Product</th>
                                <th style="width: 15%;" class="text-center">Quantity</th>
                                <th style="width: 20%;" class="text-right">Unit Price</th>
                                <th style="width: 20%;" class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $orderData['itemsOrdered']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="product-name"><?php echo e($product['name']); ?></div>
                                        <?php for($i = 1; $i <= 3; $i++): ?>
                                            <?php if(!empty($product['optionName' . $i])): ?>
                                                <div class="product-option">
                                                    <strong><?php echo e($product['optionName' . $i]); ?>:</strong>
                                                    <?php if(!empty($product['optionData' . $i])): ?>
                                                        <span class="color-swatch"
                                                            style="background-color: <?php echo e($product['optionData' . $i]); ?>;"></span>
                                                    <?php endif; ?>
                                                    <?php echo e($product['optionValue' . $i]); ?>

                                                </div>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <div style="margin-top: 8px; font-size: 12px;">
                                            <span
                                                style="display: inline-block; padding: 2px 6px; background-color: rgba(219, 94, 24, 0.15); color: #DB5E18; border-radius: 3px;">Genuine
                                                Product</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo e($product['quantity']); ?></td>
                                    <td class="text-right">Rs
                                        <?php echo e(number_format($product['lineTotal'] / $product['quantity'], 2)); ?>

                                    </td>
                                    <td class="text-right">Rs <?php echo e(number_format($product['lineTotal'], 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <table class="totals-table">
                        <tr>
                            <td class="total-label">Subtotal:</td>
                            <td class="total-value">Rs <?php echo e(number_format($orderData['subtotal'], 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="total-label">Discount:</td>
                            <td class="total-value">-Rs <?php echo e(number_format($orderData['discount'], 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="total-label">Shipping Cost:</td>
                            <td class="total-value">Rs 0.00</td>
                        </tr>
                        <tr>
                            <td class="total-label">Tax (13% VAT):</td>
                            <td class="total-value">Rs <?php echo e(number_format($orderData['total'] * 0.13, 2)); ?></td>
                        </tr>
                        <tr>
                            <td class="total-label">Total Amount:</td>
                            <td class="total-value">Rs <?php echo e(number_format($orderData['total'], 2)); ?></td>
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
                    <div class="thank-you">Thank You!</div>
                    <div>If you have any questions about this invoice, please contact us at:</div>
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

</html><?php /**PATH /opt/www/Modules/Order/resources/views/invoices_pdf.blade.php ENDPATH**/ ?>