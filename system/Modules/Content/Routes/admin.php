<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\Admin\Affiliate\AffiliateChangeStatusController;
use Modules\Content\App\Http\Controllers\Admin\Affiliate\AffiliateCreateController;
use Modules\Content\App\Http\Controllers\Admin\Affiliate\AffiliateDestroyController;
use Modules\Content\App\Http\Controllers\Admin\Affiliate\AffiliateIndexController;
use Modules\Content\App\Http\Controllers\Admin\Affiliate\AffiliateShowController;
use Modules\Content\App\Http\Controllers\Admin\Affiliate\AffiliateUpdateController;
use Modules\Content\App\Http\Controllers\Admin\BestSeller\BestSellerChangeStatusController;
use Modules\Content\App\Http\Controllers\Admin\BestSeller\BestSellerCreateController;
use Modules\Content\App\Http\Controllers\Admin\BestSeller\BestSellerDestroyController;
use Modules\Content\App\Http\Controllers\Admin\BestSeller\BestSellerIndexController;
use Modules\Content\App\Http\Controllers\Admin\BestSeller\BestSellerShowController;
use Modules\Content\App\Http\Controllers\Admin\BestSeller\BestSellerUpdateController;
use Modules\Content\App\Http\Controllers\Admin\FlashOffer\FlashOfferChangeStatusController;
use Modules\Content\App\Http\Controllers\Admin\FlashOffer\FlashOfferCreateController;
use Modules\Content\App\Http\Controllers\Admin\FlashOffer\FlashOfferDestroyController;
use Modules\Content\App\Http\Controllers\Admin\FlashOffer\FlashOfferIndexController;
use Modules\Content\App\Http\Controllers\Admin\FlashOffer\FlashOfferShowController;
use Modules\Content\App\Http\Controllers\Admin\FlashOffer\FlashOfferUpdateController;
use Modules\Content\App\Http\Controllers\Admin\Content\ContentChangeStatusController;
use Modules\Content\App\Http\Controllers\Admin\Content\ContentCreateController;
use Modules\Content\App\Http\Controllers\Admin\Content\ContentDestroyController;
use Modules\Content\App\Http\Controllers\Admin\Content\ContentIndexController;
use Modules\Content\App\Http\Controllers\Admin\Content\ContentShowController;
use Modules\Content\App\Http\Controllers\Admin\Content\ContentUpdateController;
use Modules\Content\App\Http\Controllers\Admin\Header\HeaderChangeStatusController;
use Modules\Content\App\Http\Controllers\Admin\Header\HeaderCreateController;
use Modules\Content\App\Http\Controllers\Admin\Header\HeaderDestroyController;
use Modules\Content\App\Http\Controllers\Admin\Header\HeaderIndexController;
use Modules\Content\App\Http\Controllers\Admin\Header\HeaderShowController;
use Modules\Content\App\Http\Controllers\Admin\Header\HeaderUpdateController;
use Modules\Content\App\Http\Controllers\Admin\InThePress\InThePressChangeStatusController;
use Modules\Content\App\Http\Controllers\Admin\InThePress\InThePressCreateController;
use Modules\Content\App\Http\Controllers\Admin\InThePress\InThePressDestroyController;
use Modules\Content\App\Http\Controllers\Admin\InThePress\InThePressIndexController;
use Modules\Content\App\Http\Controllers\Admin\InThePress\InThePressShowController;
use Modules\Content\App\Http\Controllers\Admin\InThePress\InThePressUpdateController;
use Modules\Content\App\Http\Controllers\Admin\NewLaunch\NewLaunchContentChangeStatusController;
use Modules\Content\App\Http\Controllers\Admin\NewLaunch\NewLaunchContentCreateController;
use Modules\Content\App\Http\Controllers\Admin\NewLaunch\NewLaunchContentDestroyController;
use Modules\Content\App\Http\Controllers\Admin\NewLaunch\NewLaunchContentIndexController;
use Modules\Content\App\Http\Controllers\Admin\NewLaunch\NewLaunchContentShowController;
use Modules\Content\App\Http\Controllers\Admin\NewLaunch\NewLaunchContentUpdateController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_contents']], function () {

    Route::prefix('contents')->group(function () {
        Route::post('/', ContentIndexController::class);
//            ->middleware('can:view_contents');

        Route::post('/create', ContentCreateController::class);
//            ->middleware('can:create_content');

        Route::get('/show/{id}', ContentShowController::class);

        Route::post('/update', ContentUpdateController::class);
//            ->middleware('can:update_content');

        Route::post('/destroy', ContentDestroyController::class);
//            ->middleware('can:delete_content');

        Route::post('/change-status', ContentChangeStatusController::class);
    });

    Route::prefix('new-launches')->group(function () {
        Route::get('/', NewLaunchContentIndexController::class);
//            ->middleware('can:view_contents');

        Route::post('/create', NewLaunchContentCreateController::class);
//            ->middleware('can:create_content');

        Route::get('/show/{id}', NewLaunchContentShowController::class);
//            ->middleware('can:view_content');

        Route::post('/update', NewLaunchContentUpdateController::class);
//            ->middleware('can:update_content');

        Route::post('/destroy', NewLaunchContentDestroyController::class);
//            ->middleware('can:delete_content');

        Route::post('/change-status', NewLaunchContentChangeStatusController::class);
    });

    Route::prefix('affiliates')->group(function () {
        Route::post('/', AffiliateIndexController::class);
//            ->middleware('can:view_contents');

        Route::post('/create', AffiliateCreateController::class);
//            ->middleware('can:create_content');

        Route::get('/show/{id}', AffiliateShowController::class);
//            ->middleware('can:view_content');

        Route::post('/update', AffiliateUpdateController::class);
//            ->middleware('can:update_content');

        Route::post('/destroy', AffiliateDestroyController::class);
//            ->middleware('can:delete_content');

        Route::post('/change-status', AffiliateChangeStatusController::class);
    });

    Route::prefix('in-the-press')->group(function () {
        Route::get('/', InThePressIndexController::class);
//            ->middleware('can:view_contents');

        Route::post('/create', InThePressCreateController::class);
//            ->middleware('can:create_content');

        Route::get('/show/{id}', InThePressShowController::class);
//            ->middleware('can:view_content');

        Route::post('/update', InThePressUpdateController::class);
//            ->middleware('can:update_content');

        Route::post('/destroy', InThePressDestroyController::class);
//            ->middleware('can:delete_content');

        Route::post('/change-status', InThePressChangeStatusController::class);
    });

    Route::prefix('best-sellers')->group(function () {
        Route::get('/', BestSellerIndexController::class);
//            ->middleware('can:view_contents');

        Route::post('/create', BestSellerCreateController::class);
//            ->middleware('can:create_content');

        Route::get('/show/{id}', BestSellerShowController::class);
//            ->middleware('can:view_content');

        Route::post('/update', BestSellerUpdateController::class);
//            ->middleware('can:update_content');

        Route::post('/destroy', BestSellerDestroyController::class);
//            ->middleware('can:delete_content');

        Route::post('/change-status', BestSellerChangeStatusController::class);
    });

    Route::prefix('flash-offers')->group(function () {
        Route::get('/', FlashOfferIndexController::class);
//            ->middleware('can:view_contents');

        Route::post('/create', FlashOfferCreateController::class);
//            ->middleware('can:create_content');

        Route::get('/show/{id}', FlashOfferShowController::class);
//            ->middleware('can:view_content');

        Route::post('/update', FlashOfferUpdateController::class);
//            ->middleware('can:update_content');

        Route::post('/destroy', FlashOfferDestroyController::class);
//            ->middleware('can:delete_content');

        Route::post('/change-status', FlashOfferChangeStatusController::class);
    });

      Route::prefix('headers')->group(function () {
        Route::get('/', HeaderIndexController::class);
//            ->middleware('can:view_contents');

        Route::post('/create', HeaderCreateController::class);
//            ->middleware('can:create_content');

        Route::get('/show/{id}', HeaderShowController::class);
//            ->middleware('can:view_content');

        Route::post('/update', HeaderUpdateController::class);
//            ->middleware('can:update_content');

        Route::post('/destroy', HeaderDestroyController::class);
//            ->middleware('can:delete_content');

        Route::post('/change-status', HeaderChangeStatusController::class);
    });
});
