<?php

namespace Webkernel\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webkernel\Services\Panels\PanelsInfoCollector;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $moduleId): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Vérifier si l'utilisateur a accès au module
        if (!PanelsInfoCollector::userHasPanelAccess($user, $moduleId)) {
            abort(403, 'Accès non autorisé à ce module.');
        }
        
        return $next($request);
    }

    /**
     * Vérifier l'accès basé sur l'URL du panneau
     */
    public function handlePanelAccess(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Extraire l'ID du panneau depuis l'URL
        $path = $request->path();
        $segments = explode('/', $path);
        $panelId = $segments[0] ?? null;
        
        if ($panelId && !PanelsInfoCollector::userHasPanelAccess($user, $panelId)) {
            abort(403, 'Accès non autorisé à ce panneau.');
        }
        
        return $next($request);
    }
} 