<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\App\Http\Controllers\User\ProductByCategoryController;
use Modules\Product\App\Http\Controllers\User\ProductGetBulkOffersController;
use Modules\Product\App\Http\Controllers\User\ProductGetDescriptionVideoController;
use Modules\Product\App\Http\Controllers\User\ProductGetMainSpecsController;
use Modules\Product\App\Http\Controllers\User\ProductGetSimilarProductController;
use Modules\Product\App\Http\Controllers\User\ProductGetSpecificationController;
use Modules\Product\App\Http\Controllers\User\ProductGetVariantDescriptionController;
use Modules\Product\App\Http\Controllers\User\ProductIndexController;
use Modules\Product\App\Http\Controllers\User\ProductIndexWithVideoController;
use Modules\Product\App\Http\Controllers\User\ProductPurchaseIndexController;
use Modules\Product\App\Http\Controllers\User\ProductShopController;
use Modules\Product\App\Http\Controllers\User\ProductShowController;
use Modules\Product\App\Http\Controllers\User\ProductShowNamesController;
use Modules\Product\App\Http\Controllers\User\ProductGetRelatedProductController;
use Modules\Product\App\Http\Controllers\User\ProductFilterDataController;
use Modules\Product\App\Http\Controllers\User\ProductListByCategoryController;

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
    Route::get('/products/names', ProductShowNamesController::class);
    Route::get('/products/list-by-category', ProductListByCategoryController::class);
    Route::get('/products/{categoryName}', ProductByCategoryController::class);
    Route::post('/products', ProductIndexController::class);
    Route::get('/shop', ProductShopController::class);
    Route::get('/filters/{url}', ProductFilterDataController::class);
    Route::get('/products-with-video', ProductIndexWithVideoController::class);
    Route::get('/products/show/{url}', ProductShowController::class);
    Route::get('/products/show/description-video/{url}', ProductGetDescriptionVideoController::class);
    Route::get('/products/show/similar/{url}', ProductGetSimilarProductController::class);
    Route::get('/products/show/specification/{url}', ProductGetSpecificationController::class);
    Route::get('/products/show/main-specs/{url}', ProductGetMainSpecsController::class);
    Route::get('/products/show/variant/{id}', ProductGetVariantDescriptionController::class);
    Route::get('/products/get/bulk-offers', ProductGetBulkOffersController::class);
    Route::get('/products/related/{slug}', ProductGetRelatedProductController::class);

});

Route::group(['middleware' => 'auth:user'], function () {
        Route::get('/purchases', ProductPurchaseIndexController::class);
});
