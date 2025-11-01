<?php
declare(strict_types=1);
namespace Platform\Numerimondes\MasterConnector;
use Webkernel\Aptitudes\Platform\{Core\CoreModule, Connector\ConnectorModule, Updator\UpdatorModule};
use Platform\Numerimondes\MasterConnector\Filament\Pages\Dashboard as MasterConnectorDashboard;
use Webkernel\Arcanes\WebkernelApp;
use Webkernel\Arcanes\QueryModules;
use Platform\Numerimondes\Filament\Resources\Software\SoftwareResource;
class MasterConnectorModule extends WebkernelApp
{
  public function configureModule(): void
  {
    $this->setModuleConfig(
      $this->module()
        ->id('master-connector')
        ->name('MasterConnector')
        ->version('1.0.0')
        ->description('Master server orchestration module for Numerimondes distribution system')
        ->viewNamespace('master-connector')
        ->dependencies([])
        ->aliases([])
        ->providers([])
        ->moduleProvides([
          // 'Widgets' => [[SomeWidget::class, '*'], [AnotherWidget::class, ['panel_id_0']],
          //  'Pages' => [[MasterConnectorDashboard::class, ['workspace']]],
        ])
        ->build(),
    );
  }
  public function register(): void
  {
    parent::register();
    // Register your services, bindings, etc.
    // Uncomment to bulk load providers from a specific folder
    // QueryModules::loadFromModule('master-connector', 'Providers', true);
  }
  public function boot(): void
  {
    parent::boot();
    // Boot your module specific logic
    // Uncomment to load module resources at boot time
    // QueryModules::loadFromModule('master-connector', 'Providers', true);

    $this->publishes(
      [
        __DIR__ . '/Config/webkernel-master-connector.php' => config_path('webkernel-master-connector.php'),
      ],
      'webkernel-master-connector-config',
    );
  }
}
