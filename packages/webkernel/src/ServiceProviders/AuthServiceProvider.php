<?php
namespace Webkernel\ServiceProviders;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Webkernel\Core\Services\PanelsAccessManager;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();
        
        // Super admin global bypass
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });
        
        // Gate pour l'accès aux panneaux
        Gate::define('access-panel', function ($user, $panelId, $context = null) {
            return app(PanelsAccessManager::class)->userCanAccessPanel($user->id, $panelId, $context);
        });
        
        // Gate pour l'accès aux ressources
        Gate::define('access-resource', function ($user, $panelId, $resourceType, $resourceIdentifier, $accessType = 'view', $context = null) {
            return app(PanelsAccessManager::class)->userCanAccessResource(
                $user->id, 
                $panelId, 
                $resourceType, 
                $resourceIdentifier, 
                $accessType,
                $context
            );
        });
        
        // Gate pour vérifier le rôle dans un panneau
        Gate::define('panel-role', function ($user, $panelId, $role, $context = null) {
            $userRole = app(PanelsAccessManager::class)->getUserPanelRole($user->id, $panelId, $context);
            return $userRole === $role;
        });
        
        // Gate pour vérifier si l'utilisateur a un des rôles dans un panneau
        Gate::define('panel-any-role', function ($user, $panelId, $roles, $context = null) {
            $userRole = app(PanelsAccessManager::class)->getUserPanelRole($user->id, $panelId, $context);
            return in_array($userRole, (array) $roles);
        });
    }
} 