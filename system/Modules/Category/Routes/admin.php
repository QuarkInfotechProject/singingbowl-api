<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\App\Http\Controllers\Admin\CategoryCreateController;
use Modules\Category\App\Http\Controllers\Admin\CategoryDestroyController;
use Modules\Category\App\Http\Controllers\Admin\CategoryIndexController;
use Modules\Category\App\Http\Controllers\Admin\CategoryShowController;
use Modules\Category\App\Http\Controllers\Admin\CategoryReOrderController;
use Modules\Category\App\Http\Controllers\Admin\CategoryUpdateController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_category']], function () {

    Route::prefix('categories')->group(function () {
        Route::get('/', CategoryIndexController::class);
//        ->middleware('can:view_category');

        Route::post('/create', CategoryCreateController::class);
//        ->middleware('can:create_category');

        Route::post('/reorder', CategoryReOrderController::class);

        Route::get('/show/{id}', CategoryShowController::class);
//        ->middleware('can:view_category');

        Route::post('/update', CategoryUpdateController::class);
//        ->middleware('can:update_category');

        Route::post('/destroy', CategoryDestroyController::class);
//        ->middleware('can:delete_category');
    });
});
