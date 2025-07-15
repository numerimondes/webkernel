<?php

namespace Webkernel\CRM;

use Illuminate\Support\ServiceProvider;

class CRM extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(CRMServiceProvider::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'webkernel-crm');
    }
} 