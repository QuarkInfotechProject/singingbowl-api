<?php

use Illuminate\Support\Facades\Route;
use Modules\Warranty\App\Http\Controllers\WarrantyClaim\WarrantyClaimIndexController;
use Modules\Warranty\App\Http\Controllers\WarrantyClaim\WarrantyClaimShowController;
use Modules\Warranty\App\Http\Controllers\WarrantyRegistration\WarrantyRegistrationCreateController;
use Modules\Warranty\App\Http\Controllers\WarrantyRegistration\WarrantyRegistrationIndexController;
use Modules\Warranty\App\Http\Controllers\WarrantyRegistration\WarrantyRegistrationShowController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {

    Route::prefix('warranty-registrations')->group(function () {
        Route::post('/', WarrantyRegistrationIndexController::class)
        ->middleware('can:view_warranty_registrations');

        Route::get('/show/{id}', WarrantyRegistrationShowController::class)
        ->middleware('can:view_warranty_registrations');
    });

    Route::prefix('warranty-claims')->group(function () {
        Route::post('/', WarrantyClaimIndexController::class)
        ->middleware('can:view_warranty_claims');

        Route::get('/show/{id}', WarrantyClaimShowController::class)
            ->middleware('can:view_warranty_claims');
    });
});

