<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Reset Your Password - ZOLPA STORE</title>
</head>

<body
    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;background-color: #f4f4f4;margin: 0;padding: 0;line-height: 1.6;">

    <!-- Dark mode styles -->
    <style>
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1a1a !important;
            }

            .email-container {
                background-color: #2d2d2d !important;
                color: #ffffff !important;
                border-color: #404040 !important;
            }

            .code-container {
                background-color: #3d3d3d !important;
            }

            .contact-info {
                color: #cccccc !important;
            }

            .footer-text {
                color: #999999 !important;
            }

            .zolpa-logo {
                filter: brightness(0) invert(1);
            }
        }
    </style>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <div class="email-container"
                    style="background-color: white;border-radius: 12px;box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);width: 100%;border: #e6f3ff 2px solid;max-width: 500px;margin: 30px auto;padding: 30px;text-align: center;">
                    <div style="margin-bottom: 20px;width: 100%;">
                        <img src="https://www.singingbowlvillagenepal.com/assets/logo/logo3.png"
                            alt="SingingBowl Logo" class="zolpa-logo"
                            style="width: auto;height: 80px;display: block;margin: 0 auto;object-fit: contain;">
                    </div>
                    <h1 style="color: #333;font-size: 24px;margin-bottom: 15px;">Password Reset</h1>
                    <div class="code-container"
                        style="background-color: #f9f9f9;padding: 20px;border-radius: 10px;margin-bottom: 20px;">
                        <div style="color: #666;margin-bottom: 25px;line-height: 1.6;">
                            @php
                                $fullText = $data->description;
                                // Extract code from patterns like "Your OTP Code: 123456" or "code: 123456"
                                preg_match('/(?:Your OTP Code|code):\s*(\d+)/i', $fullText, $codeMatches);
                                $code = $codeMatches[1] ?? '';

                                // Split text into parts before and after the code
                                if ($code) {
                                    $parts = preg_split('/(?:Your OTP Code|code):\s*\d+/i', $fullText);
                                    $beforeCode = trim($parts[0] ?? '');
                                    $afterCode = trim($parts[1] ?? '');
                                } else {
                                    // Fallback: try to extract any 6-digit number as code
                                    preg_match('/(\d{6})/', $fullText, $fallbackMatches);
                                    $code = $fallbackMatches[1] ?? '';
                                    if ($code) {
                                        $parts = preg_split('/\d{6}/', $fullText);
                                        $beforeCode = trim($parts[0] ?? '');
                                        $afterCode = trim($parts[1] ?? '');
                                    } else {
                                        $beforeCode = $fullText;
                                        $afterCode = '';
                                    }
                                }
                            @endphp

                            @if($beforeCode)
                                <p>{{ $beforeCode }}</p>
                            @endif
                        </div>

                        @if($code)
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <div style="margin: 20px 0;">
                                            <span
                                                style="display: inline-block;background: #fff;border: 2px solid #0066cc;padding: 12px 20px;border-radius: 8px;font-weight: bold;font-size: 24px;letter-spacing: 2px;color: #0066cc;">{{ $code }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        @if($afterCode)
                            <div style="margin-top: 15px;color: #666;margin-bottom: 25px;line-height: 1.6;">
                                <p>{{ $afterCode }}</p>
                            </div>
                        @endif

                        @if(!$code)
                            {{-- Fallback: if no code found, show the full description --}}
                            <p>{!! nl2br(e($data->description)) !!}</p>
                        @endif
                    </div>
                    <div style="color: #ff0000;font-size: 14px;margin-bottom: 20px;">
                        If you did not request a password reset, please ignore this email or contact our support team if
                        you have concerns.
                    </div>
                    <div class="contact-info" style="color: #666;font-size: 14px;margin-top: 20px;">
                        Need help? Contact us at <a href="mailto:support@zolpastore.com"
                            style="color: #0066cc;text-decoration: none;">support@zolpastore.com</a>
                    </div>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center" style="margin-top: 20px;">
                                <a href="https://www.instagram.com/zolpa.storenp/"
                                    style="display: inline-block;margin: 0 10px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"
                                        style="width: 24px;height: 24px;"></a>
                                <a href="https://www.tiktok.com/@zolpa.store"
                                    style="display: inline-block;margin: 0 10px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" alt="TikTok"
                                        style="width: 24px;height: 24px;"></a>
                                <a href="https://www.facebook.com/share/1LUCsGXkmK/"
                                    style="display: inline-block;margin: 0 10px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/1384/1384005.png" alt="Facebook"
                                        style="width: 24px;height: 24px;"></a>
                                <a href="https://www.linkedin.com/in/zolpa-store-36097536a"
                                    style="display: inline-block;margin: 0 10px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn"
                                        style="width: 24px;height: 24px;"></a>
                            </td>
                        </tr>
                    </table>
                    <div style="color: #999;font-size: 12px;margin-top: 30px;">
                        © {{ now()->year }} ZOLPA STORE. All rights reserved.
                        <br>
                        <a href="#" style="color: #999;text-decoration: none;margin: 0 10px;">Privacy Policy</a> | <a
                            href="#" style="color: #999;text-decoration: none;margin: 0 10px;">Terms of Service</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
<a href="#" style="display: inline-block;margin: 0 10px;"><img
        src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"
        style="width: 24px;height: 24px;"></a>
<a href="#" style="display: inline-block;margin: 0 10px;"><img
        src="https://cdn-icons-png.flaticon.com/512/733/733635.png" alt="Twitter" style="width: 24px;height: 24px;"></a>
<a href="#" style="display: inline-block;margin: 0 10px;"><img
        src="https://cdn-icons-png.flaticon.com/512/1384/1384005.png" alt="Facebook"
        style="width: 24px;height: 24px;"></a>
<a href="#" style="display: inline-block;margin: 0 10px;"><img
        src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn"
        style="width: 24px;height: 24px;"></a>
</td>
</tr>
</table>
<div style="color: #999;font-size: 12px;margin-top: 30px;">
    &copy; {{ now()->year }} ZOLPA STORE. All rights reserved.
    <br>
    <a href="#" style="color: #999;text-decoration: none;margin: 0 10px;">Privacy Policy</a> | <a href="#"
        style="color: #999;text-decoration: none;margin: 0 10px;">Terms of Service</a>
</div>
</div>
</td>
</tr>
</table>
</body>

</html>