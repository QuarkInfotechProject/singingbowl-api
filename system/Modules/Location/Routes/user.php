<?php

use Illuminate\Support\Facades\Route;
use Modules\Location\App\Http\Controllers\LocationIndexController;


Route::get('/locations', LocationIndexController::class);


