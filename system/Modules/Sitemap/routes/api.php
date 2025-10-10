<?php

use Illuminate\Support\Facades\Route;
use Modules\Sitemap\App\Http\Controllers\SitemapController;

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

Route::prefix('sitemap')->group(function () {
    Route::get('products', [SitemapController::class, 'products'])->name('sitemap.products');
    Route::get('posts', [SitemapController::class, 'posts'])->name('sitemap.posts');
});
