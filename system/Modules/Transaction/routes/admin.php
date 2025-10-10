<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Transaction\App\Http\Controllers\TransactionIndexController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_transactions']], function () {

    Route::prefix('transactions')->group(function () {
        Route::post('/', TransactionIndexController::class);
    });
});
