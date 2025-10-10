<?php

use Illuminate\Support\Facades\Route;
use Modules\DeliveryCharge\App\Http\Controllers\Admin\DeliveryChargeCreateController;
use Modules\DeliveryCharge\App\Http\Controllers\Admin\DeliveryChargeDestroyController;
use Modules\DeliveryCharge\App\Http\Controllers\Admin\DeliveryChargeIndexController;
use Modules\DeliveryCharge\App\Http\Controllers\Admin\DeliveryChargeShowController;
use Modules\DeliveryCharge\App\Http\Controllers\Admin\DeliveryChargeUpdateController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_delivery_charges']], function () {

    Route::prefix('delivery-charges')->group(function () {
        Route::get('/', DeliveryChargeIndexController::class);

        Route::post('/create', DeliveryChargeCreateController::class);

        Route::get('/show/{id}', DeliveryChargeShowController::class);

        Route::post('/update', DeliveryChargeUpdateController::class);

        Route::post('/destroy', DeliveryChargeDestroyController::class);
    });
});
