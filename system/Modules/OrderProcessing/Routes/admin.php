<?php

use Illuminate\Support\Facades\Route;
use Modules\OrderProcessing\App\Http\Controllers\CreateOrderArtifactsController;
use Modules\OrderProcessing\App\Http\Controllers\OrderArtifactsIndexController;
use Modules\OrderProcessing\App\Http\Controllers\OrderIndexForProcessingController;
use Modules\OrderProcessing\App\Http\Controllers\OrderProcessingTrackController;
use Modules\OrderProcessing\App\Http\Controllers\ProcessRefundController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_order_processing']], function () {
    Route::post('/orders-for-processing', OrderIndexForProcessingController::class);

    Route::post('/orders/create-artifacts', CreateOrderArtifactsController::class);
//        ->middleware('can:create_order_artifacts');

    Route::get('/order-artifacts', OrderArtifactsIndexController::class);
    Route::post('/orders/{orderId}/refund', ProcessRefundController::class);
});

Route::get('/track/{orderId}/{mobile}', OrderProcessingTrackController::class);
