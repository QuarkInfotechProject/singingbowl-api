<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\App\Http\Controllers\Admin\OrderChangeStatusController;
use Modules\Order\App\Http\Controllers\Admin\OrderCreateCustomNoteController;
use Modules\Order\App\Http\Controllers\Admin\OrderDestroyController;
use Modules\Order\App\Http\Controllers\Admin\OrderDestroyNoteController;
use Modules\Order\App\Http\Controllers\Admin\OrderGetStatusCountController;
use Modules\Order\App\Http\Controllers\Admin\OrderIndexController;
use Modules\Order\App\Http\Controllers\Admin\OrderInitialShowController;
use Modules\Order\App\Http\Controllers\Admin\OrderShowController;
use Modules\Order\App\Http\Controllers\Admin\OrderShowLogController;
use Modules\Order\App\Http\Controllers\Admin\OrderShowRefundDataController;
use Modules\Order\App\Http\Controllers\Admin\OrderUpdateController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_orders']], function (): void {

    Route::prefix('orders')->group(function () {
        Route::post('/', OrderIndexController::class);

        Route::get('/show/{id}', OrderShowController::class);
        Route::get('/initial-show/{id}', OrderInitialShowController::class);

        Route::post('/change-status', OrderChangeStatusController::class);
//            ->middleware('can:update_order_status');

        Route::post('/destroy', OrderDestroyController::class);
//            ->middleware('can:delete_order');

        Route::get('/status-count', OrderGetStatusCountController::class);
        Route::post('/create-note', OrderCreateCustomNoteController::class);
        Route::post('/destroy-note', OrderDestroyNoteController::class);
        Route::get('/show/{orderId}/logs', OrderShowLogController::class);
        Route::get('/show/{orderId}/refund', OrderShowRefundDataController::class);
        Route::post('/update', OrderUpdateController::class);
    });
});
