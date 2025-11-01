<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Base\RenderHooks;

use Filament\View\PanelsRenderHook;
use Webkernel\Aptitudes\Base\Services\RenderHookRegistry;

/**
 * Common render hooks that are used across multiple modules
 *
 * These hooks provide base functionality that other modules can build upon
 * or that are needed system-wide.
 */
class CommonHooks
{
    /**
     * Register all common hooks
     */
    public static function register(): void
    {
        RenderHookRegistry::registerFromModule('base', [
            [
                'hook' => PanelsRenderHook::HEAD_START,
                'callback' => [self::class, 'renderMetaTags'],
                'priority' => 100,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::SCRIPTS_BEFORE,
                'callback' => [self::class, 'renderGlobalScripts'],
                'priority' => 50,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::AUTH_REGISTER_FORM_AFTER,
                'callback' => [self::class, 'renderAuthRegisterFormAfter'],
                'priority' => 100,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::AUTH_PASSWORD_RESET_RESET_FORM_AFTER,
                'callback' => [self::class, 'renderAuthRegisterFormAfter'],
                'priority' => 100,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER,
                'callback' => [self::class, 'renderAuthRegisterFormAfter'],
                'priority' => 100,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                'callback' => [self::class, 'renderAuthRegisterFormAfter'],
                'priority' => 100,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                'callback' => [self::class, 'fiSimpleStyling'],
                'priority' => 100,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::AUTH_REGISTER_FORM_AFTER,
                'callback' => [self::class, 'fiSimpleStyling'],
                'priority' => 100,
                'conditions' => [],
            ],
            [
                'hook' => PanelsRenderHook::SIMPLE_LAYOUT_END,
                'callback' => [self::class, 'LegalNotice'],
                'priority' => 100,
                'conditions' => [],
            ],
        ]);
    }

    /**
     * Render legal notice
     */
    public static function LegalNotice(): string
    {
        return html_entity_decode('
         <div style="text-align: center; margin: 0 auto;">
                  <div style="text-align: center; max-width: 320px; margin: 0 auto; line-height: 1.4; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7);">
                     By using this service, you agree to our
                     <a href="/terms" target="_blank" class="fi-link fi-ac-link-action" style="color: rgba(255, 255, 255, 0.8);">Terms of Service</a>
                     and <a href="/privacy" target="_blank" class="fi-link fi-ac-link-action" style="color: rgba(255, 255, 255, 0.8);">Privacy Policy</a>.
             </div>

             <div style="text-align: center; margin: 20px auto 0; font-size: 0.8rem; color: rgba(255, 255, 255, 0.7);">
                 © '.date('Y').' Numerimondes. Morocco, by El Moumen Yassine.
             </div>
        </div>
       ');
    }

    /**
     * Render fi simple styling
     */
    public static function fiSimpleStyling(): string
    {
        return view('base::filament.pages.fi-simple')->render();
    }

    /**
     * Render auth register form after
     */
    public static function renderAuthRegisterFormAfter(): string
    {
        return '<div style="margin-top: 1rem; padding-left: 1rem; padding-right: 1rem; max-width: 384px; margin-left: auto; margin-right: auto;">
            '.view('filament-panels::components.theme-switcher.index')->render().'
        </div>';
    }

    /**
     * Render meta tags for all panels
     */
    public static function renderMetaTags(): string
    {
        return '
            <meta name="webkernel-version" content="1.0.0">
            <meta name="webkernel-modules" content="base">
        ';
    }

    /**
     * Render global scripts
     */
    public static function renderGlobalScripts(): string
    {
        return '<script>
            // Global Webkernel utilities
            window.Webkernel = window.Webkernel || {};
            window.Webkernel.version = "1.0.0";
        </script>';
    }
}
