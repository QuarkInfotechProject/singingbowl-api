<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Wishlist\App\Http\Controllers\User\AddToWishlistController;
use Modules\Wishlist\App\Http\Controllers\User\RemoveFromWishlistController;
use Modules\Wishlist\App\Http\Controllers\User\WishlistIndexController;

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
    Route::prefix('wishlist')->group(function () {
        Route::get('/', WishlistIndexController::class);
        Route::post('/add', AddToWishlistController::class);
        Route::post('/remove', RemoveFromWishlistController::class);
    });
});
