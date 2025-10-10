<?php

namespace Modules\Meta\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MetaServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Meta';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/migrations'));
    }
}
