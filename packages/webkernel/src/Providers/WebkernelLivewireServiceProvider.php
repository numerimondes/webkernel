<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class WebkernelLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Register views path
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'webkernel');

        // 2. Auto-discover components from Webkernel and platform/packages
        $livewirePaths = $this->getLivewirePaths();
        foreach ($livewirePaths as $path => $namespace) {
            $this->registerLivewireComponentsFrom($path, $namespace);
        }
    }

    protected function registerLivewireComponentsFrom(string $path, string $namespace): void
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (glob($path . '/*.php') as $file) {
            $class = $namespace . '\\' . basename($file, '.php');

            if (class_exists($class)) {
                $alias = Str::kebab(class_basename($class));
                $namespacePrefix = strtolower(str_replace('\\', '.', $namespace));
                Livewire::component("{$namespacePrefix}.{$alias}", $class);
            }
        }
    }

    /**
     * Get Livewire component paths and their namespaces
     *
     * @return array
     */
    protected function getLivewirePaths(): array
    {
        $paths = [
            base_path('packages/webkernel/src/Livewire') => 'Webkernel\\Livewire',
            base_path('packages/webkernel/src/Http/Livewire') => 'Webkernel\\Http\\Livewire',
        ];

        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $livewirePath = $path . '/Livewire';
            if (File::isDirectory($livewirePath)) {
                $paths[$livewirePath] = $namespace . 'Livewire';
            }
            $httpLivewirePath = $path . '/Http/Livewire';
            if (File::isDirectory($httpLivewirePath)) {
                $paths[$httpLivewirePath] = $namespace . 'Http\\Livewire';
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

    public function register(): void
    {
        //
    }
}
