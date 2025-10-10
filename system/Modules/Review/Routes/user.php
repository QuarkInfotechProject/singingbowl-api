<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\App\Http\Controllers\User\Question\QuestionCreateController;
use Modules\Review\App\Http\Controllers\User\Review\ReviewCreateController;

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

Route::group(['middleware' => 'auth:user'], function () {

    Route::prefix('reviews')->group(function () {
        Route::post('/create', ReviewCreateController::class);
    });
});

Route::post('questions/create', QuestionCreateController::class);
