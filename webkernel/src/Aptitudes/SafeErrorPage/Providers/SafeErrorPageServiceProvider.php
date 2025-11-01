<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\SafeErrorPage\Providers;

use Illuminate\Support\ServiceProvider;
use Webkernel\Aptitudes\SafeErrorPage\Support\ActionRegistry;
use Illuminate\Support\Facades\Artisan;

class SafeErrorPageServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    // Register services if needed
  }

  public function boot(): void
  {
    // Register default actions
    ActionRegistry::registerDefaults();

    // Register custom application-specific actions
    $this->registerCustomActions();
  }

  /**
   * Register custom actions for your application.
   *
   * @return void
   */
  private function registerCustomActions(): void
  {
    // Example: Migration runner
    ActionRegistry::register('run-migrations', function (): string {
      Artisan::call('migrate', ['--force' => true]);
      return 'Migrations executed successfully';
    });

    // Example: Seed database
    ActionRegistry::register('seed-database', function (): string {
      Artisan::call('db:seed', ['--force' => true]);
      return 'Database seeded successfully';
    });

    // Example: Queue restart
    ActionRegistry::register('restart-queue', function (): string {
      Artisan::call('queue:restart');
      return 'Queue workers restarted';
    });

    // Example: Storage link
    ActionRegistry::register('storage-link', function (): string {
      Artisan::call('storage:link');
      return 'Storage link created';
    });
  }
}
