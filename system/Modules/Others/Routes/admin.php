<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Others\App\Http\Controllers\ActiveOffers\ActiveOfferCreateController;
use Modules\Others\App\Http\Controllers\ActiveOffers\ActiveOfferDestroyController;
use Modules\Others\App\Http\Controllers\ActiveOffers\ActiveOfferIndexController;
use Modules\Others\App\Http\Controllers\ActiveOffers\ActiveOfferShowController;
use Modules\Others\App\Http\Controllers\ActiveOffers\ActiveOfferUpdateController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\Admin\CategoriesTrendingCreateController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\Admin\CategoriesTrendingDestroyController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\Admin\CategoriesTrendingIndexController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\Admin\CategoriesTrendingShowController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\Admin\CategoriesTrendingUpdateController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\Admin\CategoriesTrendingReOrderController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\Admin\CategoriesTrendingStatusController;
use Modules\Others\App\Http\Controllers\DarazCount\DarazCountCreateController;
use Modules\Others\App\Http\Controllers\DarazCount\DarazCountDestroyController;
use Modules\Others\App\Http\Controllers\DarazCount\DarazCountIndexController;
use Modules\Others\App\Http\Controllers\DarazCount\DarazCountShowController;
use Modules\Others\App\Http\Controllers\DarazCount\DarazCountUpdateController;
use Modules\Others\App\Http\Controllers\Features\FeatureCreateController;
use Modules\Others\App\Http\Controllers\Features\FeatureDestroyController;
use Modules\Others\App\Http\Controllers\Features\FeatureIndexController;
use Modules\Others\App\Http\Controllers\Features\FeatureShowController;
use Modules\Others\App\Http\Controllers\Features\FeatureUpdateController;
use Modules\Others\App\Http\Controllers\NewArrival\Admin\NewArrivalIndexController;
use Modules\Others\App\Http\Controllers\NewArrival\Admin\NewArrivalShowController;
use Modules\Others\App\Http\Controllers\NewArrival\Admin\NewArrivalToggleController;
use Modules\Others\App\Http\Controllers\Giveaway\Admin\GiveawayIndexController;
use Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin\LimitedTimeDealCreateController;
use Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin\LimitedTimeDealIndexController;
use Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin\LimitedTimeDealShowController;
use Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin\LimitedTimeDealStatusController;
use Modules\Others\App\Http\Controllers\LimitedTimeDeals\Admin\LimitedTimeDealReOrderController;


/**
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider within a group which
 * | is assigned the "api" middleware group. Enjoy building your API!
 * |
 */

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('features')->group(function () {
        Route::get('/', FeatureIndexController::class);
        Route::post('/create', FeatureCreateController::class);
        Route::get('/show/{id}', FeatureShowController::class);
        Route::post('/update', FeatureUpdateController::class);
        Route::post('/destroy', FeatureDestroyController::class);
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('active-offers')->group(function () {
        Route::get('/', ActiveOfferIndexController::class);
        Route::post('/create', ActiveOfferCreateController::class);
        Route::get('/show/{id}', ActiveOfferShowController::class);
        Route::post('/update', ActiveOfferUpdateController::class);
        Route::post('/destroy', ActiveOfferDestroyController::class);
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('daraz-counts')->group(function () {
        Route::get('/', DarazCountIndexController::class);
        Route::post('/create', DarazCountCreateController::class);
        Route::get('/show/{id}', DarazCountShowController::class);
        Route::post('/update', DarazCountUpdateController::class);
        Route::post('/destroy', DarazCountDestroyController::class);
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('trending-categories')->group(function () {
        Route::get('/', CategoriesTrendingIndexController::class);
        Route::post('/create', CategoriesTrendingCreateController::class);
        Route::get('/show/{id}', CategoriesTrendingShowController::class);
        Route::post('/update', CategoriesTrendingUpdateController::class);
        Route::post('/destroy/{id}', CategoriesTrendingDestroyController::class);
        route::post('/reorder', CategoriesTrendingReOrderController::class);
        route::post('/status', CategoriesTrendingStatusController::class);
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('new-arrival')->group(function () {
        Route::get('/', NewArrivalIndexController::class);
        Route::get('/show/{id}', NewArrivalShowController::class);
        Route::get('/status/{id}', NewArrivalToggleController::class);

    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::post('giveaway', GiveawayIndexController::class);
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('limitedtimedeal')->group(function () {
        Route::post('/create', LimitedTimeDealCreateController::class);
        Route::get('/', LimitedTimeDealIndexController::class);
        Route::get('/show/{id}', LimitedTimeDealShowController::class);
        Route::get('/change-status/{id}', LimitedTimeDealStatusController::class);
        Route::post('/reorder', LimitedTimeDealReOrderController::class);

    });
});
