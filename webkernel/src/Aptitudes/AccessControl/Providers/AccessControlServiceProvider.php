<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Filament\Facades\Filament;
use Webkernel\Aptitudes\AccessControl\Logic\Resources\AccessControlPolicy;
use Webkernel\Aptitudes\AccessControl\Commands\SyncPermissionsCommand;
use Webkernel\Aptitudes\AccessControl\Commands\CreateSuperAdminCommand;

class AccessControlServiceProvider extends ServiceProvider
{
  /**
   * Register services
   */
  public function register(): void {}

  /**
   * Bootstrap services
   */
  public function boot(): void
  {
    // Register dynamic policies after all providers are loaded
    $this->app->booted(function () {
      $this->registerDynamicPolicies();
    });

    // Extend User model with permissions trait
    $this->extendUserModel();
  }

  /**
   * Register dynamic policies for all Filament resources
   */
  protected function registerDynamicPolicies(): void
  {
    try {
      $panels = Filament::getPanels();

      foreach ($panels as $panel) {
        $resources = $panel->getResources();

        foreach ($resources as $resource) {
          $model = $resource::getModel();

          if (!$model || !class_exists($model)) {
            continue;
          }

          // Register the dynamic policy for each model class
          Gate::policy($model, AccessControlPolicy::class);
        }
      }
    } catch (\Exception $e) {
      // Log error but don't break the application
      report($e);
    }
  }

  /**
   * Extend the User model with permissions trait
   */
  protected function extendUserModel(): void
  {
    // Get the User model class
    $userModel = config('auth.providers.users.model', \App\Models\User::class);
  }
}
