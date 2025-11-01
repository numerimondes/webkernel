@php
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Webkernel\Aptitudes\RBAC\Facades\WebkernelRBAC;
use Webkernel\Aptitudes\RBAC\Models\Role;
use Webkernel\Aptitudes\RBAC\Models\Permission;
use App\Models\User;

$dd = [];

// 1. Informations sur les panels Filament
$dd['panels'] = [];
foreach (Filament::getPanels() as $panel) {
    $dd['panels'][] = [
        'id' => $panel->getId(),
        'path' => $panel->getPath(),
        'is_default' => $panel->isDefault(),
        'resources' => $panel->getResources(),
        'pages' => $panel->getPages(),
        'widgets' => $panel->getWidgets(),
        'middleware' => $panel->getMiddleware(),
        'auth_middleware' => $panel->getAuthMiddleware(),
    ];
}

// 2. Ressources Filament découvertes
$dd['resources'] = [];
foreach (Filament::getPanels() as $panel) {
    $resources = $panel->getResources();
    foreach ($resources as $resource) {
        if (class_exists($resource)) {
            $reflection = new ReflectionClass($resource);
            $model = null;
            $policy = null;

            // Récupérer le modèle
            if ($reflection->hasProperty('model')) {
                $modelProperty = $reflection->getProperty('model');
                $modelProperty->setAccessible(true);
                $model = $modelProperty->getValue();
            }

            // Récupérer la policy
            if ($model && Gate::getPolicyFor($model)) {
                $policy = Gate::getPolicyFor($model);
            }

            $dd['resources'][] = [
                'class' => $resource,
                'model' => $model,
                'policy' => $policy ? get_class($policy) : null,
                'panel' => $panel->getId(),
                'slug' => method_exists($resource, 'getSlug') ? $resource::getSlug() : null,
                'navigation_icon' => method_exists($resource, 'getNavigationIcon') ? $resource::getNavigationIcon() : null,
                'record_title_attribute' => method_exists($resource, 'getRecordTitleAttribute') ? $resource::getRecordTitleAttribute() : null,
            ];
        }
    }
}

// 3. Policies enregistrées
$dd['policies'] = [];
$policies = Gate::policies();
foreach ($policies as $model => $policy) {
    $dd['policies'][] = [
        'model' => $model,
        'policy' => $policy,
        'methods' => get_class_methods($policy),
    ];
}

// 4. Utilisateur actuel et ses permissions
$user = auth()->user();
$dd['current_user'] = null;
if ($user) {
    $dd['current_user'] = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'roles' => method_exists($user, 'getRoles') ? $user->getRoles() : 'N/A',
        'permissions' => method_exists($user, 'getPermissions') ? $user->getPermissions() : 'N/A',
    ];
}

// 5. Rôles et permissions RBAC
$dd['rbac'] = [
    'roles' => Role::with('permissions')->get()->map(function($role) {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'permissions_count' => $role->permissions->count(),
            'permissions' => $role->permissions->map(function($perm) {
                return [
                    'id' => $perm->id,
                    'signature' => $perm->signature,
                    'ability' => $perm->ability,
                    'resource_class' => $perm->resource_class,
                    'model_class' => $perm->model_class,
                    'panel_id' => $perm->panel_id,
                    'effect' => $perm->pivot->effect ?? null,
                ];
            }),
        ];
    }),
    'permissions' => Permission::all()->map(function($permission) {
        return [
            'id' => $permission->id,
            'signature' => $permission->signature,
            'ability' => $permission->ability,
            'resource_class' => $permission->resource_class,
            'model_class' => $permission->model_class,
            'panel_id' => $permission->panel_id,
            'is_active' => $permission->is_active,
            'label' => $permission->getLabel(),
        ];
    }),
];

// 6. Test des autorisations pour l'utilisateur actuel
$dd['authorization_tests'] = [];
if ($user) {
    foreach ($dd['resources'] as $resource) {
        if ($resource['model']) {
            $modelClass = $resource['model'];

            // Capacités qui nécessitent seulement la classe du modèle
            $classAbilities = ['viewAny', 'create', 'deleteAny', 'restoreAny', 'forceDeleteAny', 'reorder'];

            // Capacités qui nécessitent une instance du modèle
            $instanceAbilities = ['view', 'update', 'delete', 'restore', 'forceDelete', 'replicate'];

            // Test des capacités de classe
            foreach ($classAbilities as $ability) {
                try {
                    $result = Gate::forUser($user)->allows($ability, $modelClass);
                    $dd['authorization_tests'][] = [
                        'resource' => $resource['class'],
                        'model' => $modelClass,
                        'ability' => $ability,
                        'type' => 'class',
                        'result' => $result,
                        'panel' => $resource['panel'],
                    ];
                } catch (Exception $e) {
                    $dd['authorization_tests'][] = [
                        'resource' => $resource['class'],
                        'model' => $modelClass,
                        'ability' => $ability,
                        'type' => 'class',
                        'result' => 'ERROR: ' . $e->getMessage(),
                        'panel' => $resource['panel'],
                    ];
                }
            }

            // Test des capacités d'instance - SIMPLE
            foreach ($instanceAbilities as $ability) {
                try {
                    // Juste créer une instance vide - point final
                    $modelInstance = new $modelClass();
                    $result = Gate::forUser($user)->allows($ability, $modelInstance);
                    $dd['authorization_tests'][] = [
                        'resource' => $resource['class'],
                        'model' => $modelClass,
                        'ability' => $ability,
                        'type' => 'instance',
                        'result' => $result,
                        'panel' => $resource['panel'],
                    ];
                } catch (Exception $e) {
                    $dd['authorization_tests'][] = [
                        'resource' => $resource['class'],
                        'model' => $modelClass,
                        'ability' => $ability,
                        'type' => 'instance',
                        'result' => 'ERROR: ' . $e->getMessage(),
                        'panel' => $resource['panel'],
                    ];
                }
            }
        }
    }
}

// 6.1. Test des autorisations RBAC directement
$dd['rbac_tests'] = [];
if ($user) {
    foreach ($dd['resources'] as $resource) {
        if ($resource['model']) {
            $modelClass = $resource['model'];
            $abilities = ['viewAny', 'view', 'create', 'update', 'delete', 'deleteAny', 'restore', 'restoreAny', 'forceDelete', 'forceDeleteAny', 'replicate', 'reorder'];

            foreach ($abilities as $ability) {
                try {
                    $result = WebkernelRBAC::check($ability, $user, $modelClass);
                    $dd['rbac_tests'][] = [
                        'resource' => $resource['class'],
                        'model' => $modelClass,
                        'ability' => $ability,
                        'result' => $result,
                        'panel' => $resource['panel'],
                    ];
                } catch (Exception $e) {
                    $dd['rbac_tests'][] = [
                        'resource' => $resource['class'],
                        'model' => $modelClass,
                        'ability' => $ability,
                        'result' => 'ERROR: ' . $e->getMessage(),
                        'panel' => $resource['panel'],
                    ];
                }
            }
        }
    }
}

// 7. Configuration des modules Webkernel
$dd['webkernel_modules'] = [];
if (class_exists('Webkernel\Arcanes\WebkernelApp')) {
    // Essayer de récupérer les modules enregistrés
    try {
        $modules = app('webkernel.modules') ?? [];
        foreach ($modules as $module) {
            $dd['webkernel_modules'][] = [
                'class' => get_class($module),
                'id' => method_exists($module, 'getId') ? $module->getId() : 'N/A',
                'name' => method_exists($module, 'getName') ? $module->getName() : 'N/A',
                'version' => method_exists($module, 'getVersion') ? $module->getVersion() : 'N/A',
            ];
        }
    } catch (Exception $e) {
        $dd['webkernel_modules'] = ['error' => $e->getMessage()];
    }
}

// 8. Informations sur les providers Filament
$dd['filament_providers'] = [];
$providers = app()->getLoadedProviders();
foreach ($providers as $providerClass => $provider) {
    if (is_subclass_of($providerClass, \Filament\PanelProvider::class)) {
        $dd['filament_providers'][] = [
            'class' => $providerClass,
            'panel_id' => method_exists($providerClass, 'getPanelId') ? $providerClass::getPanelId() : 'N/A',
        ];
    }
}

// 9. Cache RBAC
$dd['rbac_cache'] = [
    'user_cache_key' => $user ? 'rbac:' . Str::slug(class_basename($user)) . ':' . $user->getAuthIdentifier() . ':1' : null,
    'guest_cache_key' => 'rbac:guest',
    'version' => Cache::get('rbac:version', 1),
];

// 10. Configuration de l'application
$dd['app_config'] = [
    'app_name' => config('app.name'),
    'app_env' => config('app.env'),
    'app_debug' => config('app.debug'),
    'filament_strict_authorization' => config('filament.strict_authorization', false),
    'auth_guards' => config('auth.guards'),
    'auth_providers' => config('auth.providers'),
];

@endphp

<div style="font-family: monospace; background: #f5f5f5; padding: 20px; margin: 20px; border-radius: 8px;">
    <h1 style="color: #333; border-bottom: 2px solid #333; padding-bottom: 10px;">🔍 Debug Filament Resources & Permissions</h1>

    <h2 style="color: #666; margin-top: 30px;">📊 Panels Filament</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['panels'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">📁 Ressources Filament</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['resources'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">🛡️ Policies Enregistrées</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['policies'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">👤 Utilisateur Actuel</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['current_user'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">🔐 RBAC - Rôles & Permissions</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['rbac'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">✅ Tests d'Autorisation (Gate)</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['authorization_tests'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">🔐 Tests RBAC (WebkernelRBAC)</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['rbac_tests'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">🧩 Modules Webkernel</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['webkernel_modules'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">⚙️ Providers Filament</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['filament_providers'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">💾 Cache RBAC</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['rbac_cache'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

    <h2 style="color: #666; margin-top: 30px;">🔧 Configuration App</h2>
    <pre style="background: white; padding: 15px; border-radius: 4px; overflow-x: auto;">{{ json_encode($dd['app_config'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
</div>
