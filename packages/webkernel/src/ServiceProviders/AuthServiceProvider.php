<?php
namespace Webkernel\ServiceProviders;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Webkernel\Core\Services\PanelAccessService;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();
        $accessService = new PanelAccessService();
        
        // Super admin global bypass
        Gate::before(function ($user, $ability) use ($accessService) {
            if ($accessService->isSuperAdmin($user)) {
                return true;
            }
        });
        
        // Gate pour l'accès aux panneaux
        Gate::define('access-panel', function ($user, $panelId, $context = null) use ($accessService) {
            return $accessService->canAccessPanel($user, $panelId);
        });
        
        // Gate pour l'accès aux ressources (à adapter selon besoin réel)
        Gate::define('access-resource', function ($user, $panelId, $resourceType, $resourceIdentifier, $accessType = 'view', $context = null) use ($accessService) {
            // Ici, tu peux ajouter une logique plus fine si besoin
            return $accessService->canAccessPanel($user, $panelId);
        });
        
        // Gate pour vérifier le rôle dans un panneau (à adapter si tu veux une gestion de rôle plus fine)
        Gate::define('panel-role', function ($user, $panelId, $role, $context = null) {
            // À implémenter si besoin
            return false;
        });
        
        // Gate pour vérifier si l'utilisateur a un des rôles dans un panneau
        Gate::define('panel-any-role', function ($user, $panelId, $roles, $context = null) {
            // À implémenter si besoin
            return false;
        });
    }
} 