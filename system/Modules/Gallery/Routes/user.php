<?php

use Illuminate\Support\Facades\Route;
use Modules\Gallery\App\Http\Controllers\User\GalleryIndexController;
use Modules\Gallery\App\Http\Controllers\User\GalleryShowController;

Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::prefix('galleries')->group(function () {
        Route::get('/', GalleryIndexController::class);
        Route::get('{slug}', GalleryShowController::class);
    });
});

