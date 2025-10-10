<?php

use Illuminate\Support\Facades\Route;
use Modules\Warranty\App\Http\Controllers\WarrantyClaim\WarrantyClaimCreateController;
use Modules\Warranty\App\Http\Controllers\WarrantyRegistration\WarrantyRegistrationCreateController;

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
    Route::post('/warranty-registrations/create', WarrantyRegistrationCreateController::class);
    Route::post('/warranty-claims/create', WarrantyClaimCreateController::class);
});
