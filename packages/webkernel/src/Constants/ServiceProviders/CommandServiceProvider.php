<?php

namespace Webkernel\Constants\ServiceProviders;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class CommandServiceProvider extends ServiceProvider
{
    protected array $coreCommands = [

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

            $commands = collect(File::glob($path . '/*.php'))
                ->map(fn ($file) => $namespace . basename($file, '.php'))
                ->filter(fn ($class) => $this->isValidCommand($class))
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
            $paths = array_merge($paths, $this->getCommandDirectories($basePath, $namespace));
        }

        return $paths;
    }

    protected function getCoreCommandPaths(): array
    {
        $basePath = base_path('packages/webkernel/src/Core');

        return [
            $basePath . '/Console/Commands' => 'Webkernel\Core\\Console\\Commands\\',
            $basePath . '/Console/Install' => 'Webkernel\Core\\Console\\Install\\',
            $basePath . '/Console/Package' => 'Webkernel\Core\\Console\\Package\\',
            $basePath . '/Console/Platform' => 'Webkernel\Core\\Console\\Platform\\',
        ];
    }

    protected function getCommandDirectories(string $basePath, string $namespace): array
    {
        $paths = [
            'Console',
            'Console/Commands',
            'Console/Install',
            'Console/Package',
            'Console/Platform',
        ];

        $directories = [];

        foreach ($paths as $pathSuffix) {
            $fullPath = $basePath . '/' . $pathSuffix;

            if (File::isDirectory($fullPath)) {
                $nsSuffix = trim(str_replace('/', '\\', $pathSuffix), '\\');
                $fullNamespace = rtrim($namespace, '\\') . '\\' . $nsSuffix . '\\';

                $directories[$fullPath] = $fullNamespace;
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
            // Ignorer Webkernel déjà inclus dans getCoreCommandPaths()
            if ($this->isPlatformNamespace($path)) {
                $namespaces[$namespace] = base_path($path);
            }
        }

        return $namespaces;
    }

    protected function isPlatformNamespace(string $path): bool
    {
        return str_starts_with($path, 'platform/')
            || (str_starts_with($path, 'packages/') && $path !== 'packages/webkernel/src/');
    }

    protected function isValidCommand(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        try {
            $reflection = new \ReflectionClass($class);

            return $reflection->isSubclassOf(Command::class)
                && !$reflection->isAbstract();
        } catch (\ReflectionException) {
            return false;
        }
    }
}
