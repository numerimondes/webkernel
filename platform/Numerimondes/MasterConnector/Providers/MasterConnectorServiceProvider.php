<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Platform\Numerimondes\MasterConnector\Http\Middleware\AuthenticateToken;
use Platform\Numerimondes\MasterConnector\Services\LicenseManager;
use Platform\Numerimondes\MasterConnector\Services\ModuleCatalog;
use Platform\Numerimondes\MasterConnector\Services\ModuleUploader;

class MasterConnectorServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    // Register singletons
    $this->app->singleton(LicenseManager::class);
    $this->app->singleton(ModuleCatalog::class);
    $this->app->singleton(ModuleUploader::class);

    // Merge configuration
    $this->mergeConfigFrom(__DIR__ . '/../Config/master-connector.php', 'master-connector');
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    // Register middleware
    $this->app['router']->aliasMiddleware('numerimondes.auth', AuthenticateToken::class);

    // Load migrations
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

    // Register API routes
    $this->registerRoutes();

    // Publish configuration
    $this->publishes(
      [
        __DIR__ . '/../Config/master-connector.php' => config_path('master-connector.php'),
      ],
      'master-connector-config',
    );

    // Publish migrations (optional)
    $this->publishes(
      [
        __DIR__ . '/../Database/Migrations' => database_path('migrations'),
      ],
      'master-connector-migrations',
    );
  }

  /**
   * Register API routes
   */
  protected function registerRoutes(): void
  {
    Route::group(
      [
        'prefix' => 'api',
        'middleware' => ['api'],
      ],
      function () {
        // Authentication endpoint (no token required)
        Route::post('/auth/validate', [
          \Platform\Numerimondes\MasterConnector\Http\Controllers\AuthController::class,
          'validate',
        ])->middleware('numerimondes.rate-limit:auth');

        // Protected endpoints (require token authentication)
        Route::group(
          [
            'middleware' => ['numerimondes.auth'],
          ],
          function () {
            // Module listing
            Route::get('/modules/list', [
              \Platform\Numerimondes\MasterConnector\Http\Controllers\ModulesController::class,
              'list',
            ])->middleware('numerimondes.rate-limit:list');

            // Module download
            Route::get('/modules/download', [
              \Platform\Numerimondes\MasterConnector\Http\Controllers\ModulesController::class,
              'download',
            ])->middleware('numerimondes.rate-limit:download');

            // Checksum
            Route::get('/modules/checksum/{identifier}', [
              \Platform\Numerimondes\MasterConnector\Http\Controllers\ModulesController::class,
              'checksum',
            ])->middleware('numerimondes.rate-limit:list');

            // Updates check
            Route::post('/modules/updates', [
              \Platform\Numerimondes\MasterConnector\Http\Controllers\ModulesController::class,
              'updates',
            ])->middleware('numerimondes.rate-limit:list');
          },
        );
      },
    );
  }
}
