<?php

namespace Modules\Others\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Others\App\Models\CategoriesTrending;
use Modules\Others\App\Observers\CategoriesTrendingObserver;

class OthersServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Others';

    protected string $moduleNameLower = 'others';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/migrations'));
        CategoriesTrending::observe(CategoriesTrendingObserver::class);
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
        $this->publishes([module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower);
    }
}
