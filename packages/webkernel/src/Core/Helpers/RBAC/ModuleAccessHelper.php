<?php

namespace Webkernel\Core\Helpers\Modules;
use App\Models\User;
use Webkernel\Core\Services\PanelsInfoCollector;
use Webkernel\Core\Services\PanelAccessService;
use Illuminate\Support\Facades\Gate;
use ReflectionClass;
use ReflectionMethod;
use Exception;

class ModuleAccessHelper
{
    public static function getAccessibleModules(User $user): array
    {
        $allPanels = PanelsInfoCollector::getAllPanelsInfo();
        $accessibleModules = [];
        $accessService = new PanelAccessService();
        
        foreach ($allPanels as $namespace => $namespaceData) {
            $accessibleModules[$namespace] = [
                'name' => $namespace,
                'modules' => []
            ];
            
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                $accessibleModules[$namespace]['modules'][$moduleName] = [
                    'name' => $moduleName,
                    'panels' => [],
                    'submodules' => []
                ];
                
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        if ($accessService->canAccessPanel($user, $panel)) {
                            $accessibleModules[$namespace]['modules'][$moduleName]['panels'][] = $panel;
                        }
                    }
                }
                
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                        $accessibleModules[$namespace]['modules'][$moduleName]['submodules'][$submoduleName] = [
                            'name' => $submoduleName,
                            'panels' => []
                        ];
                        
                        foreach ($submoduleData['panels'] as $panel) {
                            if ($accessService->canAccessPanel($user, $panel)) {
                                $accessibleModules[$namespace]['modules'][$moduleName]['submodules'][$submoduleName]['panels'][] = $panel;
                            }
                        }
                    }
                }
            }
        }
        
        return $accessibleModules;
    }
    
    public static function getAllModules(): array
    {
        $allPanels = PanelsInfoCollector::getAllPanelsInfo();
        $allModules = [];
        
        foreach ($allPanels as $namespace => $namespaceData) {
            $allModules[$namespace] = [
                'name' => $namespace,
                'modules' => []
            ];
            
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                $allModules[$namespace]['modules'][$moduleName] = [
                    'name' => $moduleName,
                    'panels' => [],
                    'submodules' => []
                ];
                
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        $allModules[$namespace]['modules'][$moduleName]['panels'][] = $panel;
                    }
                }
                
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                        $allModules[$namespace]['modules'][$moduleName]['submodules'][$submoduleName] = [
                            'name' => $submoduleName,
                            'panels' => []
                        ];
                        
                        foreach ($submoduleData['panels'] as $panel) {
                            $allModules[$namespace]['modules'][$moduleName]['submodules'][$submoduleName]['panels'][] = $panel;
                        }
                    }
                }
            }
        }
        
        return $allModules;
    }
    
    private static function canAccessPanel(User $user, array $panel): bool
    {
        $panelId = $panel['id'] ?? 'unknown';
        $isRestricted = $panel['restricted'] ?? false;
        $accessService = new PanelAccessService();
        
        if (!$isRestricted) {
            return true;
        }
        
        return $accessService->canAccessPanel($user, $panelId);
    }
    
    public static function getAccessiblePanelsList(User $user): array
    {
        $accessibleModules = self::getAccessibleModules($user);
        $panels = [];
        
        foreach ($accessibleModules as $namespace => $namespaceData) {
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        $panels[] = $panel;
                    }
                }
                
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                        foreach ($submoduleData['panels'] as $panel) {
                            $panels[] = $panel;
                        }
                    }
                }
            }
        }
        
        return $panels;
    }
    
    public static function getEachModuleFilamentResourcesPolicies(): array
    {
        $allPanels = PanelsInfoCollector::getAllPanelsInfo();
        $modulesPolicies = [];
        
        foreach ($allPanels as $namespace => $namespaceData) {
            $modulesPolicies[$namespace] = [
                'name' => $namespace,
                'modules' => []
            ];
            
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                $modulesPolicies[$namespace]['modules'][$moduleName] = [
                    'name' => $moduleName,
                    'resources' => []
                ];
                
                $resources = self::getRegisteredFilamentResources($namespace, $moduleName);
                
                foreach ($resources as $resourceClass) {
                    $resourceInfo = self::analyzeFilamentResource($resourceClass);
                    if ($resourceInfo) {
                        $modulesPolicies[$namespace]['modules'][$moduleName]['resources'][] = $resourceInfo;
                    }
                }
            }
        }
        
        return $modulesPolicies;
    }
    
    private static function getRegisteredFilamentResources(string $namespace, string $moduleName): array
    {
        $resources = [];
        
        try {
            $panels = \Filament\Facades\Filament::getPanels();
            
            foreach ($panels as $panel) {
                $panelResources = $panel->getResources();
                
                foreach ($panelResources as $resourceClass) {
                    if (str_contains($resourceClass, $namespace) && str_contains($resourceClass, $moduleName)) {
                        $resources[] = $resourceClass;
                    }
                }
            }
            
            return array_unique($resources);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    private static function analyzeFilamentResource(string $resourceClass): ?array
    {
        try {
            if (!class_exists($resourceClass)) {
                return null;
            }
            
            $model = $resourceClass::getModel();
            
            if (!$model || !class_exists($model)) {
                return null;
            }
            
            $policyInfo = self::analyzePolicyForModel($model);
            
            return [
                'resource' => $resourceClass,
                'model' => $model,
                'policy' => $policyInfo
            ];
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    private static function analyzePolicyForModel(string $modelClass): ?array
    {
        try {
            $policyClass = self::findPolicyClass($modelClass);
            
            if (!$policyClass || !class_exists($policyClass)) {
                return null;
            }
            
            $reflection = new ReflectionClass($policyClass);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            $permissions = [];
            
            foreach ($methods as $method) {
                $methodName = $method->getName();
                
                if (str_starts_with($methodName, '__') || 
                    $method->getDeclaringClass()->getName() !== $policyClass) {
                    continue;
                }
                
                $parameters = $method->getParameters();
                
                $permissions[] = [
                    'name' => $methodName,
                    'requires_model' => count($parameters) > 1,
                    'parameters' => array_map(fn($param) => [
                        'name' => $param->getName(),
                        'type' => $param->getType() ? $param->getType()->getName() : 'mixed'
                    ], $parameters)
                ];
            }
            
            return [
                'class' => $policyClass,
                'permissions' => $permissions
            ];
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    private static function findPolicyClass(string $modelClass): ?string
    {
        $gate = Gate::getFacadeRoot();
        $policies = $gate->policies();
        
        $registeredPolicy = $policies[$modelClass] ?? null;
        if ($registeredPolicy && class_exists($registeredPolicy)) {
            return $registeredPolicy;
        }
        
        $modelBaseName = class_basename($modelClass);
        $policyName = $modelBaseName . 'Policy';
        
        $possiblePaths = self::generatePolicyPaths($modelClass, $policyName);
        
        foreach ($possiblePaths as $path) {
            if (class_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }
    
    private static function generatePolicyPaths(string $modelClass, string $policyName): array
    {
        $paths = [];
        $parts = explode('\\', $modelClass);
        
        $paths[] = str_replace('Models', 'Policies', dirname($modelClass)) . '\\' . $policyName;
        $paths[] = str_replace('Core\\Models', 'Core\\Policies', dirname($modelClass)) . '\\' . $policyName;
        
        $namespaceIndex = 0;
        $moduleIndex = array_search('Modules', $parts);
        
        if ($moduleIndex !== false) {
            $namespace = $parts[$namespaceIndex];
            $module = $parts[$moduleIndex + 1];
            
            $paths[] = "Platform\\Modules\\{$module}\\Core\\Policies\\{$policyName}";
            $paths[] = "{$namespace}\\Modules\\{$module}\\Core\\Policies\\{$policyName}";
            $paths[] = "{$namespace}\\Modules\\{$module}\\Policies\\{$policyName}";
        }
        
        return array_unique($paths);
    }
    
    public static function getModuleResourcesWithPolicies(string $namespace, string $moduleName): array
    {
        $allModules = self::getEachModuleFilamentResourcesPolicies();
        
        if (!isset($allModules[$namespace]['modules'][$moduleName])) {
            return [];
        }
        
        return $allModules[$namespace]['modules'][$moduleName]['resources'];
    }
    
    public static function getAllPoliciesPermissions(): array
    {
        $modulesPolicies = self::getEachModuleFilamentResourcesPolicies();
        $allPermissions = [];
        
        foreach ($modulesPolicies as $namespace => $namespaceData) {
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                foreach ($moduleData['resources'] as $resource) {
                    if ($resource['policy']) {
                        $key = $namespace . '.' . $moduleName . '.' . class_basename($resource['model']);
                        $allPermissions[$key] = [
                            'namespace' => $namespace,
                            'module' => $moduleName,
                            'model' => $resource['model'],
                            'policy' => $resource['policy']['class'],
                            'permissions' => $resource['policy']['permissions']
                        ];
                    }
                }
            }
        }
        
        return $allPermissions;
    }
}