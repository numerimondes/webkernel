<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\RenderHooks;

use Filament\View\PanelsRenderHook;
use Webkernel\Aptitudes\Base\Services\RenderHookRegistry;

/**
 * User-specific render hooks
 *
 * These hooks handle user-related features like themes, preferences,
 * user-specific UI elements, and authentication-dependent content.
 */
class UserHooks
{
    /**
     * Register all User hooks
     */
    public static function register(): void
    {
        RenderHookRegistry::registerFromModule('users', [
            [
                'hook' => PanelsRenderHook::HEAD_END,
                'callback' => [self::class, 'renderUserTheme'],
                'priority' => 90,
                'conditions' => ['auth' => true],
            ],
            [
                'hook' => PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                'callback' => [self::class, 'renderUserTimezone'],
                'priority' => 90,
                'conditions' => ['auth' => true],
            ],
        ]);
    }

    /**
     * Render user-specific timezone
     */
    public static function renderUserTimezone(): string
    {
        // Avoid duplications by checking if element already exists
        static $rendered = false;
        if ($rendered) {
            return '';
        }
        $rendered = true;

        return view('users::currentuserdatetime')->render();
    }
    /**
     * Render user-specific theme CSS
     */
    public static function renderUserTheme(): string
    {
        // Use request user to avoid duplicate auth queries
        $user = request()->user();
        if (!$user) {
            return '';
        }

        try {
            $preferences = \Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference::getOrCreateForUser($user);
            $theme = $preferences->theme_name ?? 'monochromatic-webkernel-css';

            if ($theme === 'classic') {
                return '';
            }

            return view("users::webkernel-css.{$theme}")->render();
        } catch (\Exception $e) {
            return '<!-- Theme error: ' . $e->getMessage() . ' -->';
        }
    }


}
