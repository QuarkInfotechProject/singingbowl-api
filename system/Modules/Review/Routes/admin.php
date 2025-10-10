<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\App\Http\Controllers\Admin\ReviewChangeStatusController;
use Modules\Review\App\Http\Controllers\Admin\ReviewDestroyController;
use Modules\Review\App\Http\Controllers\Admin\ReviewIndexController;
use Modules\Review\App\Http\Controllers\Admin\ReviewReplyController;
use Modules\Review\App\Http\Controllers\Admin\ReviewShowController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_reviews']], function () {

    Route::prefix('reviews')->group(function () {
        Route::post('/', ReviewIndexController::class);
        Route::get('/show/{uuid}', ReviewShowController::class);
        Route::post('/destroy', ReviewDestroyController::class);
        Route::post('/change-status', ReviewChangeStatusController::class);
        Route::post('/reply/create', ReviewReplyController::class);
    });
});
