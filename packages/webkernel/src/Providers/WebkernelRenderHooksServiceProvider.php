<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Schema;
use Webkernel\Models\RenderHookSetting;

class WebkernelRenderHooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // You may bind services or perform registrations here in the future.
    }

    public function boot(): void
    {
        $this->registerPanelsRenderHooks();
        $this->forceSidebarCollapsible();
    }

    protected function isRenderHookEnabled(string $key): bool
    {
        try {
            // Vérification de la connexion DB avant toute opération
            \DB::connection()->getPdo();

            if (!Schema::hasTable('render_hook_settings')) {
                return true; // Fallback si la table n'existe pas
            }

            return (bool) optional(RenderHookSetting::where('hook_key', $key)->first())->enabled ?? true;

        } catch (\Exception $e) {
            return true; // Fallback si la DB n'est pas accessible
        }
    }

    protected function forceSidebarCollapsible(): void
    {
        $currentPanel = filament()->getCurrentPanel();

        if ($currentPanel) {
            $this->forceSidebarSettings($currentPanel);
        }
    }

    protected function forceSidebarSettings($currentPanel): void
    {
        $currentPanel->sidebarCollapsibleOnDesktop(true);
        $currentPanel->sidebarFullyCollapsibleOnDesktop(true);
    }

    protected function registerPanelsRenderHooks(): void
    {
        if (!app()->runningInConsole()) {
            app()->singleton('page_start_time', fn() => microtime(true));
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn() => customizable_render_hook_view('webkernel::components.webkernel.assets.head-css')->render()
        );

        if ($this->isRenderHookEnabled('current_user_datetime')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn() => customizable_render_hook_view('webkernel::components.webkernel.ui.atoms.currentuserdatetime')
            );
        }

        if ($this->isRenderHookEnabled('language_selector')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn() => customizable_render_hook_view('webkernel::components.webkernel.ui.molecules.language-selector')
            );
        }

        if ($this->isRenderHookEnabled('search_hide')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn() => customizable_render_hook_view('webkernel::components.webkernel.ui.atoms.search-hide')
            );
        }



        // Optional hooks (commentés volontairement)
        /*
         if ($this->isRenderHookEnabled('footer_partial')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::FOOTER,
                fn () => customizable_render_hook_view('webkernel::components.webkernel.ui.atoms.footer')
            );
        }

        if ($this->isRenderHookEnabled('tenant_menu_after')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::TENANT_MENU_AFTER,
                fn () => customizable_render_hook_view('components.userpanels')
            );
        }
        */


        FilamentView::registerRenderHook(
    PanelsRenderHook::BODY_END,
    function () {
        $currentLocale = app()->getLocale();
        $rtlLanguages = config('webkernel.translation.rtl_languages', []);
        $isRTL = in_array($currentLocale, $rtlLanguages);

        $positionSide = $isRTL ? 'left: 10px;' : 'right: 10px;';

        if (!session()->has('numerimondes_badge_id')) {
            session(['numerimondes_badge_id' => 'nm-badge-' . bin2hex(random_bytes(4))]);
        }

        $brand = 'Numerimondes';
        $logo = 'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://numerimondes.com/&size=256';
        $base_width = 36;
        $img_width = $base_width * 0.6;
        $svg_width = $base_width * 0.4;
        $id = session('numerimondes_badge_id');
        $poweredByText = lang('powered_by');
        $poweredLinkBrand = "https://numerimondes.com";

        $poweredLinkBrandIcon = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
        </svg>
        SVG;

        return <<<HTML
        <div id="{$id}" style="
            {$positionSide}
            position: fixed; bottom: 10px; padding: 8px 14px; font-size: 12px;
            font-family: sans-serif; color: white; background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); z-index: 9999; /* Below loader */
            display: flex; align-items: center; white-space: nowrap;
            gap: 6px; cursor: default;
        ">
            <style>
                .size-6 {
                    width: 16px;
                    height: 16px;
                    display: inline-block;
                    vertical-align: middle;
                }
                #{$id} span.powered-by-container {
                    display: none;
                    align-items: center;
                    gap: 4px;
                }
                #{$id}:hover span.powered-by-container {
                    display: inline-flex;
                }
            </style>
            <span class="powered-by-container" style="margin-left: 3px; margin-right: 3px;">
                {$poweredByText}
            </span>
            <img src="{$logo}" alt="Logo" style="width: {$img_width}px; height: auto; display: inline-block; vertical-align: middle; margin-right: 2px;">
            <strong>{$brand}</strong>
            <span class="powered-by-container" style="margin-left: 3px; margin-right: 3px;">
                <a class="powered-by-container" href="{$poweredLinkBrand}" target="_blank" rel="noopener noreferrer" style="margin-left: 3px; margin-right: 3px; color: inherit; text-decoration: none;">
                    {$poweredLinkBrandIcon}
                </a>
            </span>
        </div>
        <script>
            (function() {
                'use strict';
                const BADGE_ID = '{$id}';
                const LOADER_ID = 'overlay-loader'; // ID of the loader to exclude
                const BADGE_MARGIN = 20; // Space above the badge
                const CHECK_INTERVAL = 100; // Continuous check every 100ms
                const PROCESSED_ATTR = 'data-nm-processed';
                let badge = null;
                let badgeHeight = 0;
                let badgeBottom = 10; // Initial position
                let processedElements = new WeakSet();
                let isProcessing = false;
                let cachedFixedElements = new Map();
                let lastDOMUpdate = 0;

                // Retrieve the badge element
                function getBadge() {
                    if (!badge) {
                        badge = document.getElementById(BADGE_ID);
                        if (badge) {
                            badgeHeight = badge.offsetHeight;
                        }
                    }
                    return badge;
                }

                // Check if an element is fixed-position
                function isElementFixed(element) {
                    if (!element || element === badge || element.id === BADGE_ID || element.id === LOADER_ID) {
                        return false;
                    }
                    if (cachedFixedElements.has(element)) {
                        return cachedFixedElements.get(element);
                    }
                    const style = getComputedStyle(element);
                    const isFixed = style.position === 'fixed' &&
                                    style.display !== 'none' &&
                                    style.visibility !== 'hidden';
                    cachedFixedElements.set(element, isFixed);
                    return isFixed;
                }

                // Check if an element is in the bottom-right quadrant
                function isInBottomRightQuadrant(rect) {
                    const windowWidth = window.innerWidth;
                    const windowHeight = window.innerHeight;
                    return rect.right >= windowWidth * 0.5 &&
                           rect.bottom >= windowHeight * 0.7;
                }

                // Determine if an element should be pushed up
                function shouldElementBePushed(element) {
                    if (!isElementFixed(element)) return false;
                    if (element.id === BADGE_ID || element.id === LOADER_ID) return false;
                    const rect = element.getBoundingClientRect();
                    if (!isInBottomRightQuadrant(rect)) return false;
                    const requiredSpace = badgeHeight + BADGE_MARGIN;
                    return window.innerHeight - rect.bottom < requiredSpace;
                }

                // Push an element upward to avoid badge overlap
                function pushUpElement(element) {
                    if (processedElements.has(element) || element.id === BADGE_ID || element.id === LOADER_ID) return;
                    const currentBottom = parseInt(element.style.bottom) || 0;
                    const requiredShift = badgeHeight + BADGE_MARGIN + 4;
                    if (currentBottom < requiredShift) {
                        element.style.bottom = requiredShift + 'px';
                        element.setAttribute(PROCESSED_ATTR, 'true');
                        processedElements.add(element);
                        console.log('Element shifted:', element, 'new bottom:', requiredShift + 'px');
                    }
                }

                // Process all fixed elements, excluding the loader
                function processAllFixedElements() {
                    if (isProcessing || !getBadge()) return;
                    isProcessing = true;
                    try {
                        const now = Date.now();
                        if (now - lastDOMUpdate > 1000) {
                            cachedFixedElements.clear();
                            lastDOMUpdate = now;
                        }
                        const allElements = document.querySelectorAll('*:not(#' + BADGE_ID + '):not(#' + LOADER_ID + ')');
                        for (const element of allElements) {
                            if (shouldElementBePushed(element)) {
                                pushUpElement(element);
                            }
                        }
                    } catch (error) {
                        console.warn('Error processing fixed elements:', error);
                    } finally {
                        isProcessing = false;
                    }
                }

                // Continuously monitor DOM changes
                let lastCheck = 0;
                function continuousCheck() {
                    const now = Date.now();
                    if (now - lastCheck >= CHECK_INTERVAL) {
                        processAllFixedElements();
                        lastCheck = now;
                    }
                    requestAnimationFrame(continuousCheck);
                }

                // Observe DOM mutations
                const observer = new MutationObserver((mutations) => {
                    let shouldProcess = false;
                    for (const mutation of mutations) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            for (const node of mutation.addedNodes) {
                                if (node.nodeType === Node.ELEMENT_NODE) {
                                    cachedFixedElements.delete(node);
                                    shouldProcess = true;
                                }
                            }
                        }
                        if (mutation.type === 'attributes' &&
                            (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                            cachedFixedElements.delete(mutation.target);
                            shouldProcess = true;
                        }
                    }
                    if (shouldProcess) {
                        lastDOMUpdate = Date.now();
                        setTimeout(processAllFixedElements, 10);
                    }
                });

                // Initialize badge protection
                function initBadgeProtection() {
                    console.log('Initializing Numerimondes badge protection');
                    observer.observe(document.documentElement, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['style', 'class']
                    });
                    processAllFixedElements();
                    continuousCheck();
                    setTimeout(processAllFixedElements, 100);
                    setTimeout(processAllFixedElements, 500);
                    setTimeout(processAllFixedElements, 1000);
                    setTimeout(processAllFixedElements, 2000);
                }

                // Handle window resize
                window.addEventListener('resize', () => {
                    cachedFixedElements.clear();
                    setTimeout(processAllFixedElements, 10);
                });

                // Handle scroll events
                window.addEventListener('scroll', () => {
                    if (Math.random() < 0.1) {
                        processAllFixedElements();
                    }
                });

                // Start badge protection
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initBadgeProtection);
                } else {
                    initBadgeProtection();
                }

                // Protect badge from third-party scripts
                Object.defineProperty(window, 'nmBadgeProtection', {
                    value: {
                        forceCheck: processAllFixedElements,
                        getBadge: getBadge,
                        version: '2.0'
                    },
                    writable: false,
                    configurable: false
                });
            })();
        </script>
        HTML;
    }
);

        switch (true) {
            // Render hook to test
            case true:
                FilamentView::registerRenderHook(
                    PanelsRenderHook::BODY_END,
                    fn () => <<<HTML
                        <div style="
                            position: fixed;
                            bottom: 5px; /* positionné au-dessus du badge qui est à 10px */
                            right: 10px;
                            z-index: 39;
                            background-color: #2563eb;
                            color: white;
                            padding: 12px 16px;
                            border-radius: 9999px;
                            font-size: 14px;
                            font-family: sans-serif;
                            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                            cursor: pointer;
                        ">
                            💬 Chat
                        </div>
                    HTML
                );
                break;

            default:
                break;
        }


    }
}
