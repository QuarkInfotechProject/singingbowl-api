<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\App\Http\Controllers\MenuChangeStatusController;
use Modules\Menu\App\Http\Controllers\MenuController;
use Modules\Menu\App\Http\Controllers\MenuIndexController;
use Modules\Menu\App\Http\Controllers\MenuReOrderController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_menus']], function () {
    Route::prefix('menu')->group(function () {
        Route::get('/', MenuIndexController::class);
        Route::get('/active', MenuController::class);

        Route::post('/reorder', MenuReOrderController::class);
//            ->middleware('can:reorder_menu');

        Route::post('/change-status', MenuChangeStatusController::class);
//            ->middleware('can:change_menu_status');
    });
});
