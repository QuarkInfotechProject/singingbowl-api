<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\App\Http\Controllers\User\ApplyCouponController;
use Modules\Coupon\App\Http\Controllers\User\CouponIndexController;
use Modules\Coupon\App\Http\Controllers\User\RemoveCouponController;

Route::prefix('coupons')->group(function () {
    Route::get('/', CouponIndexController::class);
    Route::post('/apply', ApplyCouponController::class);
    Route::post('/remove', RemoveCouponController::class);
});
