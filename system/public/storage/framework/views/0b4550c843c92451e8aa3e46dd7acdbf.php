<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            align-items: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2) !important;
            border: 2px solid #f1f1f1;
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

        .description {
            font-size: 20px;
            font-weight: 500;
            color: #2c3e50;
            margin-top: 0;
        }

        h1 {
            font-size: 24px;
            text-align: center;
            font-weight: 500;
            color: #2c3e50;
            margin-top: 0;
        }

        hr {
            border: none;
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2ecc71;
            /* Modern green color */
            color: white !important;
            text-decoration: none;
            font-weight: 600;
            border-radius: 25px;
            /* Rounder corners */
            margin: 20px 0;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #27ae60;
            /* Darker green on hover */
        }

        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 30px;
        }

        .notice {
            font-size: 16px;
            color: #7f8c8d;
            margin-top: 20px;
        }

        .cta-container {
            text-align: center;
            margin: 30px 0;
        }

        .logo p {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
            font-family: 'Arial', sans-serif;

        }

        .social-media {
            margin-top: 20px;
            text-align: center
        }

        .social-media a {
            display: inline-block;
            margin: 0 10px;
        }

        .social-media img {
            width: 24px;
            height: 24px;
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 15px;
            }

            .button {
                display: block;
                width: 100%;
            }

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="<?php echo e(asset('https://www.liblogo.com/img-logo/la250lec9-laravel-logo-laravel-logo-air.png')); ?>"
                alt="FusionHub Logo">
        </div>
        <h1>Password Reset</h1>

        <div class="description">
            <p><?php echo preg_replace('/(\w+\s+\w+)!/', '<strong>$1</strong>!', preg_replace('/\s{2,}/', '<br>', $data->description)); ?>

            </p>
        </div>
        <hr>

        <div class="cta-container">
            <a href="<?php echo e($passwordResetUrl); ?>" target="_blank" class="button">RESET YOUR PASSWORD</a>
        </div>

        <p class="notice" style="color: #ff0000;">If you did not request a password reset, please ignore this email or
            contact our support team
            if you have concerns.</p><br>

        <div class="contact-info">
            <p>Need help? Contact us at <a href="mailto:support@example.com"
                    style="color: #3498db; text-decoration: none;">support@example.com</a></p>
        </div>

        <p class="notice">This email was sent to you as part of our security procedures. We're committed to keeping
            your
            account safe.</p>

        <div class="social-media">
            <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"></a>
            <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733635.png" alt="Twitter"></a>
            <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/1384/1384005.png" alt="Facebook"></a>
            <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn"></a>
        </div>

        <p class="footer">&copy; <?php echo e(now()->year); ?> <?php echo e(env('STORE_NAME', 'FUSIONHUB')); ?>. All rights reserved.<br>
            <a href="#" style="color: #7f8c8d;">Privacy Policy</a> | <a href="#" style="color: #7f8c8d;">Terms of
                Service</a>
        </p>
    </div>
</body>

</html><?php /**PATH /opt/www/Modules/User/resources/views/reset_password.blade.php ENDPATH**/ ?>