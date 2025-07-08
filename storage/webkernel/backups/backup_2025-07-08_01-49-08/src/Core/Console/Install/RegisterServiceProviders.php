<?php

namespace Webkernel\Core\Console\Install;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RegisterServiceProviders extends Command
{
    protected $signature = 'webkernel:install-register-providers';
    protected $description = 'Webkernel Installer Composer Updater';
    protected $hidden = true;
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->registerServiceProviders();
    }

    protected function registerServiceProviders(): void
    {
        $providerPath = base_path('bootstrap/providers.php');

        if (!File::exists($providerPath)) {
            $this->error('❌ bootstrap/providers.php not found. Please ensure you\'re running this in a Laravel 12+ project.');
            exit(1);
        }

        $this->info('[✓] Found bootstrap/providers.php');

        $providers = [
            "Webkernel\\Core\\Providers\\WebkernelServiceProvider::class",
            "Webkernel\\Core\\Providers\\Filament\\SystemPanelProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelRenderHooksServiceProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelBladeServiceProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelCommandServiceProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelHelperServiceProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelMigrationServiceProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelRouteServiceProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelViewServiceProvider::class",
            "Webkernel\\Core\\Providers\\WebkernelWebhookServiceProvider::class",
        ];

        $contents = File::get($providerPath);
        $modified = false;

        foreach ($providers as $provider) {
            if (strpos($contents, $provider) !== false) {
                $this->info('[✓] ' . $provider . ' already registered');
                continue;
            }

            $contents = preg_replace(
                '/(return\s*\[\s*(.*?))(\];)/s',
                "$1\n    $provider,\n$3",
                $contents
            );

            $this->info('[✓] Added ' . $provider . ' to providers.php');
            $modified = true;
        }

        if ($modified) {
            File::put($providerPath, $contents);
            $this->info('[✓] Saved providers.php with new providers');
        } else {
            $this->info('[✓] No new providers to add');
        }
    }
}
