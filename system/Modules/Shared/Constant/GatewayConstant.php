<?php

namespace Modules\Shared\Constant;

class GatewayConstant
{
    const COD = 'cod';
    const ESEWA = 'esewa';
    const KHALTI = 'khalti';

    const  CARD = 'card';
    const  IME_PAY = 'IMEPay';
    const  GETPAY = 'getPay';

    public static $gatewayMapping = [
        self::COD => 'Cash on Delivery',
        self::ESEWA => 'eSewa',
        self::CARD => 'Visa/Mastercard',
        self::IME_PAY => 'IME Pay',
        self::KHALTI => 'Khalti',
        self::GETPAY => 'GetPay'
    ];
}
