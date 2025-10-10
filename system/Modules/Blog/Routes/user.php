<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\App\Http\Controllers\User\PostIndexController;
use Modules\Blog\App\Http\Controllers\User\PostShowController;

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


Route::post('posts', PostIndexController::class);
Route::get('posts/show', PostShowController::class);


