<?php

namespace Webkernel\Aptitudes\Platform\Connector\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Aptitudes\Platform\Connector\Http\Middleware\RateLimitMiddleware;
use Webkernel\Aptitudes\Platform\Connector\Services\MasterApiClient;
use Webkernel\Aptitudes\Platform\Connector\Services\StreamingDownloader;
use Webkernel\Aptitudes\Platform\Connector\Services\SyncService;

class ConnectorServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->singleton(MasterApiClient::class);
    $this->app->singleton(StreamingDownloader::class);
    $this->app->singleton(SyncService::class);
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    // Register middleware only if MasterConnector is present (server mode)
    if (class_exists(\Webkernel\Aptitudes\Platform\MasterConnector\MasterConnectorServiceProvider::class)) {
      $this->app['router']->aliasMiddleware('numerimondes.rate-limit', RateLimitMiddleware::class);
    }
  }
}
