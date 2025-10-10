<?php

use Illuminate\Support\Facades\Route;

use Modules\User\App\Http\Controllers\Admin\UserActivateController;
use Modules\User\App\Http\Controllers\Admin\UserBlockController;
use Modules\User\App\Http\Controllers\Admin\UserIndexController;
use Modules\User\App\Http\Controllers\Admin\UserShowController;
use Modules\User\App\Http\Controllers\Admin\UserSummaryController;
use Modules\User\App\Http\Controllers\Admin\UserUpdateController;


Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_users'], 'prefix' => 'end-users'], function () {
    Route::post('/', UserIndexController::class);
    Route::get('show/{uuid}', UserShowController::class);

    Route::post('block', UserBlockController::class);
//        ->middleware('can:block_user');

    Route::post('activate', UserActivateController::class);
//        ->middleware('can:reactivate_user');

    Route::post('update', UserUpdateController::class);
    Route::post('summary', UserSummaryController::class)
        ->middleware('can:view_customers');
});
