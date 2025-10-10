<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\App\Http\Controllers\User\ProductSearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('products')->group(function () {
    Route::get('search', [ProductSearchController::class, 'search'])->name('products.search');
    Route::get('suggestions', [ProductSearchController::class, 'suggestions'])->name('products.suggestions');
    Route::get('popular-searches', [ProductSearchController::class, 'popularSearches'])->name('products.popularSearches');
});
