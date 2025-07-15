<?php
namespace Webkernel\Core\Services;

use Filament\Facades\Filament;
use App\Models\User;
use Webkernel\Core\Models\UserPanels;

class PanelsInfoCollector
{
    /**
     * Summary of getAllPanelsInfo
     * @example {{ dd(\Webkernel\Core\Services\PanelsInfoCollector::getAllPanelsInfo()) }}
     * @example {{ dd(\Webkernel\Core\Services\PanelsInfoCollector::debugStructure()) }}
     * @return array
     */
    public static function getAllPanelsInfo(): array
    {
        $panels = Filament::getPanels();
        $infos = [];
        $providersMap = [];
        
        // Collecte tous les providers et leurs panels
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'Filament\PanelProvider')) {
                try {
                    $reflection = new \ReflectionClass($class);
                    $constructor = $reflection->getConstructor();
                    
                    if ($constructor && $constructor->getNumberOfRequiredParameters() > 0) {
                        continue;
                    }
                    
                    $instance = new $class();
                    $panel = $instance->panel(new \Filament\Panel());
                    $providersMap[$panel->getId()] = $class;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        
        // Collecte des infos depuis les méthodes webkernelPanelInfo
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'Filament\PanelProvider') && method_exists($class, 'webkernelPanelInfo')) {
                $panelInfo = $class::webkernelPanelInfo();
                $panelInfo['source'] = self::analyzeClassStructure($class);
                $infos[] = $panelInfo;
            }
        }
        
        // Collecte des panels par défaut
        foreach ($panels as $panel) {
            if (!collect($infos)->where('id', $panel->getId())->count()) {
                $defaultInfo = [
                    'id' => $panel->getId(),
                    'path' => $panel->getPath(),
                ];
                
                $allowedMethods = ['getName', 'getUrl', 'getDomain', 'getColors', 'getFont', 'getFontFamily', 'getFontProvider', 'getFontUrl'];
                
                foreach (get_class_methods($panel) as $method) {
                    if (in_array($method, $allowedMethods)) {
                        try {
                            $value = $panel->$method();
                            if ($value !== null && !is_object($value) && !is_array($value)) {
                                $key = strtolower(substr($method, 3));
                                $defaultInfo[$key] = $value;
                            }
                        } catch (\Exception $e) {
                            // Skip methods that cause errors
                        }
                    }
                }
                
                $panelClass = $providersMap[$panel->getId()] ?? 'Unknown';
                $defaultInfo['source'] = self::analyzeClassStructure($panelClass);
                
                $infos[] = $defaultInfo;
            }
        }
        
        return self::organizePanelsByStructure($infos);
    }
    
    private static function analyzeClassStructure(string $className): array
    {
        if ($className === 'Unknown') {
            return [
                'namespace_root' => 'Unknown',
                'path_segments' => [],
                'structure' => 'unknown',
                'full_path' => $className
            ];
        }
        
        // Découpe le namespace en segments
        $segments = explode('\\', $className);
        $namespaceRoot = $segments[0] ?? 'Unknown';
        
        // Analyse de la structure selon le type de namespace
        $structure = self::determineStructureType($segments);
        
        // Vérification de sécurité
        if (!is_array($structure)) {
            $structure = ['type' => 'simple', 'levels' => []];
        }
        
        return [
            'namespace_root' => $namespaceRoot,
            'path_segments' => $segments,
            'structure' => $structure,
            'full_path' => $className
        ];
    }
    
    private static function determineStructureType(array $segments): array
    {
        $count = count($segments);
        
        if ($count < 2) {
            return [
                'type' => 'simple',
                'levels' => []
            ];
        }
        
        $namespaceRoot = $segments[0];
        $technicalSegments = ['Providers', 'Filament', 'Provider'];
        
        // Structure spécifique pour Webkernel
        if ($namespaceRoot === 'Webkernel') {
            return self::analyzeWebkernelStructure($segments);
        }
        
        // Structure spécifique pour Modules
        if ($namespaceRoot === 'Modules') {
            return self::analyzeModulesStructure($segments);
        }
        
        // Structure pour Numerimondes (qui semble suivre le pattern Modules)
        if ($namespaceRoot === 'Numerimondes' && isset($segments[1]) && $segments[1] === 'Modules') {
            return self::analyzeNumerimondesModulesStructure($segments);
        }
        
        // Structure générique pour les autres namespaces
        return self::analyzeGenericStructure($segments);
    }
    
    private static function analyzeWebkernelStructure(array $segments): array
    {
        // Structure: Webkernel\{Module}\[Providers\]...
        $levels = [];
        
        if (isset($segments[1])) {
            $levels['module'] = $segments[1];
        }
        
        return [
            'type' => 'webkernel',
            'levels' => $levels,
            'depth' => 1
        ];
    }
    
    private static function analyzeModulesStructure(array $segments): array
    {
        // Structure: Modules\{nom_module}\{nom_sous_module}\...
        $levels = [];
        
        if (isset($segments[1])) {
            $levels['module'] = $segments[1];
        }
        
        if (isset($segments[2])) {
            $levels['submodule'] = $segments[2];
        }
        
        return [
            'type' => 'modules',
            'levels' => $levels,
            'depth' => count(array_filter([$levels['module'] ?? null, $levels['submodule'] ?? null]))
        ];
    }
    
    private static function analyzeNumerimondesModulesStructure(array $segments): array
    {
        // Structure: Numerimondes\Modules\{nom_module}\{nom_sous_module}\...
        $levels = [];
        
        if (isset($segments[2])) {
            $levels['module'] = $segments[2];
        }
        
        if (isset($segments[3])) {
            $levels['submodule'] = $segments[3];
        }
        
        return [
            'type' => 'numerimondes_modules',
            'levels' => $levels,
            'depth' => count(array_filter([$levels['module'] ?? null, $levels['submodule'] ?? null]))
        ];
    }
    
    private static function analyzeGenericStructure(array $segments): array
    {
        $levels = [];
        $depth = 0;
        
        foreach ($segments as $index => $segment) {
            if ($index === 0) {
                $levels['root'] = $segment;
                $depth++;
            } elseif ($index === 1) {
                $levels['sub'] = $segment;
                $depth++;
            } elseif ($index === 2) {
                $levels['subsub'] = $segment;
                $depth++;
            }
        }
        
        return [
            'type' => 'generic',
            'levels' => $levels,
            'depth' => $depth
        ];
    }
    
    private static function organizePanelsByStructure(array $panels): array
    {
        $organized = [];
        
        foreach ($panels as $panel) {
            $source = $panel['source'] ?? [];
            $structure = $source['structure'] ?? [];
            $type = $structure['type'] ?? 'unknown';
            $levels = $structure['levels'] ?? [];
            $namespaceRoot = $source['namespace_root'] ?? 'Unknown';
            
            if (!isset($organized[$namespaceRoot])) {
                $organized[$namespaceRoot] = [
                    'name' => $namespaceRoot,
                    'modules' => []
                ];
            }
            
            switch ($type) {
                case 'webkernel':
                    self::organizeWebkernelPanel($organized[$namespaceRoot], $panel, $levels);
                    break;
                case 'modules':
                case 'numerimondes_modules':
                    self::organizeModulesPanel($organized[$namespaceRoot], $panel, $levels);
                    break;
                default:
                    self::organizeGenericPanel($organized[$namespaceRoot], $panel, $levels);
                    break;
            }
        }
        
        return $organized;
    }
    
    private static function organizeWebkernelPanel(array &$namespaceData, array $panel, array $levels): void
    {
        $moduleName = $levels['module'] ?? 'default';
        
        if (!isset($namespaceData['modules'][$moduleName])) {
            $namespaceData['modules'][$moduleName] = [
                'name' => $moduleName,
                'panels' => []
            ];
        }
        
        $namespaceData['modules'][$moduleName]['panels'][] = $panel;
    }
    
    private static function organizeModulesPanel(array &$namespaceData, array $panel, array $levels): void
    {
        $moduleName = $levels['module'] ?? 'default';
        $submoduleName = $levels['submodule'] ?? null;
        
        if (!isset($namespaceData['modules'][$moduleName])) {
            $namespaceData['modules'][$moduleName] = [
                'name' => $moduleName,
                'panels' => [],
                'submodules' => []
            ];
        }
        
        if ($submoduleName) {
            if (!isset($namespaceData['modules'][$moduleName]['submodules'][$submoduleName])) {
                $namespaceData['modules'][$moduleName]['submodules'][$submoduleName] = [
                    'name' => $submoduleName,
                    'panels' => []
                ];
            }
            $namespaceData['modules'][$moduleName]['submodules'][$submoduleName]['panels'][] = $panel;
        } else {
            $namespaceData['modules'][$moduleName]['panels'][] = $panel;
        }
    }
    
    private static function organizeGenericPanel(array &$namespaceData, array $panel, array $levels): void
    {
        $moduleName = $levels['sub'] ?? 'default';
        
        if (!isset($namespaceData['modules'][$moduleName])) {
            $namespaceData['modules'][$moduleName] = [
                'name' => $moduleName,
                'panels' => []
            ];
        }
        
        $namespaceData['modules'][$moduleName]['panels'][] = $panel;
    }
    
    /**
     * Méthode de debug pour analyser la structure
     */
    public static function debugStructure(): array
    {
        $panels = self::getAllPanelsInfo();
        $debug = [];
        
        foreach ($panels as $namespace => $namespaceData) {
            $debug[$namespace] = [
                'name' => $namespaceData['name'],
                'modules_count' => count($namespaceData['modules']),
                'modules' => array_keys($namespaceData['modules'])
            ];
        }
        
        return $debug;
    }
    
    /**
     * Retourne une liste plate de tous les panels
     */
    public static function getFlatPanelsList(): array
    {
        $allPanels = self::getAllPanelsInfo();
        $flatList = [];
        
        foreach ($allPanels as $namespace => $namespaceData) {
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        $flatList[] = $panel;
                    }
                }
                
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                        foreach ($submoduleData['panels'] as $panel) {
                            $flatList[] = $panel;
                        }
                    }
                }
            }
        }
        
        return $flatList;
    }
    
    /**
     * Retourne les panels organisés par type d'accès (public/restricted)
     */
    public static function getPanelsByAccess(): array
    {
        $allPanels = self::getFlatPanelsList();
        $panelsByAccess = [
            'public' => [],
            'restricted' => []
        ];
        
        foreach ($allPanels as $panel) {
            $panelId = $panel['id'] ?? 'unknown';
            $isRestricted = $panel['restricted'] ?? false;
            
            if ($isRestricted) {
                $panelsByAccess['restricted'][$panelId] = $panel;
            } else {
                $panelsByAccess['public'][$panelId] = $panel;
            }
        }
        
        return $panelsByAccess;
    }
    
    /**
     * Vérifie si un utilisateur a accès à un panel spécifique
     */
    public static function userHasPanelAccess(User $user, string $panelId): bool
    {
        $panelsByAccess = self::getPanelsByAccess();
        
        // Si le panel est public, accès autorisé
        if (isset($panelsByAccess['public'][$panelId])) {
            return true;
        }
        
        // Si le panel est restreint, vérifier la whitelist
        if (isset($panelsByAccess['restricted'][$panelId])) {
            $userPanels = UserPanels::where('user_id', $user->id)->first();
            if (!$userPanels || !$userPanels->panels) {
                return false;
            }
            return isset($userPanels->panels[$panelId]);
        }
        
        return false;
    }

    /**
     * Utilise ModuleAccessHelper::getAllModules pour lister tous les modules réels,
     * puis scanne Filament/Resources pour chaque module/sous-module et retourne les infos resources/policies/actions.
     * @return array
     */
    public static function getAllPanelsResourcesPolicies(): array
    {
        $result = [];
        $modules = \Webkernel\Core\Helpers\Modules\ModuleAccessHelper::getAllModules();
        foreach ($modules as $namespace => $namespaceData) {
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                // Scan panels au niveau module
                $modulePath = self::guessModulePath($namespace, $moduleName);
                if ($modulePath) {
                    $resources = self::scanResourcesInPath($modulePath);
                    if ($resources) {
                        $result[$namespace . '/' . $moduleName] = $resources;
                    }
                }
                // Scan panels dans les sous-modules
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                        $submodulePath = self::guessModulePath($namespace, $moduleName, $submoduleName);
                        if ($submodulePath) {
                            $resources = self::scanResourcesInPath($submodulePath);
                            if ($resources) {
                                $result[$namespace . '/' . $moduleName . '/' . $submoduleName] = $resources;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Devine le chemin du module ou sous-module à partir du namespace et des noms
     */
    private static function guessModulePath($namespace, $moduleName, $submoduleName = null)
    {
        // Heuristique simple pour platform/Modules et packages/webkernel/src
        $base1 = base_path('platform/Modules/' . $moduleName . ($submoduleName ? '/' . $submoduleName : ''));
        $base2 = base_path('packages/webkernel/src/' . $moduleName . ($submoduleName ? '/' . $submoduleName : ''));
        if (is_dir($base1)) return $base1;
        if (is_dir($base2)) return $base2;
        return null;
    }

    /**
     * Scanne le dossier Filament/Resources d'un module et retourne les infos sur les resources
     */
    private static function scanResourcesInPath($modulePath)
    {
        $resourcesDir = $modulePath . '/Filament/Resources';
        if (!is_dir($resourcesDir)) return [];
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($resourcesDir));
        $found = [];
        foreach ($rii as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') continue;
            $relative = ltrim(str_replace($modulePath . '/', '', $file->getPathname()), DIRECTORY_SEPARATOR);
            $classNamespace = str_replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''], $relative);
            // Namespace heuristique : Numerimondes\Modules\ReamMar\Core\Filament\Resources\...
            $parts = explode('/', str_replace(base_path() . '/', '', $modulePath));
            $namespacePrefix = str_replace('/', '\\', $parts[0] ?? '') . (isset($parts[1]) ? '\\' . $parts[1] : '') . (isset($parts[2]) ? '\\' . $parts[2] : '') . (isset($parts[3]) ? '\\' . $parts[3] : '');
            $fullClassName = $namespacePrefix . '\\Filament\\Resources\\' . str_replace('/', '\\', substr($relative, 0, -4));
            $fullClassName = str_replace('\\\\', '\\', $fullClassName);
            if (!class_exists($fullClassName)) continue;
            if (!is_subclass_of($fullClassName, \Filament\Resources\Resource::class)) continue;
            $model = null;
            $policy = null;
            $actions = [];
            if (property_exists($fullClassName, 'model') || method_exists($fullClassName, 'getModel')) {
                $model = method_exists($fullClassName, 'getModel')
                    ? $fullClassName::getModel()
                    : (property_exists($fullClassName, 'model') ? $fullClassName::$model : null);
            }
            if ($model && class_exists($model)) {
                $modelParts = explode('\\', $model);
                $modelName = array_pop($modelParts);
                $policyGuess = implode('\\', array_merge($modelParts, ['Policies', $modelName . 'Policy']));
                if (class_exists($policyGuess)) {
                    $policy = $policyGuess;
                } else {
                    $policyGuess = 'App\\Policies\\' . $modelName . 'Policy';
                    if (class_exists($policyGuess)) {
                        $policy = $policyGuess;
                    }
                }
            }
            if ($policy && class_exists($policy)) {
                $reflection = new \ReflectionClass($policy);
                foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if ($method->class === $policy && !$method->isStatic() && !$method->isConstructor()) {
                        $actions[] = $method->getName();
                    }
                }
            }
            $found[] = [
                'resource' => $fullClassName,
                'model' => $model,
                'policy' => $policy,
                'actions' => $actions,
            ];
        }
        return $found;
    }

} 