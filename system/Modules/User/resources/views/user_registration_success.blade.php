<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Welcome to Singing Bowl Village</title>

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

        .social-media {
            margin-top: 20px;
        }

        .social-media a {
            display: inline-block;
            margin: 0 10px;
        }

        .social-media img {
            width: 24px;
            height: 24px;
        }

        .footer {
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .footer a {
            color: #7f8c8d;
            text-decoration: none;
            margin: 0 10px;
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

            .footer a {
                color: #999999 !important;
            }

            .footer a:hover {
                color: #66b3ff !important;
            }

            hr {
                background-color: #404040 !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="https://www.singingbowlvillagenepal.com/assets/logo/logo3.png"
                alt="Singing Bowl Village Logo" style="height: 60px; width: auto; object-fit: contain;">
        </div>
        <h2 style="font-weight: normal !important; font-size: 18px; color: #2c3e50; margin-top: 0;">Account Registration
            Successful</h2>
        <div class=description>
            <p style="font-weight: normal !important; font-size: 18px; margin-top: 0;">
                @php
                    $parts = explode(',', $sendRegisterEmailDTO->description, 2);
                    $firstPart = '<strong>' . $parts[0] . '</strong>';

                    // Find and make email bold in the second part
                    if (isset($parts[1])) {
                        preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $parts[1], $matches);
                        if (!empty($matches)) {
                            $email = $matches[0];
                            $parts[1] = str_replace($email, '<strong>' . $email . '</strong>', $parts[1]);
                        }
                    }

                    echo $firstPart . (isset($parts[1]) ? ',<br>' . $parts[1] : '');
                @endphp
            </p>
        </div>

        <hr>
        <div class="contact-info">
            <p>Need help? Contact us at <a href="mailto:singingbowlvillagenepal@gmail.com">singingbowlvillagenepal@gmail.com</a></p>
            <p>Phone: 977 985-1352794</p>
            <p>WhatsApp: <a href="https://wa.me/9779851352794">977 985-1352794</a></p>
        </div>
        <div class="social-media">
            <a href="https://www.instagram.com/singingbowlvillage/?igsh=MTkxcDY0YzNvbWQyNg%3D%3D&utm_source=qr"><img
                    src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"></a>
            <a href="https://www.facebook.com/people/Singing-Bowl/61550698021090/"><img
                    src="https://cdn-icons-png.flaticon.com/512/1384/1384005.png" alt="Facebook"></a>
            <a href="https://www.youtube.com/@singingbowlvillage"><img
                    src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="YouTube"></a>
            <a href="https://wa.me/9779851352794"><img
                    src="https://cdn-icons-png.flaticon.com/512/733/733585.png" alt="WhatsApp"></a>
        </div>
        <div class="footer">
            &copy; {{ now()->year }} {{ env('STORE_NAME', 'Singing Bowl Village') }}. All rights reserved.<br>
            <a href="https://www.singingbowlvillagenepal.com/privacy-policy">Privacy Policy</a> | <a href="https://www.singingbowlvillagenepal.com/terms-and-condition">Terms of Service</a>
        </div>
    </div>
</body>

</html>