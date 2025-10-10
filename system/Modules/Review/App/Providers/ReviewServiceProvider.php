<?php

namespace Modules\Review\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Review\App\Events\ReviewCreated;
use Modules\Review\App\Events\ReviewDeleted;
use Modules\Review\App\Events\ReviewUpdated;
use Modules\Review\App\Listeners\ReviewCacheInvalidationListener;

class ReviewServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Review';

    protected string $moduleNameLower = 'review';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerEventListeners();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/migrations'));
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
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {
        Event::listen([
            ReviewCreated::class,
            ReviewUpdated::class,
            ReviewDeleted::class,
        ], ReviewCacheInvalidationListener::class);
    }
}
