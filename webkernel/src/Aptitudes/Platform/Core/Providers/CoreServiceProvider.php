<?php

namespace Webkernel\Aptitudes\Platform\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Aptitudes\Platform\Core\Models\LocalLicense;
use Webkernel\Aptitudes\Platform\Core\Services\EncryptionService;
use Webkernel\Aptitudes\Platform\Core\Services\LicenseCacheService;
use Webkernel\Aptitudes\Platform\Core\Services\LicenseTokenService;

class CoreServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->singleton(LicenseTokenService::class);
    $this->app->singleton(EncryptionService::class);
    $this->app->singleton(LicenseCacheService::class);
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    // LocalLicense::observe(); // Placeholder for any observers if needed in future
  }
}
