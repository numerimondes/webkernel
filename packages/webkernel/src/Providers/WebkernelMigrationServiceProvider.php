<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class WebkernelMigrationServiceProvider extends ServiceProvider
{
    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->loadMigrationsFromPackages();
    }

    /**
     * Load migrations from all packages and platform.
     */
    protected function loadMigrationsFromPackages(): void
    {
        $migrationPaths = $this->getMigrationPaths();
        foreach ($migrationPaths as $path) {
            if (File::isDirectory($path)) {
                $this->loadMigrationsFrom($path);
            }
        }
    }

    /**
     * Get migration paths
     *
     * @return array
     */
    protected function getMigrationPaths(): array
    {
        $paths = [
            base_path('packages/webkernel/src/database/migrations'),
        ];

        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $migrationPath = $path . '/database/migrations';
            if (File::isDirectory($migrationPath)) {
                $paths[] = $migrationPath;
            }
        }

        return array_unique($paths);
    }

    /**
     * Get PSR-4 namespaces from composer.json
     *
     * @return array
     */
    protected function getAutoloadNamespaces(): array
    {
        $composerJson = json_decode(File::get(base_path('composer.json')), true);
        $namespaces = [];

        if (isset($composerJson['autoload']['psr-4'])) {
            foreach ($composerJson['autoload']['psr-4'] as $namespace => $path) {
                if (str_starts_with($path, 'platform/') || (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/')) {
                    $namespaces[rtrim($namespace, '\\') . '\\'] = base_path($path);
                }
            }
        }

        return $namespaces;
    }
}
