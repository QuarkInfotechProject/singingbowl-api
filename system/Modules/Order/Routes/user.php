<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\App\Http\Controllers\OrderController;
use Modules\Order\App\Http\Controllers\User\OrderCancelController;
use Modules\Order\App\Http\Controllers\User\OrderCompleteWithCardController;
use Modules\Order\App\Http\Controllers\User\OrderFailWithCardController;
use Modules\Order\App\Http\Controllers\User\OrderPaymentFailController;
use Modules\Order\App\Http\Controllers\User\OrderCompleteController;
use Modules\Order\App\Http\Controllers\User\OrderCreateController;
use Modules\Order\App\Http\Controllers\User\OrderIndexController;
use Modules\Order\App\Http\Controllers\User\OrderShowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth:user', 'cart.auth']], function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', OrderIndexController::class);
        Route::post('/create', OrderCreateController::class);
        Route::get('/show/{id}', OrderShowController::class);
        Route::post('/cancel', OrderCancelController::class);
        Route::post('/payment-fail', OrderPaymentFailController::class);
        Route::post('/success', OrderCompleteController::class);
    });
});

Route::post('orders/card-payment/fail', OrderFailWithCardController::class);
Route::post('orders/card-payment/success', OrderCompleteWithCardController::class);




