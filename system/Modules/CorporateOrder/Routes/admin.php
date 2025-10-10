<?php

use Illuminate\Support\Facades\Route;
use Modules\CorporateOrder\App\Http\Controllers\CorporateOrderCreateController;
use Modules\CorporateOrder\App\Http\Controllers\CorporateOrderIndexController;
use Modules\CorporateOrder\App\Http\Controllers\CorporateOrderShowController;
use Modules\CorporateOrder\App\Http\Controllers\CorporateOrderStatusChangeController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_corporate_orders']], function () {

    Route::prefix('corporate-orders')->group(function () {
        Route::get('/', CorporateOrderIndexController::class);

        Route::get('/show/{id}', CorporateOrderShowController::class);
        Route::post('/update', CorporateOrderStatusChangeController::class);
    });
});
