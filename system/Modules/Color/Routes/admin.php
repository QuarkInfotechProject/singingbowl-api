<?php
use Illuminate\Support\Facades\Route;
use Modules\Color\App\Http\Controllers\ColorChangeStatusController;
use Modules\Color\App\Http\Controllers\ColorCreateController;
use Modules\Color\App\Http\Controllers\ColorUpdateController;
use Modules\Color\App\Http\Controllers\ColorDeleteController;
use Modules\Color\App\Http\Controllers\ColorIndexController;
use Modules\Color\App\Http\Controllers\ColorShowController;

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_color']], function () {
    Route::prefix('colors')->group(function () {
        Route::post('/', ColorIndexController::class);
        Route::post('/create', ColorCreateController::class);
        Route::post('/change-status', ColorChangeStatusController::class);
        Route::post('/update', ColorUpdateController::class);
        Route::post('/destroy', ColorDeleteController::class);
        Route::get('/show/{id}', ColorShowController::class);
    });
});
