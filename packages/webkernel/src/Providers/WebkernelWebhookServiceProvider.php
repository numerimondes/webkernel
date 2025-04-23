<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;

class WebkernelWebhookServiceProvider extends ServiceProvider
{
    /**
     * Register services and bindings related to webhooks.
     *
     * @return void
     */
    public function register(): void
    {
        // You can bind webhook-related services here.
        // Example:
        // $this->app->bind(WebhookHandlerInterface::class, CustomWebhookHandler::class);
    }

    /**
     * Bootstrap any application services related to webhooks.
     *
     * @return void
     */
    public function boot(): void
    {
        // Perform webhook route registration, events, etc.
        // Example:
        // $this->loadRoutesFrom(__DIR__ . '/../routes/webhooks.php');
    }
}
