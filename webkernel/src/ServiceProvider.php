<?php

declare(strict_types=1);

namespace Webkernel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Foundation\Application;

/**
 * Main service provider for Webkernel package.
 */
class ServiceProvider extends LaravelServiceProvider
{
  public function register()
  {
    // Initialiser les timings dès le début
    $timings = [
      'start' => LARAVEL_START,
      'booting_start' => null,
      'booting_end' => null,
    ];

    $this->app->booting(function () use (&$timings) {
      $timings['booting_start'] = microtime(true);
      app()->instance('app.timings', $timings);
    });

    $this->app->booted(function () use (&$timings) {
      $timings['booting_end'] = microtime(true);
      app()->instance('app.timings', $timings);
    });

    $this->app->instance('app.timings', $timings);

    $this->app->singleton(\Webkernel\Arcanes\ArcancesServiceProvider::class, function (Application $app) {
      return new \Webkernel\Arcanes\ArcancesServiceProvider($app);
    });

    $this->app->register(\Webkernel\Arcanes\ArcancesServiceProvider::class);
  }

  public function boot()
  {
    // Boot time est déjà capturé dans register()
  }
}
