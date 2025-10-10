<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            text-align: center;
            border: 2px solid #f1f1f1;
        }

        .description {
            font-size: 20px;
            text-align: left;
            font-weight: 500;
            color: #2c3e50;
            margin-top: 0;
        }

        .logo {
            margin-bottom: 20px;
            width: 100%;
        }

        .logo img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        hr {
            border: none;
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }

        .contact-info {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .thank-you-message {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 15px 20px;
            margin: 20px 0;
            text-align: center;
            border-left: 4px solid #52b8d8;
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>

<body
    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px 0; line-height: 1.6; text-align: center;">
    <div
        style="background-color: white; border-radius: 8px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); width: 100%; max-width: 600px; margin: 20px auto; padding: 0 0 20px 0; text-align: center; border: 2px solid #f1f1f1; overflow: hidden;">
        <div style="width: 100%; margin: 0 0 25px 0; padding: 0;">
            <img src="<?php echo e(asset('https://www.liblogo.com/img-logo/la250lec9-laravel-logo-laravel-logo-air.png')); ?>"
                alt="Logo"
                style="width: 100%; display: block; max-height: 180px; object-fit: contain; background-color: #f5f5f5; padding: 20px 0;">
        </div>

        <h2 style="font-weight: normal; font-size: 24px; color: #2c3e50; margin-top: 0; padding: 0 20px;">
            <?php echo e($orderInvoice->title); ?>

        </h2>

        <div style="font-size: 18px; text-align: left; color: #2c3e50; margin-top: 20px; padding: 0 20px;">
            <?php echo $orderInvoice->description; ?>

        </div>

        <div
            style="background-color: #f8f9fa; border-radius: 6px; padding: 15px 20px; margin: 20px 20px; text-align: center; border-left: 4px solid #52b8d8;">
            <h3 style="color: #2c3e50; margin-top: 0; font-size: 18px;">Thank You for Your Purchase!</h3>
            <p style="color: #5a6268; margin-bottom: 0;">We truly appreciate your business and trust in our products.
                Your satisfaction is our top priority, and we're committed to providing you with the best possible
                service. We look forward to serving you again soon!</p>
        </div>

        <hr style="border: none; height: 1px; background-color: #e9ecef; margin: 20px 20px;">

        <div style="color: #666; font-size: 14px; margin-top: 20px; padding: 0 20px;">
            <p>Need help? Contact us at <a href="mailto:support@example.com"
                    style="color: #52b8d8; text-decoration: none;">support@example.com</a></p>
        </div>

        <div
            style="text-align: center; color: #7f8c8d; font-size: 14px; margin-top: 30px; padding: 20px 20px 0; border-top: 1px solid #e9ecef;">
            &copy; <?php echo e(now()->year); ?> <?php echo e(env('STORE_NAME', 'ZOLPA STORE')); ?>. All rights reserved.<br>
            <a href="#" style="color: #7f8c8d; text-decoration: none; margin: 0 10px;">Privacy Policy</a> | <a href="#"
                style="color: #7f8c8d; text-decoration: none; margin: 0 10px;">Terms of Service</a>
        </div>
    </div>
</body>

</html><?php /**PATH /opt/www/Modules/Order/resources/views/invoice.blade.php ENDPATH**/ ?>