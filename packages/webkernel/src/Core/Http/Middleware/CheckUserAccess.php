<?php
namespace Webkernel\Core\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkernel\Core\Models\PlatformOwner;
use Webkernel\Core\Models\UserPanels;
use Webkernel\Core\Services\PanelsInfoCollector;
use Webkernel\Core\Services\PanelAccessService;
use Filament\Notifications\Notification;
class CheckUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $path = $request->path();
        $segments = explode('/', $path);
        $panelId = $segments[0] ?? null;
        $accessService = new PanelAccessService();

        // Si pas connecté, laisser passer (Filament gère ça)
        if (!$user) {
            return $next($request);
        }

        // Si pas de panelId, laisser passer
        if (!$panelId) {
            return $next($request);
        }

        // Vérifier l'accès via le service
        if (!$accessService->canAccessPanel($user, $panelId)) {
            // Vérifier si l'utilisateur a accès à au moins un panel
            $accessiblePanels = $accessService->getAccessiblePanels($user);
            
            if (!empty($accessiblePanels)) {
                // Rediriger vers le premier panel accessible
                $firstAccessiblePanel = array_key_first($accessiblePanels);
                $redirectUrl = '/' . $firstAccessiblePanel;
                
                // Notification Filament pour informer de la redirection
                Notification::make()
                    ->title('Accès restreint')
                    ->warning()
                    ->persistent()
                    ->body('Vous n\'avez pas accès au panel "' . $panelId . '". Vous avez été redirigé vers un panel accessible.')
                    ->send();
                
                return redirect($redirectUrl);
            } else {
                // L'utilisateur n'a accès à aucun panel, afficher la page d'erreur
                return response()->view('webkernel::components.webkernel.ui.organism.access-denied.access-denied', [
                    'message' => 'Vous n\'avez accès à aucun panel. Contactez votre administrateur.'
                ], 403);
            }
        }

        return $next($request);
    }
}