<?php
namespace Modules\FlashSale\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\FlashSale\App\Http\Controllers';

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
                $this->loadRoutesFromIfExists(module_path('FlashSale', '/Routes/admin.php'));
            });

        Route::prefix('api/user')
        ->namespace($this->moduleNamespace)
        ->group(module_path('FlashSale', '/Routes/user.php'));
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
                $this->loadRoutesFromIfExists(module_path('FlashSale', '/Routes/web.php'));
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
