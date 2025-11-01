<?php declare(strict_types=1);
namespace Webkernel\Arcanes\Support\RemoteComponents;

use Illuminate\Support\ServiceProvider;

class RCServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    $this->app->singleton(RCService::class);

    require_once __DIR__ . '/../Helpers/withRemoteComponents.php';

    // IMPORTANT: Charger les composants dès le register, pas au boot
    $this->app->resolving(RCService::class, function ($service, $app) {
      $service->loadFromModules([]);
    });
  }

  public function boot(): void
  {
    // Nothing here - loading happens in register
  }
}
