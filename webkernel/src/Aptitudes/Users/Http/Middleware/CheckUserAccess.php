<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkernel\Aptitudes\Panels\Services\PanelAccessService;
use Filament\Notifications\Notification;
class CheckUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Get user from request to avoid duplicate auth queries
        $user = $request->user();
        $path = $request->path();
        $segments = explode('/', $path);
        $panelId = $segments[0] ?? null;
        $accessService = new PanelAccessService();

        if (!$user) {
            return $next($request);
        }

        if (!$panelId) {
            return $next($request);
        }

        if (!$accessService->canAccessPanel($user, $panelId)) {
            // Check if the user has access to at least one panel
            $accessiblePanels = $accessService->getAccessiblePanels($user);

            if (!empty($accessiblePanels)) {
                // Redirect to the first accessible panel
                $firstAccessiblePanel = array_key_first($accessiblePanels);
                $redirectUrl = '/' . $firstAccessiblePanel;

                // Notification Filament to inform about the redirection
                Notification::make()
                    ->title('Restricted access')
                    ->warning()
                    ->persistent()
                    ->body('You do not have access to the panel "' . $panelId . '". You have been redirected to an accessible panel.')
                    ->send();

                return redirect($redirectUrl);
            } else {
                // The user has no access to any panel, display the error page
                return response()->view('webkernel-users::components.webkernel.ui.organism.access-denied.access-denied', [
                    'message' => 'You do not have access to any panel. Contact your administrator.'
                ], 403);
            }
        }

        return $next($request);
    }
}
