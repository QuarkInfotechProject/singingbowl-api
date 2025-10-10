<?php
namespace Modules\Brand\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\\Brand\\App\\Http\\Controllers';

    /**
     * Boot the route service provider.
     */
    public function boot(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "api" routes for the application.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api/admin')
            ->middleware(['admin'])
            ->namespace($this->moduleNamespace)
            ->group(function () {
                $this->loadRoutesFromIfExists(module_path('Brand', '/Routes/admin.php'));
            });

        Route::prefix('api/user')
            ->namespace($this->moduleNamespace)
            ->group(function () {
                $this->loadRoutesFromIfExists(module_path('Brand', '/Routes/user.php'));
            });
    }

    /**
     * Define the "web" routes for the application (if needed).
     */
    protected function mapWebRoutes(): void
    {
        Route::prefix('web')
            ->middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(function () {
                $this->loadRoutesFromIfExists(module_path('Brand', '/Routes/web.php'));
            });
    }

    /**
     * Load routes only if the file exists to avoid errors.
     */
    protected function loadRoutesFromIfExists($path): void
    {
        if (file_exists($path)) {
            Route::group([], function () use ($path) {
                require $path;
            });
        }
    }
}
