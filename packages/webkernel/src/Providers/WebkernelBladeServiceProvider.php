<?php

namespace Webkernel\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class WebkernelBladeServiceProvider extends ServiceProvider
{
    /**
     * Register additional services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('webkernel', fn($expression) => "<?php echo webkernel_include({$expression}); ?>");

        // Register Blade directives from platform and packages
        $bladePaths = $this->getBladePaths();
        foreach ($bladePaths as $path => $namespace) {
            $directiveFile = $path . '/Directives.php';
            if (File::exists($directiveFile)) {
                $directiveClass = $namespace . 'Blade\\Directives';
                if (class_exists($directiveClass) && method_exists($directiveClass, 'register')) {
                    $directiveClass::register();
                }
            }
        }
    }

    /**
     * Get Blade directive paths and their namespaces
     *
     * @return array
     */
    protected function getBladePaths(): array
    {
        $paths = [];
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $bladePath = $path . '/Blade';
            if (File::isDirectory($bladePath)) {
                $paths[$bladePath] = $namespace;
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
