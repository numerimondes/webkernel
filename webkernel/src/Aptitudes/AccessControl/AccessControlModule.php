<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl;

use Webkernel\Arcanes\QueryModules;
use Webkernel\Arcanes\WebkernelApp;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\AccessControlResource;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Widgets\Stats as AccessControlStatsWidget;

class AccessControlModule extends WebkernelApp
{
  public function configureModule(): void
  {
    $this->setModuleConfig(
      $this->module()
        ->id('access-control')
        ->name('AccessControl')
        ->version('2.0.0')
        ->description('Simple and robust access control system')
        ->viewNamespace('access-control')
        ->dependencies([])
        ->aliases([])
        ->providers([\Webkernel\Aptitudes\AccessControl\Providers\AccessControlServiceProvider::class])
        ->moduleProvides([
          'Widgets' => [[AccessControlStatsWidget::class, ['system']]],
          'Pages' => [
            /* ['PermissionGroupsPage', '*']*/
          ],
          'Resources' => [[AccessControlResource::class, ['system']]],
        ])
        ->build(),
    );
  }

  public function register(): void
  {
    parent::register();
    QueryModules::loadFromModule('access-control', 'Providers/Register', true);
  }

  public function boot(): void
  {
    parent::boot();
    QueryModules::loadFromModule('access-control', 'Providers/Boot', true);
  }
}
