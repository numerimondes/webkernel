<?php
namespace Webkernel\Core\Providers;

use Illuminate\Support\ServiceProvider;

class WebkernelConstantsServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register(): void
    {
        $this->loadWebkernelConstants();
        $this->loadWebkernelStubs();
    }

    public function boot(): void
    {
        // Boot logic if needed
    }

    protected function loadWebkernelConstants(): void
    {
        $constantsPath = base_path('packages/webkernel/src/PlatformConfig/ConfigFiles/WebkernelStaticGeneratedFiles/constants-globals.php');
        if (file_exists($constantsPath)) {
            require_once $constantsPath;
        }
    }

    protected function loadWebkernelStubs(): void
    {
        $autoloadPath = base_path('packages/webkernel/src/PlatformConfig/ConfigFiles/WebkernelStaticGeneratedFiles/autoload-stubs.php');
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        }
    }

    public function provides(): array
    {
        return ['webkernel.constants'];
    }
}