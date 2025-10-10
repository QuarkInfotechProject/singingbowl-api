<?php

use Illuminate\Support\Facades\Route;
use Modules\Coupon\App\Http\Controllers\Admin\CouponChangeStatusController;
use Modules\Coupon\App\Http\Controllers\Admin\CouponCreateController;
use Modules\Coupon\App\Http\Controllers\Admin\CouponDestroyController;
use Modules\Coupon\App\Http\Controllers\Admin\CouponIndexController;
use Modules\Coupon\App\Http\Controllers\Admin\CouponShowCodeController;
use Modules\Coupon\App\Http\Controllers\Admin\CouponShowController;
use Modules\Coupon\App\Http\Controllers\Admin\CouponUpdateController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_coupons']], function () {

    Route::prefix('coupons')->group(function () {
        Route::post('/', CouponIndexController::class);
        Route::get('/show-names', CouponShowCodeController::class);

        Route::post('/create', CouponCreateController::class);
//            ->middleware('can:create_coupon');

        Route::get('/show/{id}', CouponShowController::class);

        Route::post('/update', CouponUpdateController::class);
//            ->middleware('can:update_coupon');

        Route::post('/destroy', CouponDestroyController::class);
//            ->middleware('can:delete_coupon');

        Route::post('/change-status', CouponChangeStatusController::class);
    });
});
