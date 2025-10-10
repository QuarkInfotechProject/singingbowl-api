<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Address\App\Http\Controllers\User\AddressCreateController;
use Modules\Address\App\Http\Controllers\User\AddressIndexController;
use Modules\Address\App\Http\Controllers\User\AddressShowController;
use Modules\Address\App\Http\Controllers\User\AddressUpdateController;

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

Route::group(['middleware' => 'auth:user'], function () {
    Route::prefix('address')->group(function () {
        Route::get('/', AddressIndexController::class);
        Route::post('/add', AddressCreateController::class);
        Route::get('/show/{uuid}', AddressShowController::class);
        Route::post('/update', AddressUpdateController::class);
    });
});
