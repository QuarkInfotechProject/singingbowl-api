<?php
use Illuminate\Support\Facades\Route;
use Modules\Brand\App\Http\Controllers\Admin\BrandIndexController;
use Modules\Brand\App\Http\Controllers\Admin\BrandCreateController;
use Modules\Brand\App\Http\Controllers\Admin\BrandUpdateController;
use Modules\Brand\App\Http\Controllers\Admin\BrandActiveInactiveStatusController;
use Modules\Brand\App\Http\Controllers\Admin\BrandShowController;

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_brand']], function () {

    Route::prefix('brand')->group(function () {
        
        Route::post('/', BrandIndexController::class);
            //->middleware('can:view_brands');

        Route::post('/create', BrandCreateController::class);
            //->middleware('can:create_brand');
        
        Route::get('/show/{id}', BrandShowController::class);
            //->middleware('can:view_brand');
        
        Route::post('/update', BrandUpdateController::class);
           // ->middleware('can:update_brand');
        
        Route::post('/change-status', BrandActiveInactiveStatusController::class);
    });
});
