<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body
    style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;background-color: #f4f4f4;margin: 0;padding: 0;line-height: 1.6;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <div
                    style="background-color: white;border-radius: 12px;box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);width: 100%;border: #e6f3ff 2px solid;max-width: 500px;margin: 30px auto;padding: 30px;text-align: center;">
                    <div style="margin-bottom: 20px;width: 100%;">
                        <img src="<?php echo e(asset('https://www.liblogo.com/img-logo/la250lec9-laravel-logo-laravel-logo-air.png')); ?>"
                            alt="FusionHub Logo" style="width: 100%;height: auto;object-fit: cover;">
                    </div>
                    <h1 style="color: #333;font-size: 24px;margin-bottom: 15px;">You're Almost There!</h1>
                    <div style="background-color: #f9f9f9;padding: 20px;border-radius: 10px;margin-bottom: 20px;">
                        <div style="color: #666;margin-bottom: 25px;line-height: 1.6;">
                            <?php
                                $fullText = $sendRegisterEmailDTO->description;
                                preg_match('/^(.*?)\s*(\d+)\s*\.\s*(.*?)$/i', $fullText, $matches);
                                $message = $matches[1] ?? '';
                                $code = $matches[2] ?? '';
                                $thankYou = $matches[3] ?? '';
                            ?>
                            <?php echo e($message); ?>

                        </div>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <span
                                        style="display: inline-block;background: #fff;border: 2px solid #0066cc;padding: 8px 12px;border-radius: 8px;font-weight: bold;min-width: 30px;"><?php echo e($code); ?></span>
                                </td>
                            </tr>
                        </table>
                        <div style="margin-top: 15px;color: #666;margin-bottom: 25px;line-height: 1.6;"><?php echo e($thankYou); ?>

                        </div>
                    </div>
                    <div style="color: #ff0000;font-size: 14px;margin-bottom: 20px;">
                        Do not share this code with anyone under any circumstances!
                    </div>
                    <div style="color: #666;font-size: 14px;margin-top: 20px;">
                        Need help? Contact us at <a href="mailto:support@example.com">support@example.com</a>
                    </div>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center" style="margin-top: 20px;">
                                <a href="#" style="display: inline-block;margin: 0 10px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram"
                                        style="width: 24px;height: 24px;"></a>
                                <a href="#" style="display: inline-block;margin: 0 10px;"><img
                                        src="https://cdn-icons-png.flaticon.com/512/733/733635.png" alt="Twitter"
                                        style="width: 24px;height: 24px;"></a>
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
                        © <?php echo e(now()->year); ?> FUSIONHUB. All rights reserved.
                        <br>
                        <a href="#" style="color: #999;text-decoration: none;margin: 0 10px;">Privacy Policy</a> | <a
                            href="#" style="color: #999;text-decoration: none;margin: 0 10px;">Terms of Service</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html><?php /**PATH /opt/www/Modules/User/resources/views/user_registration.blade.php ENDPATH**/ ?>