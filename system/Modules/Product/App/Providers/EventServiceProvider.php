<?php

namespace Modules\Product\App\Providers; // Corrected namespace

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Product\App\Events\ProductCreated;
use Modules\Product\App\Events\ProductUpdated;
use Modules\Product\App\Events\ProductDeleted;
use Modules\Product\App\Listeners\ProductCacheInvalidationListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProductCreated::class => [
            ProductCacheInvalidationListener::class,
        ],
        ProductUpdated::class => [
            ProductCacheInvalidationListener::class,
        ],
        ProductDeleted::class => [
            ProductCacheInvalidationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
