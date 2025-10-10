<?php

namespace Modules\Cart\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Modules\Cart\App\Http\Middleware\CartAuthMiddleware;

class CartServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Cart';

    protected string $moduleNameLower = 'cart';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/migrations'));
        $this->registerMiddleware();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower);
    }

    /**
     * Register middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('cart.auth', CartAuthMiddleware::class);
    }
}
