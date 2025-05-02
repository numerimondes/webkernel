<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Support\Str;

class WebkernelLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Register views path
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'webkernel');

        // 2. Auto-discover components from Webkernel\Livewire
        $this->registerLivewireComponentsFrom(
            __DIR__ . '/../Livewire',
            'Webkernel\\Livewire'
        );

        // 3. Auto-discover components from Webkernel\Http\Livewire
        $this->registerLivewireComponentsFrom(
            __DIR__ . '/../Http/Livewire',
            'Webkernel\\Http\\Livewire'
        );
    }

protected function registerLivewireComponentsFrom(string $path, string $namespace): void
{
    if (!is_dir($path)) {
        return;
    }

    foreach (glob($path . '/*.php') as $file) {
        $class = $namespace . '\\' . basename($file, '.php');

        if (class_exists($class)) {
            // correct alias
            $alias = Str::kebab(class_basename($class));

            // Register component with alias name
            Livewire::component("webkernel::{$alias}", $class);
        }
    }
}
// WebkernelLivewireServiceProvider.php

    public function register(): void
    {
        //
    }
}
