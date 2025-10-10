<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\ActivityLog\Interface\ActivityLogRepositoryInterface;
use Src\ActivityLog\Repository\ActivityLogRepository;
use Src\AdminUser\Interface\AdminUserRepositoryInterface;
use Src\AdminUser\Repository\AdminUserRepository;
use Src\Config\Interface\EmailTemplateRepositoryInterface;
use Src\Config\Interface\SystemConfigRepositoryInterface;
use Src\Config\Repository\EmailTemplateRepository;
use Src\Config\Repository\SystemConfigRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
        $this->app->bind(AdminUserRepositoryInterface::class, AdminUserRepository::class);
        $this->app->bind(SystemConfigRepositoryInterface::class, SystemConfigRepository::class);
        $this->app->bind(EmailTemplateRepositoryInterface::class, EmailTemplateRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
