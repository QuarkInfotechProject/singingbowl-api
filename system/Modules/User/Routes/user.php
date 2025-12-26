<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\Profile\UserProfileShowController;
use Modules\User\App\Http\Controllers\Profile\UserProfileUpdateController;
use Modules\User\App\Http\Controllers\UserChangePasswordController;
use Modules\User\App\Http\Controllers\UserForgotPasswordController;
use Modules\User\App\Http\Controllers\UserGoogleLoginController;
use Modules\User\App\Http\Controllers\UserLoginController;
use Modules\User\App\Http\Controllers\UserLogoutController;
use Modules\User\App\Http\Controllers\UserRegisterController;
use Modules\User\App\Http\Controllers\UserResetPasswordController;
use Modules\User\App\Http\Controllers\UserSendRegisterMailController;
use Modules\User\App\Http\Controllers\UserSocialLoginCallbackController;
use Modules\User\App\Http\Controllers\UserSocialLoginRedirectController;

Route::middleware(['guest'])->group(function () {
    Route::post('/register', UserRegisterController::class);
    Route::post('/forgot-password', UserForgotPasswordController::class);
    Route::post('/register/send-mail', UserSendRegisterMailController::class);
    Route::post('/reset-password', UserResetPasswordController::class);

    Route::post('/login', UserLoginController::class);
    Route::post('/auth/google/login', UserGoogleLoginController::class);

    Route::get('/auth/{provider}/redirect', UserSocialLoginRedirectController::class);
    Route::get('/auth/{provider}/callback', UserSocialLoginCallbackController::class);
});

Route::group(['middleware' => 'auth:user'], function () {
    Route::post('/logout', UserLogoutController::class);
    Route::post('/change-password', UserChangePasswordController::class);
    Route::get('/profile', UserProfileShowController::class);
    Route::post('/profile/update', UserProfileUpdateController::class);
});
