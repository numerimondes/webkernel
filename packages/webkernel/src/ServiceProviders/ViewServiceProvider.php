<?php
namespace Webkernel\ServiceProviders;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Initialize the service provider.
     */
    public function boot(): void
    {
        $this->loadViewsFromPackage();
        $this->loadViewsFromModules();
    }
    
    /**
     * Load views from the Webkernel package and platform/packages.
     */
    protected function loadViewsFromPackage(): void
    {
        // Load Webkernel views
        $this->loadViewsFrom(base_path('packages/webkernel/src/Core/Resources/Views'), 'webkernel');
        view()->addNamespace('webkernel', base_path('packages/webkernel/src/Core/Resources/Views'));
        
        // Load views from platform and other packages
        $autoloadNamespaces = $this->getAutoloadNamespaces();
        foreach ($autoloadNamespaces as $namespace => $path) {
            $viewPath = $path . '/Resources/Views';
            if (File::isDirectory($viewPath)) {
                $viewNamespace = strtolower(str_replace('\\', '.', $namespace));
                $this->loadViewsFrom($viewPath, $viewNamespace);
                view()->addNamespace($viewNamespace, $viewPath);
            }
        }
    }
    
    /**
     * Load views from platform modules structure
     * platform/Modules/{nom_module}/{nom_sous_module}/Resources/Views
     */
    protected function loadViewsFromModules(): void
    {
        $modulesPath = base_path('platform/Modules');
        
        if (!File::isDirectory($modulesPath)) {
            return;
        }
        
        // Parcourir tous les modules
        $modules = File::directories($modulesPath);
        
        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            
            // Parcourir tous les sous-modules dans chaque module
            $subModules = File::directories($modulePath);
            
            foreach ($subModules as $subModulePath) {
                $subModuleName = basename($subModulePath);
                $viewPath = $subModulePath . '/Resources/Views';
                
                if (File::isDirectory($viewPath)) {
                    // Créer le namespace: module.submodule (ex: reammar.core)
                    $viewNamespace = strtolower($moduleName . '.' . $subModuleName);
                    
                    $this->loadViewsFrom($viewPath, $viewNamespace);
                    view()->addNamespace($viewNamespace, $viewPath);
                }
            }
        }
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