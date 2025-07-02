<?php

namespace Webkernel\Providers;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class WebkernelCommandServiceProvider extends ServiceProvider
{
    protected array $coreCommands = [
        \Webkernel\Console\Package\PlatformComposer::class,
    ];

    public function register(): void
    {
        $this->commands($this->coreCommands);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerDynamicCommands();
        }
    }

    protected function registerDynamicCommands(): void
    {
        $commandPaths = $this->getCommandPaths();
        $allCommands = [];

        foreach ($commandPaths as $path => $namespace) {
            if (!File::isDirectory($path)) {
                continue;
            }

            $commands = collect(File::glob("{$path}/*.php"))
                ->map(fn($file) => $namespace . basename($file, '.php'))
                ->filter(fn($class) => $this->isValidCommand($class))
                ->toArray();

            $allCommands = array_merge($allCommands, $commands);
        }

        if (!empty($allCommands)) {
            $this->commands($allCommands);
        }
    }

    protected function getCommandPaths(): array
    {
        $paths = $this->getCoreCommandPaths();
        $autoloadNamespaces = $this->getAutoloadNamespaces();

        foreach ($autoloadNamespaces as $namespace => $basePath) {
            $commandDirectories = $this->getCommandDirectories($basePath, $namespace);
            $paths = array_merge($paths, $commandDirectories);
        }

        return $paths;
    }

    protected function getCoreCommandPaths(): array
    {
        $basePath = base_path('packages/webkernel/src');
        
        return [
            $basePath . '/Console/Commands' => 'Webkernel\\Console\\Commands\\',
            $basePath . '/Console/Install' => 'Webkernel\\Console\\Install\\',
            $basePath . '/Console/Package' => 'Webkernel\\Console\\Package\\',
        ];
    }

    protected function getCommandDirectories(string $basePath, string $namespace): array
    {
        $directories = [];
        $consolePaths = [
            '/Console',
            '/Console/Commands',
            '/Console/Install',
            '/Console/Package',
        ];

        foreach ($consolePaths as $consolePath) {
            $fullPath = $basePath . $consolePath;
            if (File::isDirectory($fullPath)) {
                $directories[$fullPath] = $namespace . 'Console\\' . 
                    ($consolePath === '/Console' ? '' : trim(str_replace('/', '\\', $consolePath), '\\Console\\') . '\\');
            }
        }

        return $directories;
    }

    protected function getAutoloadNamespaces(): array
    {
        $composerPath = base_path('composer.json');
        
        if (!File::exists($composerPath)) {
            return [];
        }

        $composerJson = json_decode(File::get($composerPath), true);
        
        if (!isset($composerJson['autoload']['psr-4'])) {
            return [];
        }

        $namespaces = [];
        
        foreach ($composerJson['autoload']['psr-4'] as $namespace => $path) {
            if ($this->isPlatformNamespace($path)) {
                $namespaces[rtrim($namespace, '\\') . '\\'] = base_path($path);
            }
        }

        return $namespaces;
    }

    protected function isPlatformNamespace(string $path): bool
    {
        return str_starts_with($path, 'platform/') || 
               (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/');
    }

    protected function isValidCommand(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        try {
            $reflection = new \ReflectionClass($class);
            return $reflection->isSubclassOf(Command::class) && 
                   !$reflection->isAbstract();
        } catch (\ReflectionException $e) {
            return false;
        }
    }
}