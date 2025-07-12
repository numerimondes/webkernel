<?php

namespace Webkernel\Core\Helpers;

use Illuminate\Support\Facades\Route;
use Webkernel\Core\Http\Middleware\CheckModuleAccess;

class RouteHelper
{
    /**
     * Enregistrer une route avec vérification d'accès au module
     */
    public static function moduleRoute(string $method, string $uri, $action, string $moduleId): void
    {
        Route::match([$method], $uri, $action)
            ->middleware(CheckModuleAccess::class . ':' . $moduleId);
    }

    /**
     * Enregistrer un groupe de routes avec vérification d'accès au module
     */
    public static function moduleGroup(string $moduleId, callable $callback): void
    {
        Route::middleware(CheckModuleAccess::class . ':' . $moduleId)
            ->group($callback);
    }

    /**
     * Enregistrer un groupe de routes pour un panneau avec vérification automatique
     */
    public static function panelGroup(string $panelId, callable $callback): void
    {
        Route::middleware(CheckModuleAccess::class . ':handlePanelAccess')
            ->prefix($panelId)
            ->group($callback);
    }
} 