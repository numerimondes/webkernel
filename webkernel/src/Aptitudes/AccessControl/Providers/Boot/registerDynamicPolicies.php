<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Providers\Boot;

use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Access\Gate;
use Webkernel\Aptitudes\AccessControl\Logic\Resources\AccessControlPolicy;

class registerDynamicPolicies
{
  public function handle(): void
  {
    // Register the dynamic policies
    $this->registerDynamicPolicies();
  }

  protected function registerDynamicPolicies(): void
  {
    $panels = Filament::getPanels();

    foreach ($panels as $panel) {
      $resources = $panel->getResources();

      foreach ($resources as $resource) {
        $model = $resource::getModel();

        if (!$model || !class_exists($model)) {
          continue;
        }

        /**
         * Register the dynamic policy for each model class
         *
         * The Gate contract is resolved from the service container
         * using the Gate::class binding. This ensures the proper
         * gate manager instance is obtained for policy registration.
         */
        app(Gate::class)->policy($model, AccessControlPolicy::class);
      }
    }
  }
}
