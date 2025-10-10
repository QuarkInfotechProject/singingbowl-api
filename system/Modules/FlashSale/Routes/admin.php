<?php
use Illuminate\Support\Facades\Route;
use Modules\FlashSale\App\Http\Controllers\Admin\FlashSaleIndexController; 
use Modules\FlashSale\App\Http\Controllers\Admin\FlashSaleCreateController;
use Modules\FlashSale\App\Http\Controllers\Admin\FlashSaleUpdateController;  
use Modules\FlashSale\App\Http\Controllers\Admin\FlashSaleShowController;
use Modules\FlashSale\App\Http\Controllers\Admin\FlashSaleDestroyController;

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_flash_sale']], function () {

    Route::prefix('flash-sale')->group(function () {
        Route::post('/create', FlashSaleCreateController::class);

        Route::post('/index', FlashSaleIndexController::class);
        Route::post('/update', FlashSaleUpdateController::class);
        Route::post('/destroy', FlashSaleDestroyController::class);

        Route::GET('/show/{id}', FlashSaleShowController::class);
    });
});
