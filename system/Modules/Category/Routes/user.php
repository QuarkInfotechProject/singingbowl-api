<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\App\Http\Controllers\User\CategoryIndexController;
use Modules\Category\App\Http\Controllers\User\CategoryProductListController;

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

Route::middleware(['guest'])->group(function () {
    Route::get('/categories', CategoryIndexController::class);
    Route::get('/categories/product', CategoryProductListController::class);
});
