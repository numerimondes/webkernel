<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n\RenderHooks;

use Filament\View\PanelsRenderHook;
use Illuminate\View\View;
use Webkernel\Aptitudes\Base\Services\RenderHookRegistry;

/**
 * I18n-specific render hooks
 *
 * These hooks handle internationalization features like language switchers,
 * locale-specific assets, and RTL support.
 */
class I18nHooks
{
    /**
     * Register all I18n hooks
     */
    public static function register(): void
    {
        RenderHookRegistry::registerFromModule('i18n', [

            [
                'hook' => PanelsRenderHook::USER_MENU_BEFORE,
                'callback' => [self::class, 'renderLanguageSelector'],
                'priority' => 90,
                'conditions' => ['auth' => true],
            ],

        ]);
    }

    /**
     * Render language selector using a simple approach
     */
    public static function renderLanguageSelector(): view
    {
        try {
            // Use the old working language-selector view
            return view('i18n::components.LanguageSelector.index');
        } catch (\Exception $e) {
            // Fallback in case of error
            return '<!-- Language selector error: ' . $e->getMessage() . ' -->';
        }
    }

}
