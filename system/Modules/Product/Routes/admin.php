<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\App\Http\Controllers\Admin\ProductAddSpecificationsController;
use Modules\Product\App\Http\Controllers\Admin\ProductQuickUpdateController;
use Modules\Product\App\Http\Controllers\Admin\ProductCreateController;
use Modules\Product\App\Http\Controllers\Admin\ProductDestroyController;
use Modules\Product\App\Http\Controllers\Admin\ProductExclusionController;
use Modules\Product\App\Http\Controllers\Admin\ProductIndexController;
use Modules\Product\App\Http\Controllers\Admin\ProductReorderController;
use Modules\Product\App\Http\Controllers\Admin\ProductShowController;
use Modules\Product\App\Http\Controllers\Admin\ProductShowVariantController;
use Modules\Product\App\Http\Controllers\Admin\ProductUpdateController;
use Modules\Product\App\Http\Controllers\Admin\ProductBestSellerController;
use Modules\Product\App\Http\Controllers\Admin\ProductUpdateSpecificationsController;
use Modules\Product\App\Http\Controllers\Admin\ProductUpdateVariantController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_products']], function () {

    Route::prefix('products')->group(function () {
        Route::post('/', ProductIndexController::class);

        Route::post('/create', ProductCreateController::class);
//            ->middleware('can:create_product');

        Route::get('/show/{id}', ProductShowController::class);

        Route::post('/update', ProductUpdateController::class);
//            ->middleware('can:update_product');

        Route::post('/destroy', ProductDestroyController::class);
//            ->middleware('can:delete_product');

        Route::get('/variants/show/{uuid}', ProductShowVariantController::class);

        Route::post('/variants/update', ProductUpdateVariantController::class);
//            ->middleware('can:update_product');

        Route::post('/quick-update', ProductQuickUpdateController::class);

        Route::get('/exclude/{uuid}', ProductExclusionController::class);
        Route::post('/reorder', ProductReorderController::class);
        Route::post('/bestseller', ProductBestSellerController::class);
    });
});
