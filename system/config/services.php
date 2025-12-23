<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => 'https://uat.zolpastore.com/auth/google/callback',
    ],

    // 'esewa' => [
    //     'secret' => '8gBm/:&EnhH.1/q',
    //     'product_code' => 'EPAYTEST',
    //     'uat_url' => 'https://uat.esewa.com.np'
    // ],

    // 'khalti' => [
    //     'secret' => '459b64c4733448d0807e6fc568bcd264'
    // ],

    // 'card' => [
    //     'access_key' => 'cf38d6c34faf3b9ab19ae01f8bebddbd',
    //     'profile_id' => '851F2085-DE32-4D57-957E-52155293AA45',
    //     'secret_key' => '936ae1bcf5de45a09ce67998c35786234a1286533e204a17b858c1f875ff7819a9b5313a7e2f4af9b1b758cf4a77814919117a772adb4d409748a766141f3dab37002f9379be4957b0c1581c083cec1b05a4f8ae23d7483eaba62eba6c59c6908690190b4b384ca797bd3317437f298b7f7a8a9eaa834c6b925a4b38238516f4'
    // ],

    // 'IMEPay' => [
    //     'merchant_number' => '9869772973',
    //     'merchant_name' => 'Ultima',
    //     'merchant_module' => 'ULTIMA',
    //     'merchant_code' => 'ULTIMA',
    //     'username' => 'ultima',
    //     'password' => 'ime@1234',
    // ],

    // 'swift' => [
    //     'username' => 'Ultima',
    //     'password' => 'ULTIMA@123',
    //     'org_code' => 'Ultima'
    // ],

    // 'pathao' => [
    //     'client_id' => 'QK9b69QaEv',
    // //     'client_secret' => 'k12nLGgq0zM3a65Sp65el4SZO6dhhMIxR0rDCavz',
    // //     'username' => 'test.parcel@pathao.com',
    // //     'password' => 'lovePathao',
    // //     'store_id' => '130903'
    // // ]

    'getpay' => [
        'name'           => 'GetPay',
        'business_name'  => env('GETPAY_BUSINESS_NAME', 'Singing Bowl'),
        'website_domain' => env('GETPAY_WEBSITE_DOMAIN'),
        'base_api_url'   => env('GETPAY_BASE_URL', 'https://ecom-getpay.nchl.com.np/ecom-web-checkout/v1/secure-merchant/transactions'),
        'logo_url'       => env('GETPAY_LOGO_URL'),
        'pap_info'       => env('GETPAY_PAP_INFO'),
        'opr_key'        => env('GETPAY_OPR_KEY'),
        'ins_key'        => env('GETPAY_INS_KEY', ''),
    ],
];
