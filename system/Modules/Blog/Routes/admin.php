<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\App\Http\Controllers\Admin\PostChangeStatusController;
use Modules\Blog\App\Http\Controllers\Admin\PostCreateController;
use Modules\Blog\App\Http\Controllers\Admin\PostDestroyController;
use Modules\Blog\App\Http\Controllers\Admin\PostIndexController;
use Modules\Blog\App\Http\Controllers\Admin\PostShowController;
use Modules\Blog\App\Http\Controllers\Admin\PostUpdateController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_posts']], function () {

    Route::prefix('posts')->group(function () {
        Route::post('/', PostIndexController::class);

        Route::post('/create', PostCreateController::class);

        Route::get('/show/{id}', PostShowController::class);

        Route::post('/update', PostUpdateController::class);

        Route::post('/destroy', PostDestroyController::class);

        Route::post('/change-status', PostChangeStatusController::class);
    });
});
