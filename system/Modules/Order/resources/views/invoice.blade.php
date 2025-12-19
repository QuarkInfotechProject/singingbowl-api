<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $orderInvoice->title ?? 'Order Invoice' }}</title>

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
            border-left: 4px solid #DB5E18;
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 15px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1a1a !important;
            }

            .container {
                background-color: #2d2d2d !important;
                color: #ffffff !important;
                border-color: #404040 !important;
            }

            .description {
                color: #ffffff !important;
            }

            .logo img {
                filter: brightness(0) invert(1);
            }

            .contact-info {
                color: #cccccc !important;
            }

            .contact-info a {
                color: #66b3ff !important;
            }

            .footer {
                color: #999999 !important;
                border-top-color: #404040 !important;
            }

            .thank-you-message {
                background-color: #3d3d3d !important;
                border-left-color: #DB5E18 !important;
            }

            hr {
                background-color: #404040 !important;
            }
        }
    </style>
</head>

<body
    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px 0; line-height: 1.6; text-align: center;">
    <div
        style="background-color: white; border-radius: 8px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); width: 100%; max-width: 600px; margin: 20px auto; padding: 0 0 20px 0; text-align: center; border: 2px solid #f1f1f1; overflow: hidden;">
        <div style="width: 100%; margin: 0 0 15px 0; padding: 0;">
            <img src="https://www.singingbowlvillagenepal.com/assets/logo/logo3.png" alt="Singing Bowl Village"
                style="width: auto; height: 60px; display: block; margin: 0 auto; object-fit: contain;">
        </div>

        <h2 style="font-weight: normal; font-size: 24px; color: #2c3e50; margin-top: 0; padding: 0 20px;">
            {{ $orderInvoice->title }}
        </h2>

        <div style="font-size: 18px; text-align: left; color: #2c3e50; margin-top: 20px; padding: 0 20px;">
            {!! $orderInvoice->description !!}
        </div>

        <div
            style="background-color: #f8f9fa; border-radius: 6px; padding: 15px 20px; margin: 20px 20px; text-align: center; border-left: 4px solid #DB5E18;">
            <h3 style="color: #2c3e50; margin-top: 0; font-size: 18px;">{{ $orderInvoice->title }}</h3>
            <p style="color: #5a6268; margin-bottom: 0;">{!! $orderInvoice->message !!}</p>
        </div>

        <hr style="border: none; height: 1px; background-color: #e9ecef; margin: 20px 20px;">

        <div style="color: #666; font-size: 14px; margin-top: 20px; padding: 0 20px;">
            <p>Need help? Contact us at <a href="mailto:support@singingbowlvillagenepal.com"
                    style="color: #DB5E18; text-decoration: none;">support@singingbowlvillagenepal.com</a></p>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="https://www.instagram.com/singingbowlvillagenepal" style="display: inline-block; margin: 0 8px;"><img
                    src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"
                    style="width: 28px; height: 28px;"></a>
            <a href="https://www.tiktok.com/@singingbowlvillagenepal" style="display: inline-block; margin: 0 8px;"><img
                    src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" alt="TikTok"
                    style="width: 28px; height: 28px;"></a>
            <a href="https://www.facebook.com/singingbowlvillagenepal" style="display: inline-block; margin: 0 8px;"><img
                    src="https://cdn-icons-png.flaticon.com/512/1384/1384005.png" alt="Facebook"
                    style="width: 28px; height: 28px;"></a>
            <a href="https://www.linkedin.com/company/singingbowlvillagenepal"
                style="display: inline-block; margin: 0 8px;"><img
                    src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn"
                    style="width: 28px; height: 28px;"></a>
        </div>

        <div
            style="text-align: center; color: #7f8c8d; font-size: 14px; margin-top: 30px; padding: 20px 20px 0; border-top: 1px solid #e9ecef;">
            &copy; {{ now()->year }} {{ env('STORE_NAME', 'Singing Bowl Village') }}. All rights reserved.<br>
            <a href="#" style="color: #7f8c8d; text-decoration: none; margin: 0 10px;">Privacy Policy</a> | <a href="#"
                style="color: #7f8c8d; text-decoration: none; margin: 0 10px;">Terms of Service</a>
        </div>
    </div>
</body>

</html>