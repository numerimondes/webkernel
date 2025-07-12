<?php
namespace Webkernel\Core\Services;

use Webkernel\Core\Models\PlatformOwner;
use Webkernel\Core\Models\UserPanels;
use Webkernel\Core\Services\PanelsInfoCollector;
use Illuminate\Contracts\Auth\Authenticatable;

class PanelAccessService
{
    /**
     * Vérifie si l'utilisateur peut accéder à un panel spécifique
     */
    public function canAccessPanel(Authenticatable $user, string $panelId): bool
    {
        // Vérification spéciale pour le panel "system" - réservé aux super admins/owners
        if ($panelId === 'system') {
            return $this->isSuperAdmin($user);
        }
        
        // 1. Vérifier si l'utilisateur est super admin (is_eternal_owner)
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        // 2. Vérifier si le panel est explicitement public
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        if (isset($panelsByAccess['public'][$panelId])) {
            return true;
        }
        // 3. Pour les panels restricted (ou non trouvés), vérifier les permissions explicites
        $hasExplicitAccess = $this->userHasExplicitPanelAccess($user, $panelId, $panelsByAccess);
        return $hasExplicitAccess;
    }

    /**
     * Vérifie si l'utilisateur est super admin (is_eternal_owner)
     */
    public function isSuperAdmin(Authenticatable $user): bool
    {
        $platformOwner = PlatformOwner::where('user_id', $user->id)
            ->where('is_eternal_owner', true)
            ->first();
        if ($platformOwner) {
            $now = now();
            if ($platformOwner->when && $now->lt($platformOwner->when)) return false;
            if ($platformOwner->until && $now->gt($platformOwner->until)) return false;
            return true;
        }
        return false;
    }

    /**
     * Vérifie si l'utilisateur a explicitement accès à un panel restreint
     */
    public function userHasExplicitPanelAccess(Authenticatable $user, string $panelId, ?array $panelsByAccess = null): bool
    {
        $panelsByAccess = $panelsByAccess ?? PanelsInfoCollector::getPanelsByAccess();
        $isRestricted = isset($panelsByAccess['restricted'][$panelId]);
        if ($isRestricted) {
            $userPanels = UserPanels::where('user_id', $user->id)->first();
            if (!$userPanels || !$userPanels->panels) {
                return false;
            }
            return isset($userPanels->panels[$panelId]);
        }
        // Si le panel n'est pas restreint, accès autorisé
        return true;
    }

    /**
     * Retourne la liste des panels accessibles à l'utilisateur
     */
    public function getAccessiblePanels(Authenticatable $user): array
    {
        $accessiblePanels = [];
        if ($this->isSuperAdmin($user)) {
            $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
            return array_merge($panelsByAccess['public'] ?? [], $panelsByAccess['restricted'] ?? []);
        }
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        $accessiblePanels = array_merge($accessiblePanels, $panelsByAccess['public'] ?? []);
        $userPanels = UserPanels::where('user_id', $user->id)->first();
        if ($userPanels && $userPanels->panels) {
            foreach ($userPanels->panels as $panelId => $hasAccess) {
                if ($hasAccess && isset($panelsByAccess['restricted'][$panelId])) {
                    $accessiblePanels[$panelId] = $panelsByAccess['restricted'][$panelId];
                }
            }
        }
        return $accessiblePanels;
    }

    /**
     * Retourne UNIQUEMENT les panels auxquels l'utilisateur a le droit d'accéder
     */
    public function getUserAccessiblePanels(Authenticatable $user): array
    {
        $accessiblePanels = [];
        
        // Récupérer tous les panels disponibles
        $panelsByAccess = PanelsInfoCollector::getPanelsByAccess();
        
        // Vérifier chaque panel individuellement
        foreach ($panelsByAccess['public'] ?? [] as $panelId => $panel) {
            if ($this->canAccessPanel($user, $panelId)) {
                $accessiblePanels[$panelId] = $panel;
            }
        }
        
        foreach ($panelsByAccess['restricted'] ?? [] as $panelId => $panel) {
            if ($this->canAccessPanel($user, $panelId)) {
                $accessiblePanels[$panelId] = $panel;
            }
        }
        
        return $accessiblePanels;
    }
} 