<?php

namespace Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Config;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Product', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(\Modules\Product\App\Providers\EventServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Product', 'Config/config.php') => config_path('product.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Product', 'Config/config.php'), 'product'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/product');

        $sourcePath = module_path('Product', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', 'product-module-views']);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/product';
        }, Config::get('view.paths')), [$sourcePath]), 'product');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/product');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'product');
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path('Product', 'Resources/lang'), 'product');
            $this->loadJsonTranslationsFrom(module_path('Product', 'Resources/lang'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
