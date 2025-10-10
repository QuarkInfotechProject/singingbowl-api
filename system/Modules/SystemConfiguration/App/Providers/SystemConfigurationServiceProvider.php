<?php

namespace Modules\SystemConfiguration\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\SystemConfiguration\App\Console\LoadEmailTemplates;
use Modules\SystemConfiguration\App\Console\LoadSystemConfigSettings;

class SystemConfigurationServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'SystemConfiguration';

    protected string $moduleNameLower = 'systemconfiguration';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerConfig();
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
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
         $this->commands([
             LoadEmailTemplates::class,
             LoadSystemConfigSettings::class
         ]);
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
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
