<?php

use Illuminate\Support\Facades\Route;
use Modules\DeliveryCharge\App\Http\Controllers\User\DeliveryChargeIndexController;
use Modules\DeliveryCharge\App\Http\Controllers\User\DeliveryCalculatorController;

Route::get('delivery-charges', DeliveryChargeIndexController::class);
Route::post('/delivery-charges/calculate', [DeliveryCalculatorController::class, 'getCalculatedCharge']);
