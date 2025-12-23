<?php

use Illuminate\Support\Facades\Route;
use Modules\Gallery\App\Http\Controllers\Admin\GalleryCreateController;
use Modules\Gallery\App\Http\Controllers\Admin\GalleryDestroyController;
use Modules\Gallery\App\Http\Controllers\Admin\GalleryIndexController;
use Modules\Gallery\App\Http\Controllers\Admin\GalleryShowController;
use Modules\Gallery\App\Http\Controllers\Admin\GalleryUpdateController;

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('galleries')->group(function () {
        Route::post('/', GalleryIndexController::class);
        Route::post('create', GalleryCreateController::class);
        Route::get('show/{id}', GalleryShowController::class);
        Route::post('update', GalleryUpdateController::class);
        Route::post('destroy', GalleryDestroyController::class);
    });
});

