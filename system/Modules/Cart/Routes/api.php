<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\App\Http\Controllers\CartIndexController;
use Modules\Cart\App\Http\Controllers\CartAddItemController;
use Modules\Cart\App\Http\Controllers\CartUpdateItemController;
use Modules\Cart\App\Http\Controllers\CartRemoveItemController;
use Modules\Cart\App\Http\Controllers\CartClearController;
use Modules\Cart\App\Http\Middleware\CartAuthMiddleware;
use Modules\Cart\App\Http\Controllers\GetGuestCartTokenController;

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

// Route for generating a guest token - no auth required
Route::get('/token', GetGuestCartTokenController::class);

// Unified cart routes that work for both authenticated users and guests with tokens
Route::group(['middleware' => [CartAuthMiddleware::class], 'prefix' => 'cart'], function () {
    Route::get('/', CartIndexController::class);                     // View cart contents
    Route::post('/add', CartAddItemController::class);               // Add item to cart
    Route::post('/update', CartUpdateItemController::class);         // Update cart item (POST instead of PUT for form compatibility)
    Route::post('/remove', CartRemoveItemController::class);         // Remove cart item (POST instead of DELETE for form compatibility)
    Route::post('/clear', CartClearController::class);               // Clear entire cart (POST instead of DELETE for form compatibility)
});