<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Webkernel\Providers\WebkernelRouteServiceProvider;

class WebkernelPlatformServiceProvider extends ServiceProvider
{
    /**
     * Register additional services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Créer les liens symboliques pour les assets des packages
        $this->createPackageSymlinks();

        // Charger les routes
        $routeProvider = new WebkernelRouteServiceProvider($this->app);
        $routeProvider->loadRoutes();

        // Charger la config platform après que tout soit prêt
        $this->app->booted(function () {
            $platformFile = base_path('platform/platform.php');
            if (file_exists($platformFile)) {
                require $platformFile;
            }
        });
    }

    /**
     * Créer des liens symboliques pour les assets des packages
     */
    protected function createPackageSymlinks(): void
    {
        $publicPath = public_path('storage');

        // S'assurer que le dossier storage existe
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        // Créer le lien pour webkernel
        $webkernelSource = base_path('packages/webkernel/src/resources');
        $webkernelTarget = $publicPath . '/packages/webkernel/src/resources';

        if (File::exists($webkernelSource) && !File::exists($webkernelTarget)) {
            File::makeDirectory(dirname($webkernelTarget), 0755, true);

            if (PHP_OS_FAMILY === 'Windows') {
                // Sur Windows, utiliser une copie récursive
                File::copyDirectory($webkernelSource, $webkernelTarget);
            } else {
                // Sur Unix/Linux, utiliser un lien symbolique
                symlink($webkernelSource, $webkernelTarget);
            }
        }

        // Vous pouvez ajouter d'autres packages ici
        $this->createSymlinksForOtherPackages($publicPath);
    }

    /**
     * Créer des liens pour d'autres packages automatiquement
     */
    protected function createSymlinksForOtherPackages(string $publicPath): void
    {
        $packagesPath = base_path('packages');

        if (!File::exists($packagesPath)) {
            return;
        }

        $packages = File::directories($packagesPath);

        foreach ($packages as $packagePath) {
            $packageName = basename($packagePath);

            // Ignorer webkernel (déjà traité)
            if ($packageName === 'webkernel') {
                continue;
            }

            $resourcesPath = $packagePath . '/src/resources';

            if (File::exists($resourcesPath)) {
                $targetPath = $publicPath . '/packages/' . $packageName . '/src/resources';

                if (!File::exists($targetPath)) {
                    File::makeDirectory(dirname($targetPath), 0755, true);

                    if (PHP_OS_FAMILY === 'Windows') {
                        File::copyDirectory($resourcesPath, $targetPath);
                    } else {
                        symlink($resourcesPath, $targetPath);
                    }
                }
            }
        }
    }
}
