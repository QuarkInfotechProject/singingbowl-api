<?php

namespace App\Providers\Event;

use App\Events\AdminActivityLogEvent;
use App\Listeners\AdminActivityLogListener;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

class EmailEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AdminActivityLogEvent::class => [
            AdminActivityLogListener::class,
        ],
    ];

    public function boot()
    {
        // Perform bootstrapping tasks here
        // For example: $this->loadViewsFrom(__DIR__.'/path/to/views', 'myviews');
    }
}
