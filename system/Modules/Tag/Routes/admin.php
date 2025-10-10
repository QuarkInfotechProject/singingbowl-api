<?php

use Illuminate\Support\Facades\Route;
use Modules\Tag\App\Http\Controllers\TagCreateController;
use Modules\Tag\App\Http\Controllers\TagDestroyController;
use Modules\Tag\App\Http\Controllers\TagIndexController;
use Modules\Tag\App\Http\Controllers\TagShowController;
use Modules\Tag\App\Http\Controllers\TagUpdateController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_tags']], function () {

    Route::prefix('tags')->group(function () {
        Route::post('/', TagIndexController::class);

        Route::post('create', TagCreateController::class);
//        ->middleware('can:create_tag');

        Route::get('show/{id}', TagShowController::class);

        Route::post('update', TagUpdateController::class);
//        ->middleware('can:update_tag');

        Route::post('destroy', TagDestroyController::class);
//        ->middleware('can:delete_tag');
    });
});


