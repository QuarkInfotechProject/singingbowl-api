<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemConfiguration\App\Http\Controllers\EmailTemplate\EmailTemplateIndexController;
use Modules\SystemConfiguration\App\Http\Controllers\EmailTemplate\EmailTemplateShowController;
use Modules\SystemConfiguration\App\Http\Controllers\EmailTemplate\EmailTemplateUpdateController;
use Modules\SystemConfiguration\App\Http\Controllers\Setting\SettingIndexController;
use Modules\SystemConfiguration\App\Http\Controllers\Setting\SettingUpdateController;


/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {

    Route::prefix('settings')->group(function () {
        Route::get('/', SettingIndexController::class)
            ->middleware('can:view_setting_configurations');

        Route::post('/update', SettingUpdateController::class)
            ->middleware('can:view_setting_configurations');
    });

    Route::prefix('emails')->group(function () {
        Route::get('/', EmailTemplateIndexController::class)
            ->middleware('can:view_email_templates');

        Route::get('/show/{name}', EmailTemplateShowController::class)
            ->middleware('can:view_email_templates');

        Route::post('/update', EmailTemplateUpdateController::class)
            ->middleware('can:view_email_templates');
    });
});
