<?php declare(strict_types=1);
namespace Platform\Numerimondes;
use Platform\Numerimondes\Filament\Resources\Software\SoftwareResource;
use Webkernel\Arcanes\WebkernelApp;
use Platform\Numerimondes\Providers\PlatformPanelProvider;

class Numerimondes extends WebkernelApp
{
  public function configureModule(): void
  {
    $this->setModuleConfig(
      $this->module()
        ->id('numerimondes')
        ->name('Numerimondes')
        ->version('1.0.0')
        ->description('Numerimondes Platform Module')
        ->viewNamespace('numerimondes')
        ->dependencies([])
        ->aliases([])
        ->moduleProvides([
          'Resources' => [
            // [UserResource::class, ['admin', 'workspace']],
          ],
          'Pages' => [
            // [WelcomeSystemDashboard::class, 'system'],
          ],
          'Widgets' => [
            // [SomeWidget::class, '*'],
            // [DashboardWidget::class, ['admin', 'workspace']],
          ],
          'middleware' => [
            // [\App\Http\Middleware\EncryptCookies::class, '*'],
          ],
          'authMiddleware' => [
            // [\App\Http\Middleware\Authenticate::class, ['admin', 'workspace']],
          ],
        ])
        ->providers([PlatformPanelProvider::class])
        ->build(),
    );
  }

  public function register(): void
  {
    parent::register();

    // Register your services, bindings, etc.
  }
  public function boot(): void
  {
    parent::boot();

    // Boot your module specific logic
  }
}
