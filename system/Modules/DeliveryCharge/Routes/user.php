<?php

use Illuminate\Support\Facades\Route;
use Modules\DeliveryCharge\App\Http\Controllers\User\DeliveryChargeIndexController;

Route::get('delivery-charges', DeliveryChargeIndexController::class);
