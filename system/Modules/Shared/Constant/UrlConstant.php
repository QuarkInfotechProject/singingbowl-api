<?php

namespace Modules\Shared\Constant;

class UrlConstant
{
    public const PAYMENT_SUCCESS_URL = 'https://www.singingbowlvillagenepal.com/api/user/orders/success';
    public const PAYMENT_FAILURE_URL = 'https://www.singingbowlvillagenepal.com/api/user/orders/payment-fail';
    public const ESEWA_ENQUIRY_URL = 'https://uat.esewa.com.np/api/epay/transaction/status';
    public const CARD_SUCCESS_REDIRECT_URL = 'https://www.singingbowlvillagenepal.com/orders/success/';
    public const CARD_FAILURE_REDIRECT_URL = 'https://www.singingbowlvillagenepal.com/orders/fail/';
    public const IMEPAY_BASE_URL = 'https://stg.imepay.com.np:7979/api/Web/';
    public const KHALTI_BASE_URL = 'https://a.khalti.com/api/v2/epayment/initiate/';
    public const KHALTI_LOOKUP_URL = 'https://a.khalti.com/api/v2/epayment/lookup/';
    public const SWIFT_SMS_BASE_URL = 'https://smartsms.swifttech.com.np:8083/api/Sms/ExecuteSendSmsV5';
    public const PATHAO_BASE_URL = 'https://courier-api-sandbox.pathao.com';
    public const PATHAO_TRACK_URL = 'https://parcel.pathao.com/tracking';

    // GetPay Payment Gateway
    public const GETPAY_BASE_URL = 'https://ecom-getpay.nchl.com.np/ecom-web-checkout';
    public const GETPAY_MERCHANT_STATUS_URL = '/v1/secure-merchant/transactions/merchant-status';
}
