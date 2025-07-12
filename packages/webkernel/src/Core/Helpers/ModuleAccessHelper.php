<?php

namespace Webkernel\Core\Helpers;

use App\Models\User;
use Webkernel\Services\Panels\PanelsInfoCollector;

class ModuleAccessHelper
{
    /**
     * Vérifie si un utilisateur a accès à un module
     */
    public static function userHasModuleAccess(User $user, string $moduleId): bool
    {
        return PanelsInfoCollector::userHasPanelAccess($user, $moduleId);
    }

    /**
     * Retourne tous les modules accessibles pour un utilisateur
     */
    public static function getUserAccessibleModules(User $user): array
    {
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        $accessibleModules = [];

        // Ajouter tous les modules publics
        foreach ($panelsByAccess['public'] as $moduleId => $module) {
            $accessibleModules[$moduleId] = $module;
        }

        // Ajouter les modules restricted auxquels l'utilisateur a accès
        $userPanels = \Webkernel\Core\Models\UserPanels::where('user_id', $user->id)->first();
        if ($userPanels && $userPanels->panels) {
            foreach ($userPanels->panels as $moduleId => $access) {
                if (isset($panelsByAccess['restricted'][$moduleId])) {
                    $accessibleModules[$moduleId] = $panelsByAccess['restricted'][$moduleId];
                }
            }
        }

        return $accessibleModules;
    }

    /**
     * Retourne les modules restricted pour un utilisateur
     */
    public static function getUserRestrictedModules(User $user): array
    {
        $userPanels = \Webkernel\Core\Models\UserPanels::where('user_id', $user->id)->first();
        if (!$userPanels || !$userPanels->panels) {
            return [];
        }

        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        $restrictedModules = [];

        foreach ($userPanels->panels as $moduleId => $access) {
            if (isset($panelsByAccess['restricted'][$moduleId])) {
                $restrictedModules[$moduleId] = $panelsByAccess['restricted'][$moduleId];
            }
        }

        return $restrictedModules;
    }

    /**
     * Retourne les modules publics
     */
    public static function getPublicModules(): array
    {
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        return $panelsByAccess['public'] ?? [];
    }

    /**
     * Retourne tous les modules restricted
     */
    public static function getRestrictedModules(): array
    {
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        return $panelsByAccess['restricted'] ?? [];
    }

    /**
     * Vérifie si un module est public
     */
    public static function isModulePublic(string $moduleId): bool
    {
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        return isset($panelsByAccess['public'][$moduleId]);
    }

    /**
     * Vérifie si un module est restricted
     */
    public static function isModuleRestricted(string $moduleId): bool
    {
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        return isset($panelsByAccess['restricted'][$moduleId]);
    }
} 