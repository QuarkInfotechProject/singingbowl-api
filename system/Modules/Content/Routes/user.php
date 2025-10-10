<?php

use Illuminate\Support\Facades\Route;
use Modules\Content\App\Http\Controllers\User\Affiliate\AffiliateIndexController;
use Modules\Content\App\Http\Controllers\User\BestSeller\BestSellerIndexController;
use Modules\Content\App\Http\Controllers\User\FlashOffer\FlashOfferIndexController;
use Modules\Content\App\Http\Controllers\User\Content\ContentIndexController;
use Modules\Content\App\Http\Controllers\User\Header\HeaderIndexController;
use Modules\Content\App\Http\Controllers\User\InThePress\InThePressIndexController;
use Modules\Content\App\Http\Controllers\User\NewLaunch\NewLaunchIndexController;

Route::get('contents', ContentIndexController::class);
Route::get('affiliates', AffiliateIndexController::class);
Route::get('in-the-press', InThePressIndexController::class);
Route::get('new-launches', NewLaunchIndexController::class);
Route::get('best-sellers', BestSellerIndexController::class);
Route::get('flash-offers', FlashOfferIndexController::class);
Route::get('headers', HeaderIndexController::class);
