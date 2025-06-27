<?php

namespace Webkernel\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WebkernelPoliciesServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $policyPaths = $this->getPolicyPaths();

        foreach ($policyPaths as $policyPath => $namespace) {
            if (!File::isDirectory($policyPath)) {
                continue;
            }

            $files = File::allFiles($policyPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $policyClass = $namespace . 'Policies\\' . Str::studly($file->getBasename('.php'));
                    $modelClass = $namespace . 'Models\\' . Str::studly($file->getBasename('.php'));

                    if (class_exists($modelClass)) {
                        Gate::policy($modelClass, $policyClass);
                    }
                }
            }
        }
    }

    /**
     * Get policy paths and their corresponding namespaces
     *
     * @return array
     */
    protected function getPolicyPaths(): array
    {
        $paths = [
            base_path('packages/webkernel/src/Policies') => 'Webkernel\\',
        ];

        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $policyPath = $path . '/Policies';
            if (File::isDirectory($policyPath)) {
                $paths[$policyPath] = $namespace;
            }
        }

        return $paths;
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
