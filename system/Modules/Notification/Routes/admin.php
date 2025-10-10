<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\App\Http\Controllers\NotificationDestroyController;
use Modules\Notification\App\Http\Controllers\NotificationIndexController;
use Modules\Notification\App\Http\Controllers\NotificationMarkAsReadController;
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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_orders']], function () {

    Route::prefix('notifications')->group(function () {
        Route::get('/', NotificationIndexController::class);
        Route::post('/mark-as-read/{id?}', NotificationMarkAsReadController::class);
        Route::post('/destroy', NotificationDestroyController::class);
    });
});
