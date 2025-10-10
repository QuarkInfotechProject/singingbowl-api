<?php

use Illuminate\Support\Facades\Route;
use Modules\AccessGroup\App\Http\Controllers\Permission\PermissionForMenuController;
use Modules\AccessGroup\App\Http\Controllers\Permission\PermissionIndexController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleAssignPermissionForMenuController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleAssignPermissionController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleCreateController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleDestroyController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleIndexController;
use Modules\AccessGroup\App\Http\Controllers\Role\RolePermissionMappingForMenuController;
use Modules\AccessGroup\App\Http\Controllers\Role\RolePermissionMappingController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleRevokePermissionController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleShowController;
use Modules\AccessGroup\App\Http\Controllers\Role\RoleUpdateController;

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

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin']], function () {
    Route::prefix('permission')->group(function () {
        Route::get('/', PermissionIndexController::class);
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'auth:admin', 'can:view_roles']], function () {
    Route::prefix('group')->group(function () {
        Route::get('/', RoleIndexController::class);
//            ->middleware('can:view_roles');

        Route::post('/create', RoleCreateController::class);
//            ->middleware('can:create_role');

        Route::get('/show/{id}', RoleShowController::class);
//            ->middleware('can:view_roles');

        Route::post('/update', RoleUpdateController::class);
//            ->middleware('can:update_role');

        Route::post('/destroy', RoleDestroyController::class);
//            ->middleware('can:delete_role');

        Route::post('/assign-module-permission', RoleAssignPermissionController::class);
//            ->middleware('can:assign_module_permission_to_role');

        Route::post('/revoke-permission', RoleRevokePermissionController::class);
//            ->middleware('can:revoke_permission_from_role');

        Route::get('/module-mapping', RolePermissionMappingController::class);
//            ->middleware('can:update_role');
    });
});
