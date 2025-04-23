<?php


namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;

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
     * Load migrations from all packages.
     */
    protected function loadMigrationsFromPackages(): void
    {
        $packagesPath = base_path('packages');
        $migrationsPaths = glob("{$packagesPath}/*/src/database/migrations", GLOB_ONLYDIR);

        foreach ($migrationsPaths as $path) {
            $this->loadMigrationsFrom($path);
        }
    }
}
