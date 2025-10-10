<?php

use Illuminate\Support\Facades\Route;
use Modules\Attribute\App\Http\Controllers\Attribute\AttributeCreateController;
use Modules\Attribute\App\Http\Controllers\Attribute\AttributeDestroyController;
use Modules\Attribute\App\Http\Controllers\Attribute\AttributeIndexController;
use Modules\Attribute\App\Http\Controllers\Attribute\AttributeShowController;
use Modules\Attribute\App\Http\Controllers\Attribute\AttributeUpdateController;
use Modules\Attribute\App\Http\Controllers\AttributeSet\AttributeSetCreateController;
use Modules\Attribute\App\Http\Controllers\AttributeSet\AttributeSetDestroyController;
use Modules\Attribute\App\Http\Controllers\AttributeSet\AttributeSetIndexController;
use Modules\Attribute\App\Http\Controllers\AttributeSet\AttributeSetShowController;
use Modules\Attribute\App\Http\Controllers\AttributeSet\AttributeSetUpdateController;
use Modules\Attribute\App\Http\Controllers\CategoryAttribute\CategoryAttributeIndexController;
use Modules\Attribute\App\Http\Controllers\CategoryAttribute\CategoryAttributeStatusController;
use Modules\Attribute\App\Http\Controllers\CategoryAttribute\CategoryAttributeSortOrderController;
use Modules\Attribute\App\Http\Controllers\CategoryAttribute\CategoryAttributesByCategoriesController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_attribute_sets']], function () {

    //Attribute sets routes
    Route::prefix('attribute-sets')->group(function () {
        Route::get('/', AttributeSetIndexController::class);

        Route::post('create', AttributeSetCreateController::class);
//            ->middleware('can:create_attribute_set');

        Route::get('show/{id}', AttributeSetShowController::class);

        Route::post('update', AttributeSetUpdateController::class);
//            ->middleware('can:update_attribute_set');

        Route::post('destroy', AttributeSetDestroyController::class);
//            ->middleware('can:delete_attribute_set');
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_attributes']], function () {

    //Attribute routes
    Route::prefix('attributes')->group(function () {
        Route::post('/', AttributeIndexController::class);

        Route::post('create', AttributeCreateController::class);
//            ->middleware('can:create_attribute');

        Route::get('show/{id}', AttributeShowController::class);

        Route::post('update', AttributeUpdateController::class);
//            ->middleware('can:update_attribute');

        Route::post('destroy', AttributeDestroyController::class);
//            ->middleware('can:delete_attribute');
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    // Category Attribute routes
    Route::prefix('categories/attributes')->group(function () {
        Route::get('/', CategoryAttributeIndexController::class);
        Route::post('status', CategoryAttributeStatusController::class);
        Route::post('reorder', CategoryAttributeSortOrderController::class);
        // New endpoint: fetch attributes for multiple categories
        Route::post('by-categories', CategoryAttributesByCategoriesController::class);
    });
});



