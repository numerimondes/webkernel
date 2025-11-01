<?php

declare(strict_types=1);

use Filament\Panel;
use Webkernel\Arcanes\Support\RemoteComponents\RCService;

if (!function_exists('withRemoteComponents')) {
  /**
   * Inject remote components into a Filament panel
   *
   * Usage:
   *
   * public function panel(Panel $panel): Panel
   * {
   *     return withRemoteComponents(
   *         $panel->id('workspace')->path('workspace')->login()
   *     );
   * }
   */
  function withRemoteComponents(Panel $panel): Panel
  {
    return app(RCService::class)->inject($panel);
  }
}
