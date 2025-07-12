<?php
namespace Webkernel\Services\Panels;

use Filament\Facades\Filament;
use App\Models\User;
use Webkernel\Core\Models\UserPanels;

class PanelsInfoCollector
{
    /**
     * Summary of getAllPanelsInfo
     * @example {{ dd(\Webkernel\Services\Panels\PanelsInfoCollector::getAllPanelsInfo()) }}
     * @example {{ dd(\Webkernel\Services\Panels\PanelsInfoCollector::debugStructure()) }}
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
        // Structure générique
        $technicalSegments = ['Providers', 'Filament', 'Provider'];
        $meaningfulSegments = [];
        
        // Ignore le namespace root et les segments techniques
        for ($i = 1; $i < count($segments); $i++) {
            $segment = $segments[$i];
            
            if (in_array($segment, $technicalSegments) && $i >= count($segments) - 3) {
                continue;
            }
            
            $meaningfulSegments[] = $segment;
        }
        
        $levels = [];
        if (isset($meaningfulSegments[0])) {
            $levels['module'] = $meaningfulSegments[0];
        }
        if (isset($meaningfulSegments[1])) {
            $levels['submodule'] = $meaningfulSegments[1];
        }
        
        return [
            'type' => 'generic',
            'levels' => $levels,
            'depth' => count($meaningfulSegments)
        ];
    }
    
    private static function organizePanelsByStructure(array $panels): array
    {
        $organized = [];
        
        foreach ($panels as $panel) {
            $source = $panel['source'] ?? [];
            
            if (!is_array($source)) {
                continue;
            }
            
            $namespaceRoot = $source['namespace_root'] ?? 'Unknown';
            $structure = $source['structure'] ?? ['type' => 'simple', 'levels' => []];
            
            if (!is_array($structure)) {
                $structure = ['type' => 'simple', 'levels' => []];
            }
            
            // Crée la section du namespace si elle n'existe pas
            if (!isset($organized[$namespaceRoot])) {
                $organized[$namespaceRoot] = [
                    'namespace' => $namespaceRoot,
                    'modules' => []
                ];
            }
            
            $levels = $structure['levels'] ?? [];
            $structureType = $structure['type'] ?? 'simple';
            
            // Organisation selon le type de structure
            switch ($structureType) {
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
        $module = $levels['module'] ?? 'Core';
        
        if (!isset($namespaceData['modules'][$module])) {
            $namespaceData['modules'][$module] = [
                'name' => $module,
                'panels' => []
            ];
        }
        
        $namespaceData['modules'][$module]['panels'][] = $panel;
    }
    
    private static function organizeModulesPanel(array &$namespaceData, array $panel, array $levels): void
    {
        $module = $levels['module'] ?? 'Unknown';
        $submodule = $levels['submodule'] ?? null;
        
        if (!isset($namespaceData['modules'][$module])) {
            $namespaceData['modules'][$module] = [
                'name' => $module,
                'panels' => [],
                'submodules' => []
            ];
        }
        
        if ($submodule) {
            if (!isset($namespaceData['modules'][$module]['submodules'][$submodule])) {
                $namespaceData['modules'][$module]['submodules'][$submodule] = [
                    'name' => $submodule,
                    'panels' => []
                ];
            }
            
            $namespaceData['modules'][$module]['submodules'][$submodule]['panels'][] = $panel;
        } else {
            $namespaceData['modules'][$module]['panels'][] = $panel;
        }
    }
    
    private static function organizeGenericPanel(array &$namespaceData, array $panel, array $levels): void
    {
        $module = $levels['module'] ?? 'Misc';
        $submodule = $levels['submodule'] ?? null;
        
        if (!isset($namespaceData['modules'][$module])) {
            $namespaceData['modules'][$module] = [
                'name' => $module,
                'panels' => [],
                'submodules' => []
            ];
        }
        
        if ($submodule) {
            if (!isset($namespaceData['modules'][$module]['submodules'][$submodule])) {
                $namespaceData['modules'][$module]['submodules'][$submodule] = [
                    'name' => $submodule,
                    'panels' => []
                ];
            }
            
            $namespaceData['modules'][$module]['submodules'][$submodule]['panels'][] = $panel;
        } else {
            $namespaceData['modules'][$module]['panels'][] = $panel;
        }
    }
    
    /**
     * Méthode de debug pour voir la structure détectée
     */
    public static function debugStructure(): array
    {
        $debug = [];
        
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'Filament\PanelProvider')) {
                $debug[] = [
                    'class' => $class,
                    'analysis' => self::analyzeClassStructure($class)
                ];
            }
        }
        
        return $debug;
    }
    
    /**
     * Méthode utilitaire pour obtenir une liste plate des panels
     */
    public static function getFlatPanelsList(): array
    {
        $organized = self::getAllPanelsInfo();
        $flat = [];
        
        foreach ($organized as $namespace => $namespaceData) {
            foreach ($namespaceData['modules'] as $module => $moduleData) {
                // Panels au niveau module
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        $flat[] = $panel;
                    }
                }
                
                // Panels dans les sous-modules
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submodule => $submoduleData) {
                        foreach ($submoduleData['panels'] as $panel) {
                            $flat[] = $panel;
                        }
                    }
                }
            }
        }
        
        return $flat;
    }

    /**
     * Retourne les panneaux séparés en restricted et public
     */
    public static function getPanelsByAccess(): array
    {
        $organized = self::getAllPanelsInfo();
        $restricted = [];
        $public = [];
        
        foreach ($organized as $namespace => $namespaceData) {
            foreach ($namespaceData['modules'] as $module => $moduleData) {
                // Panels au niveau module
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        $panelId = $panel['id'] ?? 'unknown';
                        $isRestricted = $panel['restricted'] ?? false;
                        
                        if ($isRestricted) {
                            $restricted[$panelId] = $panel;
                        } else {
                            $public[$panelId] = $panel;
                        }
                    }
                }
                
                // Panels dans les sous-modules
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submodule => $submoduleData) {
                        foreach ($submoduleData['panels'] as $panel) {
                            $panelId = $panel['id'] ?? 'unknown';
                            $isRestricted = $panel['restricted'] ?? false;
                            
                            if ($isRestricted) {
                                $restricted[$panelId] = $panel;
                            } else {
                                $public[$panelId] = $panel;
                            }
                        }
                    }
                }
            }
        }
        
        return [
            'restricted' => $restricted,
            'public' => $public
        ];
    }

    /**
     * Vérifie si un utilisateur a accès à un panneau
     */
    public static function userHasPanelAccess(User $user, string $panelId): bool
    {
        // Vérifier d'abord si le panneau est public
        $panelsByAccess = self::getPanelsByAccess();
        if (isset($panelsByAccess['public'][$panelId])) {
            return true;
        }
        
        // Si le panneau est restricted, vérifier les accès utilisateur
        $userPanels = UserPanels::where('user_id', $user->id)->first();
        if (!$userPanels || !$userPanels->panels) {
            return false;
        }
        
        return isset($userPanels->panels[$panelId]);
    }
}