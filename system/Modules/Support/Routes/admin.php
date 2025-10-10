<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\App\Http\Controllers\Admin\GeneralSupport\GeneralSupportIndexController;
use Modules\Support\App\Http\Controllers\Admin\GeneralSupport\GeneralSupportShowController;
use Modules\Support\App\Http\Controllers\Admin\OrderSupport\OrderSupportIndexController;
use Modules\Support\App\Http\Controllers\Admin\OrderSupport\OrderSupportShowController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {

    Route::prefix('order-supports')->group(function () {
        Route::get('/', OrderSupportIndexController::class)
            ->middleware('can:view_order_support');

        Route::get('/show/{id}', OrderSupportShowController::class)
            ->middleware('can:view_order_support');
    });

    Route::prefix('general-supports')->group(function () {
        Route::get('/', GeneralSupportIndexController::class)
            ->middleware('can:view_general_support');

        Route::get('/show/{id}', GeneralSupportShowController::class)
            ->middleware('can:view_general_support');
    });
});
