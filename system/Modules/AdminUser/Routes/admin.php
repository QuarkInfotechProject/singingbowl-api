<?php

use Illuminate\Support\Facades\Route;
use Modules\AdminUser\App\Http\Controllers\AdminUserActivateController;
use Modules\AdminUser\App\Http\Controllers\AdminUserActivityLogController;
use Modules\AdminUser\App\Http\Controllers\AdminUserCreateController;
use Modules\AdminUser\App\Http\Controllers\AdminUserDeactivateController;
use Modules\AdminUser\App\Http\Controllers\AdminUserDestroyController;
use Modules\AdminUser\App\Http\Controllers\AdminUserIndexController;
use Modules\AdminUser\App\Http\Controllers\AdminUserShowController;
use Modules\AdminUser\App\Http\Controllers\AdminUserUpdateController;
use Modules\AdminUser\App\Http\Controllers\Analytics\AnalyticsLeaderboardsController;
use Modules\AdminUser\App\Http\Controllers\Analytics\AnalyticsPerformanceController;
use Modules\AdminUser\App\Http\Controllers\Analytics\AnalyticsRevenueStatsController;
use Modules\AdminUser\App\Http\Controllers\Auth\AdminUserChangePasswordController;
use Modules\AdminUser\App\Http\Controllers\Auth\AdminUserLoginController;
use Modules\AdminUser\App\Http\Controllers\Auth\AdminUserLogoutController;
use Modules\AdminUser\App\Http\Controllers\Dashboard\DashboardController;

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
Route::middleware(['cors'])->group(function () {
    Route::post('login', AdminUserLoginController::class);
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_admin_users'], 'prefix' => 'users'], function () {
    Route::post('/', AdminUserIndexController::class);

    Route::post('create', AdminUserCreateController::class);
//        ->middleware('can:create_admin_user');

    Route::get('show/{uuid}', AdminUserShowController::class);

    Route::post('update', AdminUserUpdateController::class);
//        ->middleware('can:update_admin_user');

    Route::post('destroy', AdminUserDestroyController::class);
//        ->middleware('can:delete_admin_user');

    Route::post('deactivate', AdminUserDeactivateController::class);
//        ->middleware('can:block_admin_user');

    Route::post('activate', AdminUserActivateController::class);
//        ->middleware('can:reactivate_admin_user');
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::post('logout', AdminUserLogoutController::class);

    Route::post('change-password', AdminUserChangePasswordController::class);

    Route::post('activity-logs', AdminUserActivityLogController::class)
        ->middleware('can:view_activity_logs');

    Route::post('dashboard', DashboardController::class)
        ->middleware('can:view_dashboard');

    Route::post('analytics/performance-indicators', AnalyticsPerformanceController::class)
        ->middleware('can:view_analytics');

    Route::post('analytics/revenue-stats', AnalyticsRevenueStatsController::class)
        ->middleware('can:view_analytics');

    Route::post('analytics/leaderboards', AnalyticsLeaderboardsController::class)
        ->middleware('can:view_analytics');
});
