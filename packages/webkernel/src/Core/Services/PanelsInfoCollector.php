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
} 