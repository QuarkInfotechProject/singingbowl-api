<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $sendRegisterEmailDTO->title ?? 'ZOLPA STORE' }}</title>
</head>

<body
    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;background-color: #f4f4f4;margin: 0;padding: 0;line-height: 1.6;">
    
    <!-- Dark mode styles -->
    <style>
        @media (prefers-color-scheme: dark) {
            body { background-color: #1a1a1a !important; }
            .email-container { background-color: #2d2d2d !important; color: #ffffff !important; border-color: #404040 !important; }
            .code-container { background-color: #3d3d3d !important; }
            .contact-info { color: #cccccc !important; }
            .footer-text { color: #999999 !important; }
            .zolpa-logo { filter: brightness(0) invert(1); }
        }
    </style>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <div class="email-container"
                    style="background-color: white;border-radius: 12px;box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);width: 100%;border: #e6f3ff 2px solid;max-width: 500px;margin: 30px auto;padding: 30px;text-align: center;">
                    
                    <!-- ZOLPA Logo -->
                    <div style="margin-bottom: 20px;width: 100%;">
                        <img src="https://uat.zolpastore.com/_next/image?url=%2Fimages%2FLayer_1.png&w=1080&q=75"
                            alt="ZOLPA STORE Logo" class="zolpa-logo" style="height: 60px;width: auto;object-fit: contain;">
                    </div>
                    
                    <h1 style="color: #333;font-size: 24px;margin-bottom: 15px;font-weight: 600;">{{ $sendRegisterEmailDTO->title ?? 'You\'re Almost There!' }}</h1>
                    
                    <div class="code-container" style="background-color: #f9f9f9;padding: 20px;border-radius: 10px;margin-bottom: 20px;">
                        <div style="color: #666;margin-bottom: 25px;line-height: 1.6;">
                            {{ $sendRegisterEmailDTO->description }}
                        </div>
                        
                        <!-- Verification Code Display -->
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <span style="display: inline-block;background: #fff;border: 2px solid #0066cc;padding: 12px 20px;border-radius: 8px;font-weight: bold;font-size: 18px;letter-spacing: 2px;color: #0066cc;">{{ $sendRegisterEmailDTO->code }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Security Warning -->
                    <div style="color: #dc3545;font-size: 14px;margin-bottom: 20px;background-color: #fff3cd;padding: 10px;border-radius: 6px;border-left: 4px solid #ffc107;">
                        <strong>⚠️ Security Notice:</strong> Do not share this code with anyone under any circumstances!
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="contact-info" style="color: #666;font-size: 14px;margin-top: 20px;">
                        Need help? Contact us at <a href="mailto:support@zolpastore.com" style="color: #0066cc;text-decoration: none;">support@zolpastore.com</a>
                    </div>
                    
                    <!-- Social Media Links -->
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: 25px;">
                        <tr>
                            <td align="center">
                                <a href="https://www.instagram.com/zolpa.storenp/" style="display: inline-block;margin: 0 8px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"
                                        style="width: 28px;height: 28px;"></a>
                                <a href="https://www.tiktok.com/@zolpa.store" style="display: inline-block;margin: 0 8px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" alt="TikTok"
                                        style="width: 28px;height: 28px;"></a>
                                <a href="https://www.facebook.com/share/1LUCsGXkmK/" style="display: inline-block;margin: 0 8px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/1384/1384005.png" alt="Facebook"
                                        style="width: 28px;height: 28px;"></a>
                                <a href="https://www.linkedin.com/in/zolpa-store-36097536a" style="display: inline-block;margin: 0 8px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/174/174857.png" alt="LinkedIn"
                                        style="width: 28px;height: 28px;"></a>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- Footer -->
                    <div class="footer-text" style="color: #999;font-size: 12px;margin-top: 30px;border-top: 1px solid #e9ecef;padding-top: 20px;">
                        © {{ now()->year }} ZOLPA STORE. All rights reserved.
                        <br><br>
                        <a href="#" style="color: #999;text-decoration: none;margin: 0 10px;">Privacy Policy</a> | 
                        <a href="#" style="color: #999;text-decoration: none;margin: 0 10px;">Terms of Service</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>