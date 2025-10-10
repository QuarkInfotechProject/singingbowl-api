<?php

namespace Modules\Others\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\Others\App\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern-based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapAdminApiRoutes();
        $this->mapUserApiRoutes();
    }

    /**
     * Define the "admin" routes for the application.
     */
    protected function mapAdminApiRoutes(): void
    {
        Route::prefix('api/admin')
            ->middleware('admin')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Others', '/Routes/admin.php'));
    }

    /**
     * Define the "user" routes for the application.
     */
    protected function mapUserApiRoutes(): void
    {
        Route::prefix('api/user')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Others', '/Routes/user.php'));
    }
}
