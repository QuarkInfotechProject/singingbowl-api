<?php
use Illuminate\Support\Facades\Route;
use Modules\Others\App\Http\Controllers\CategoriesTrending\User\CategoriesTrendingIndexController;
use Modules\Others\App\Http\Controllers\CategoriesTrending\User\CategoriesTrendingShowController;
use Modules\Others\App\Http\Controllers\NewArrival\User\NewArrivalIndexController;
use Modules\Others\App\Http\Controllers\NewArrival\User\NewArrivalShowController;
use Modules\Others\App\Http\Controllers\Giveaway\User\GiveawayCreateController;
use Modules\Others\App\Http\Controllers\LimitedTimeDeals\User\LimitedTimeDealIndexController;



// Trending Categories Routes
Route::get('trending-categories', CategoriesTrendingIndexController::class);
Route::get('trending-categories/{id}', CategoriesTrendingShowController::class);

// New Arrival Routes
Route::get('new-arrival', NewArrivalIndexController::class);
Route::get('new-arrival/show', NewArrivalShowController::class);

// Giveaway Routes
Route::post('giveaway/create', GiveawayCreateController::class);

//Limited Time Deals Routes
Route::get('limitedtimedeal', LimitedTimeDealIndexController::class);