<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Support\App\Http\Controllers\User\GeneralSupport\GeneralSupportCreateController;
use Modules\Support\App\Http\Controllers\User\OrderSupport\OrderSupportCreateController;

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

Route::middleware(['guest'])->group(function () {
    Route::post('/order-support/create', OrderSupportCreateController::class);
    Route::post('/general-support/create', GeneralSupportCreateController::class);
});
