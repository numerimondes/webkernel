<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('platform_setting')) {
    /**
     * Get a platform setting value
     */
    function platform_setting(string $reference, $default = null, ?int $tenantId = null)
    {
        return \Webkernel\Models\PlatformSetting::get($reference, $default, $tenantId);
    }
}

if (!function_exists('set_platform_setting')) {
    /**
     * Set a platform setting value
     */
    function set_platform_setting(string $reference, $value, ?int $tenantId = null)
    {
        return \Webkernel\Models\PlatformSetting::set($reference, $value, $tenantId);
    }
}

if (!function_exists('public_platform_settings')) {
    /**
     * Get all public platform settings
     */
    function public_platform_settings(?int $tenantId = null): array
    {
        return \Webkernel\Models\PlatformSetting::getPublicSettings($tenantId);
    }
}

if (!function_exists('generate_dynamic_css')) {
    /**
     * Generate dynamic CSS based on platform settings
     */
    function generate_dynamic_css(?int $tenantId = null): string
    {
        $settings = public_platform_settings($tenantId);

        $css = ":root {\n";
        $css .= "    --primary-color: " . ($settings['THEME_PRIMARY_COLOR'] ?? '#3b82f6') . ";\n";
        $css .= "    --secondary-color: " . ($settings['THEME_SECONDARY_COLOR'] ?? '#64748b') . ";\n";
        $css .= "    --sidebar-width: " . ($settings['SIDEBAR_WIDTH'] ?? 16) . "px;\n";
        $css .= "    --sidebar-border-width: " . ($settings['SIDEBAR_BORDER_WIDTH'] ?? 0.1) . "em;\n";
        $css .= "    --sidebar-border-color: " . ($settings['SIDEBAR_BORDER_COLOR'] ?? 'rgba(var(--gray-200), 1)') . ";\n";
        $css .= "    --sidebar-dark-border-color: " . ($settings['SIDEBAR_DARK_BORDER_COLOR'] ?? 'rgba(var(--gray-800), 1)') . ";\n";
        $css .= "    --loader-background: " . ($settings['LOADER_BACKGROUND'] ?? 'rgba(255, 255, 255, 0.05)') . ";\n";
        $css .= "    --loader-dark-background: " . ($settings['LOADER_DARK_BACKGROUND'] ?? 'rgba(0, 0, 0, 0.3)') . ";\n";
        $css .= "    --loader-border-color: " . ($settings['LOADER_BORDER_COLOR'] ?? 'rgba(255, 255, 255, 0.12)') . ";\n";
        $css .= "    --loader-spinner-size: " . ($settings['LOADER_SPINNER_SIZE'] ?? 3) . "px;\n";
        $css .= "    --loader-spinner-border-width: " . ($settings['LOADER_SPINNER_BORDER_WIDTH'] ?? 4) . "px;\n";
        $css .= "    --loader-spinner-border-color: " . ($settings['LOADER_SPINNER_BORDER_COLOR'] ?? 'rgba(255, 255, 255, 0.2)') . ";\n";
        $css .= "}\n\n";

        // Responsive padding
        $paddingX = $settings['CONTENT_PADDING_X'] ?? 1;
        $paddingY = $settings['CONTENT_PADDING_Y'] ?? 2;

        $css .= "@media (min-width: 768px) {\n";
        $css .= "    .dynamic-content-padding {\n";
        $css .= "        padding-left: {$paddingX}% !important;\n";
        $css .= "        padding-right: {$paddingX}% !important;\n";
        $css .= "        padding-top: {$paddingY}px !important;\n";
        $css .= "        padding-bottom: {$paddingY}px !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        $css .= "@media (min-width: 1024px) {\n";
        $css .= "    .dynamic-content-padding {\n";
        $css .= "        padding-left: {$paddingX}% !important;\n";
        $css .= "        padding-right: {$paddingX}% !important;\n";
        $css .= "    }\n";
        $css .= "}\n\n";

        // Sidebar styling
        $css .= ":dir(ltr) aside.fi-main-sidebar.fi-sidebar-open {\n";
        $css .= "    width: var(--sidebar-width) !important;\n";
        $css .= "    border-right: var(--sidebar-border-width) solid var(--sidebar-border-color) !important;\n";
        $css .= "    border-left: none !important;\n";
        $css .= "}\n\n";

        $css .= ":dir(rtl) aside.fi-main-sidebar.fi-sidebar-open {\n";
        $css .= "    width: var(--sidebar-width) !important;\n";
        $css .= "    border-left: var(--sidebar-border-width) solid var(--sidebar-border-color) !important;\n";
        $css .= "    border-right: none !important;\n";
        $css .= "}\n\n";

        // Dark mode adjustments
        $css .= ":dir(ltr).dark aside.fi-main-sidebar {\n";
        $css .= "    border-right-color: var(--sidebar-dark-border-color) !important;\n";
        $css .= "    border-left-color: transparent !important;\n";
        $css .= "}\n\n";

        $css .= ":dir(rtl).dark aside.fi-main-sidebar {\n";
        $css .= "    border-left-color: var(--sidebar-dark-border-color) !important;\n";
        $css .= "    border-right-color: transparent !important;\n";
        $css .= "}\n\n";

        $css .= ":dir(ltr).dark.fi-topbar nav {\n";
        $css .= "    border-right-color: var(--sidebar-dark-border-color) !important;\n";
        $css .= "    border-left-color: transparent !important;\n";
        $css .= "}\n\n";

        $css .= ":dir(rtl).dark.fi-topbar nav {\n";
        $css .= "    border-left-color: var(--sidebar-dark-border-color) !important;\n";
        $css .= "    border-right-color: transparent !important;\n";
        $css .= "}\n\n";

        // Additional existing styles
        $css .= ".fi-sidebar-header {\n";
        $css .= "    background: transparent !important;\n";
        $css .= "}\n\n";

        $css .= "nav {\n";
        $css .= "    background-color: transparent !important;\n";
        $css .= "    backdrop-filter: blur(20px) saturate(180%);\n";
        $css .= "    -webkit-backdrop-filter: blur(20px) saturate(180%);\n";
        $css .= "    border-bottom: 1px solid rgba(255, 255, 255, 0.1);\n";
        $css .= "}\n\n";

        $css .= ".fi-breadcrumbs.mb-2.hidden.sm\\:block {\n";
        $css .= "    display: none !important;\n";
        $css .= "}\n\n";

        $css .= ".fi-modal-close-overlay {\n";
        $css .= "    background-color: rgba(0, 0, 0, 0) !important;\n";
        $css .= "}\n\n";

        $css .= ".fi-header-subheading {\n";
        $css .= "    max-width: none !important;\n";
        $css .= "    white-space: normal !important;\n";
        $css .= "}\n\n";

        // Loader styles
        $css .= "#overlay-loader {\n";
        $css .= "    position: fixed;\n";
        $css .= "    top: 0;\n";
        $css .= "    left: 0;\n";
        $css .= "    right: 0;\n";
        $css .= "    bottom: 0;\n";
        $css .= "    background: var(--loader-background);\n";
        $css .= "    backdrop-filter: blur(12px) saturate(160%);\n";
        $css .= "    -webkit-backdrop-filter: blur(12px) saturate(160%);\n";
        $css .= "    border: 1px solid var(--loader-border-color);\n";
        $css .= "    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);\n";
        $css .= "    z-index: 40;\n";
        $css .= "    display: flex;\n";
        $css .= "    align-items: center;\n";
        $css .= "    justify-content: center;\n";
        $css .= "    opacity: 0;\n";
        $css .= "    pointer-events: none;\n";
        $css .= "    transition: opacity 0.3s ease;\n";
        $css .= "}\n\n";

        $css .= ".dark #overlay-loader {\n";
        $css .= "    background: var(--loader-dark-background);\n";
        $css .= "    border-color: var(--loader-border-color);\n";
        $css .= "    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);\n";
        $css .= "}\n\n";

        $css .= "#overlay-loader.active {\n";
        $css .= "    opacity: 1;\n";
        $css .= "    pointer-events: auto;\n";
        $css .= "}\n\n";

        $css .= ".loader-spinner {\n";
        $css .= "    width: var(--loader-spinner-size);\n";
        $css .= "    height: var(--loader-spinner-size);\n";
        $css .= "    border: var(--loader-spinner-border-width) solid var(--loader-spinner-border-color);\n";
        $css .= "    border-top-color: var(--primary-color);\n";
        $css .= "    border-radius: 50%;\n";
        $css .= "    animation: spin 0.8s linear infinite;\n";
        $css .= "    backdrop-filter: none;\n";
        $css .= "}\n\n";

        $css .= "@keyframes spin {\n";
        $css .= "    to {\n";
        $css .= "        transform: rotate(360deg);\n";
        $css .= "    }\n";
        $css .= "}\n";

        return $css;
    }
}

if (! function_exists('generate_dynamic_js')) {
    /**
     * Generate dynamic JavaScript for real-time updates and loader
     */
    function generate_dynamic_js(?int $tenantId = null): string
    {
        $js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    // Ensure loader is appended directly to body
    const overlay = document.createElement('div');
    overlay.id = 'overlay-loader';
    overlay.innerHTML = '<div class="loader-spinner"></div>';
    document.body.appendChild(overlay);

    const reloadController = {
        handleReload() {
            overlay.classList.add('active');
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 550);
        }
    };

    // Real-time CSS update handler
    window.addEventListener('setting-updated', (event) => {
        const { reference, value, type } = event.detail;
        let cssProperty = '';
        let cssValue = value;

        switch (reference) {
            case 'THEME_PRIMARY_COLOR':
                cssProperty = '--primary-color';
                break;
            case 'THEME_SECONDARY_COLOR':
                cssProperty = '--secondary-color';
                break;
            case 'SIDEBAR_WIDTH':
                cssProperty = '--sidebar-width';
                cssValue = type === 'number' ? value + 'px' : value;
                break;
            case 'SIDEBAR_BORDER_WIDTH':
                cssProperty = '--sidebar-border-width';
                cssValue = type === 'number' ? value + 'em' : value;
                break;
            case 'SIDEBAR_BORDER_COLOR':
                cssProperty = '--sidebar-border-color';
                break;
            case 'SIDEBAR_DARK_BORDER_COLOR':
                cssProperty = '--sidebar-dark-border-color';
                break;
            case 'LOADER_BACKGROUND':
                cssProperty = '--loader-background';
                break;
            case 'LOADER_DARK_BACKGROUND':
                cssProperty = '--loader-dark-background';
                break;
            case 'LOADER_BORDER_COLOR':
                cssProperty = '--loader-border-color';
                break;
            case 'LOADER_SPINNER_SIZE':
                cssProperty = '--loader-spinner-size';
                cssValue = type === 'number' ? value + 'px' : value;
                break;
            case 'LOADER_SPINNER_BORDER_WIDTH':
                cssProperty = '--loader-spinner-border-width';
                cssValue = type === 'number' ? value + 'px' : value;
                break;
            case 'LOADER_SPINNER_BORDER_COLOR':
                cssProperty = '--loader-spinner-border-color';
                break;
            case 'CONTENT_PADDING_X':
                document.querySelectorAll('.dynamic-content-padding').forEach(el => {
                    el.style.paddingLeft = value + '%';
                    el.style.paddingRight = value + '%';
                });
                break;
            case 'CONTENT_PADDING_Y':
                document.querySelectorAll('.dynamic-content-padding').forEach(el => {
                    el.style.paddingTop = value + 'px';
                    el.style.paddingBottom = value + 'px';
                });
                break;
        }

        if (cssProperty) {
            document.documentElement.style.setProperty(cssProperty, cssValue);
        }
    });

    window.addEventListener('triggerSmoothReload', reloadController.handleReload);

    window.addEventListener('beforeunload', () => {
        window.pxoveEventListener('triggerSmoothReload', reloadController.handleReload);
    });
});
JS;

        return $js;
    }
}

if (!function_exists('generate_manifest_json')) {
    /**
     * Generate PWA manifest.json based on platform settings
     */
    function generate_manifest_json(?int $tenantId = null): array
    {
        $settings = public_platform_settings($tenantId);

        return [
            'name' => isset($settings['PLATFORM_NAME']) ? $settings['PLATFORM_NAME'] : 'Mon Application',
            'short_name' => substr(isset($settings['PLATFORM_NAME']) ? $settings['PLATFORM_NAME'] : 'App', 0, 12),
            'description' => isset($settings['PLATFORM_DESCRIPTION']) ? $settings['PLATFORM_DESCRIPTION'] : 'Description de mon application',
            'start_url' => '/',
            'display' => 'standalone',
            'theme_color' => isset($settings['PWA_THEME_COLOR']) ? $settings['PWA_THEME_COLOR'] : '#3b82f6',
            'background_color' => isset($settings['PWA_BACKGROUND_COLOR']) ? $settings['PWA_BACKGROUND_COLOR'] : '#ffffff',
            'orientation' => isset($settings['PWA_ORIENTATION']) ? $settings['PWA_ORIENTATION'] : 'portrait-primary',
            'scope' => '/',
            'icons' => [
                [
                    'src' => isset($settings['PLATFORM_LOGO']) ? $settings['PLATFORM_LOGO'] : '/images/logo.png',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => isset($settings['PLATFORM_LOGO']) ? $settings['PLATFORM_LOGO'] : '/images/logo.png',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
            'categories' => ['business', 'productivity'],
            'lang' => app()->getLocale(),
            'dir' => 'auto',
        ];
    }
}

if (!function_exists('generate_service_worker')) {
    /**
     * Generate service worker JavaScript based on platform settings
     */
    function generate_service_worker(?int $tenantId = null): string
    {
        $settings = public_platform_settings($tenantId);
        $pwaEnabled = isset($settings['PWA_ENABLED']) ? $settings['PWA_ENABLED'] : true;
        $platformLogo = isset($settings['PLATFORM_LOGO']) ? $settings['PLATFORM_LOGO'] : '/images/logo.png';
        $platformFavicon = isset($settings['PLATFORM_FAVICON']) ? $settings['PLATFORM_FAVICON'] : '/images/favicon.ico';

        if (!$pwaEnabled) {
            return '';
        }

        $sw = <<<JS
const CACHE_NAME = 'app-cache-v1';
const urlsToCache = [
    '/',
    '{$platformLogo}',
    '{$platformFavicon}',
    '/offline.html'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        }).catch(() => {
            return caches.match('/offline.html');
        })
    );
});

self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (!cacheWhitelist.includes(cacheName)) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
JS;

        return $sw;
    }
}
