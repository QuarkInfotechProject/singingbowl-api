<?php

use Illuminate\Support\Facades\Route;
use Modules\CorporateOrder\App\Http\Controllers\CorporateOrderCreateController;

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
    Route::post('/corporate-orders/create', CorporateOrderCreateController::class);
});
