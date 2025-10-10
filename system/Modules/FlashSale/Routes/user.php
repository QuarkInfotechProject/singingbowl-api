<?php
use Modules\FlashSale\App\Http\Controllers\User\FlashSaleIndexController;
use Illuminate\Support\Facades\Route;

Route::post('flash-sale', FlashSaleIndexController::class);
