<?php
namespace Modules\Brand\Routes;

use Modules\Brand\App\Http\Controllers\User\BrandIndexController;
use Modules\Brand\App\Http\Controllers\User\BrandShowController;
use Illuminate\Support\Facades\Route;

Route::get('brand', BrandIndexController::class);
Route::get('brand/show', BrandShowController::class);