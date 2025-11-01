<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

use Filament\Facades\Filament;
use ReflectionClass;

Route::prefix('debug/user-extensions')
  ->middleware(['web'])
  ->group(function (): void {
    $handleDebugRequest = static function (bool $shouldClearCache): JsonResponse|RedirectResponse {
      try {
        if (!Auth::check()) {
          return error_response()
            ->code(401)
            ->errorCode('AUTH_DEBUG_001')
            ->message('Veuillez vous connecter pour accéder à cette fonctionnalité.')
            ->source('debug/user-extensions')
            ->action(
              type: 'link',
              color: 'primary',
              action: 'redirect',
              label: 'Se connecter',
              description: 'Connectez-vous pour accéder à cette fonctionnalité.',
              tooltip: 'Connectez-vous pour accéder à cette fonctionnalité.',
              href: 'ds',
            )
            ->redirect();
        }

        $user = Auth::user();

        // ✅ Sécurité : si par hasard $user est null malgré Auth::check()
        if (!$user instanceof User) {
          return error_response()
            ->code(500)
            ->message('User instance unavailable')
            ->source('debug/user-extensions')
            ->redirect();
        }

        // Extensions utilisateur
        $extensions = User::availableUserExtensions();

        // Vide le cache si demandé
        $cacheCleared = $shouldClearCache ? User::clearExtensionCache() ?? true : false;

        return response()->json(
          [
            'success' => true,
            'user' => [
              'id' => $user->id,
              'name' => $user->name,
              'email' => $user->email,
            ],
            'cache_cleared' => $cacheCleared,
            'extensions' => $extensions,
            'timestamp' => now()->toIso8601String(),
          ],
          200,
          [],
          JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
        );
      } catch (Throwable $e) {
        return error_response()
          ->code(500)
          ->message('Unexpected error')
          ->source('debug/user-extensions')
          ->details($e->getMessage())
          ->redirect();
      }
    };

    Route::get('/', static fn() => $handleDebugRequest(false))->name('debug.user-extensions');

    Route::match(['get', 'post'], '/refresh', static fn() => $handleDebugRequest(true))->name(
      'debug.user-extensions.refresh',
    );
  });
