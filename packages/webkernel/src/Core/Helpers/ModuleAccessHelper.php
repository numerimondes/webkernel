<?php

namespace Webkernel\Core\Helpers;

use App\Models\User;
use Webkernel\Services\Panels\PanelsInfoCollector;

class ModuleAccessHelper
{
    /**
     * Retourne la liste finale des modules et panels accessibles à l'utilisateur
     * Combine les panels publics + les panels assignés à l'utilisateur
     */
    public static function getAccessibleModules(User $user): array
    {
        $allPanels = PanelsInfoCollector::getAllPanelsInfo();
        $accessibleModules = [];
        
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
                
                // Vérifier les panels au niveau module
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        if (self::canAccessPanel($user, $panel)) {
                            $accessibleModules[$namespace]['modules'][$moduleName]['panels'][] = $panel;
                        }
                    }
                }
                
                // Vérifier les panels dans les sous-modules
                if (isset($moduleData['submodules'])) {
                    foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                        $accessibleModules[$namespace]['modules'][$moduleName]['submodules'][$submoduleName] = [
                            'name' => $submoduleName,
                            'panels' => []
                        ];
                        
                        foreach ($submoduleData['panels'] as $panel) {
                            if (self::canAccessPanel($user, $panel)) {
                                $accessibleModules[$namespace]['modules'][$moduleName]['submodules'][$submoduleName]['panels'][] = $panel;
                            }
                        }
                    }
                }
            }
        }
        
        return $accessibleModules;
    }
    
    /**
     * Vérifie si l'utilisateur peut accéder à un panel spécifique
     */
    private static function canAccessPanel(User $user, array $panel): bool
    {
        $panelId = $panel['id'] ?? 'unknown';
        $isRestricted = $panel['restricted'] ?? false;
        
        // Si le panel est public, accès autorisé
        if (!$isRestricted) {
            return true;
        }
        
        // Si le panel est restricted, vérifier les accès utilisateur
        return $user->canAccessPanel($panelId);
    }
    
    /**
     * Retourne une liste plate des panels accessibles
     */
    public static function getAccessiblePanelsList(User $user): array
    {
        $accessibleModules = self::getAccessibleModules($user);
        $panels = [];
        
        foreach ($accessibleModules as $namespace => $namespaceData) {
            foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                // Panels au niveau module
                if (isset($moduleData['panels'])) {
                    foreach ($moduleData['panels'] as $panel) {
                        $panels[] = $panel;
                    }
                }
                
                // Panels dans les sous-modules
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
} 