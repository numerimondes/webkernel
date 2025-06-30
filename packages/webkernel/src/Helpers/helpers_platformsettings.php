<?php

use Webkernel\Models\PlatformSetting;
use Illuminate\Support\Str;


/*
|--------------------------------------------------------------------------
| Usage Examples for PlatformSettingsHelper
|--------------------------------------------------------------------------
|
| // Get a setting value
| $platformName = PlatformSettingsHelper::getValue('PLATFORM_NAME');
|
| // Get all layout settings
| $layoutSettings = PlatformSettingsHelper::getSettingsByCategory('layout');
|
| // Get settings by card group
| $brandingSettings = PlatformSettingsHelper::getSettingsByCardGroup('branding');
|
| // Update a setting
| PlatformSettingsHelper::updateValue('PLATFORM_NAME', 'New App Name');
|
| // Bulk update
| PlatformSettingsHelper::bulkUpdateValues([
| 'PLATFORM_NAME' => 'Updated Name',
| 'THEME_PRIMARY_COLOR' => '#ff0000'
| ]);
|
*/

if (!function_exists('getPlatformValue')) {
    function getPlatformValue(string $key, ?int $tenantId = null): mixed
    {
        return PlatformSetting::getPlatformTypedValue($key, $tenantId);
    }
}

if (!function_exists('getPlatformSetting')) {
    function getPlatformSetting(string $key, ?int $tenantId = null): ?PlatformSetting
    {
        return PlatformSetting::getPlatformSetting($key, $tenantId);
    }
}

if (!function_exists('getPlatformPublicSettings')) {
    function getPlatformPublicSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        return PlatformSetting::getPlatformPublicSettings($tenantId);
    }
}

if (!function_exists('getPlatformSettingsByCategory')) {
    function getPlatformSettingsByCategory(string $category, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        return PlatformSetting::getPlatformAllByCategory($category, $tenantId);
    }
}

if (!function_exists('getPlatformSettingsByCardGroup')) {
    function getPlatformSettingsByCardGroup(string $cardGroup, ?int $tenantId = null): \Illuminate\Support\Collection
    {
        return PlatformSetting::getPlatformSettingsByCardGroup($cardGroup, $tenantId);
    }
}

if (!function_exists('getPlatformEditableSettings')) {
    function getPlatformEditableSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        return PlatformSetting::getPlatformEditableSettings($tenantId);
    }
}

if (!function_exists('updateValue')) {
    function updateValue(string $key, mixed $value, ?int $tenantId = null): bool
    {
        return PlatformSetting::setTypedValue($key, $tenantId);
    }
}

if (!function_exists('bulkUpdateValues')) {
    function bulkUpdateValues(array $settingsData, ?int $tenantId = null): bool
    {
        return PlatformSetting::bulkUpdate($settingsData, $tenantId);
    }
}

if (!function_exists('createSetting')) {
    function createSetting(string $key, array $settingData, ?int $tenantId = null): bool
    {
        return PlatformSetting::setSetting($key, $settingData, $tenantId);
    }
}

if (!function_exists('getPlatformName')) {
    function getPlatformName(?int $tenantId = null): ?string
    {
        return getPlatformValue('PLATFORM_NAME', $tenantId);
    }
}

if (!function_exists('getPlatformDescription')) {
    function getPlatformDescription(?int $tenantId = null): ?string
    {
        return getPlatformValue('PLATFORM_DESCRIPTION', $tenantId);
    }
}

if (!function_exists('getPlatformLogo')) {
    function getPlatformLogo(?int $tenantId = null): ?string
    {
        return PlatformSetting::getAbsoluteUrl('PLATFORM_LOGO', $tenantId);
    }
}

if (!function_exists('doesPlatformHaveFavicon')) {
    function doesPlatformHaveFavicon(?int $tenantId = null): bool
    {
        return !empty(PlatformSetting::getRawStoredData('PLATFORM_FAVICON', $tenantId));
    }
}

if (!function_exists('getPlatformFavicon')) {
    function getPlatformFavicon(?int $tenantId = null, int $size = 256): string
    {
        $favicon = PlatformSetting::getVerifiedStoredData('PLATFORM_FAVICON', $tenantId);
        return $favicon ?? "https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://numerimondes.com/&size={$size}";
    }
}

if (!function_exists('getPlatformFaviconHtml')) {
    function getPlatformFaviconHtml(?int $tenantId = null, int $size = 256): string
    {
        $faviconUrl = getPlatformFavicon($tenantId, $size);
        $extension = strtolower(pathinfo(parse_url($faviconUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

        $mimeTypes = [
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
        ];

        $mimeType = $mimeTypes[$extension] ?? 'image/x-icon';
        return '<link rel="icon" href="' . e($faviconUrl) . '" type="' . e($mimeType) . '">' . PHP_EOL;
    }
}

if (!function_exists('getPlatformLicence')) {
    function getPlatformLicence(?int $tenantId = null): ?string
    {
        return getPlatformValue('PLATFORM_LICENCE', $tenantId);
    }
}

if (!function_exists('getPlatformEnvironment')) {
    function getPlatformEnvironment(?int $tenantId = null): ?string
    {
        return getPlatformValue('PLATFORM_ENVIRONMENT', $tenantId);
    }
}

if (!function_exists('getPlatformThemePrimaryColor')) {
    function getPlatformThemePrimaryColor(?int $tenantId = null): ?string
    {
        return getPlatformValue('THEME_PRIMARY_COLOR', $tenantId);
    }
}

if (!function_exists('getPlatformThemeSecondaryColor')) {
    function getPlatformThemeSecondaryColor(?int $tenantId = null): ?string
    {
        return getPlatformValue('THEME_SECONDARY_COLOR', $tenantId);
    }
}

if (!function_exists('isPwaEnabled')) {
    function isPwaEnabled(?int $tenantId = null): bool
    {
        return getPlatformValue('PWA_ENABLED', $tenantId) === 'true';
    }
}

if (!function_exists('getPlatformPwaThemeColor')) {
    function getPlatformPwaThemeColor(?int $tenantId = null): ?string
    {
        return getPlatformValue('PWA_THEME_COLOR', $tenantId);
    }
}

if (!function_exists('getPlatformPwaBackgroundColor')) {
    function getPlatformPwaBackgroundColor(?int $tenantId = null): ?string
    {
        return getPlatformValue('PWA_BACKGROUND_COLOR', $tenantId);
    }
}

if (!function_exists('getPlatformGeneralLayout')) {
    function getPlatformGeneralLayout(?int $tenantId = null): ?string
    {
        return getPlatformValue('GENERAL_LAYOUT', $tenantId);
    }
}

if (!function_exists('getPlatformSidebarWidth')) {
    function getPlatformSidebarWidth(?int $tenantId = null): ?string
    {
        return getPlatformValue('SIDEBAR_WIDTH', $tenantId);
    }
}

if (!function_exists('getPlatformContentPadding')) {
    function getPlatformContentPadding(?int $tenantId = null): ?array
    {
        return getPlatformValue('CONTENT_PADDING', $tenantId);
    }
}

if (!function_exists('getPlatformContentPaddingX')) {
    function getPlatformContentPaddingX(?int $tenantId = null): ?string
    {
        $padding = getPlatformContentPadding($tenantId);
        return $padding['x'] ?? null;
    }
}

if (!function_exists('getPlatformContentPaddingY')) {
    function getPlatformContentPaddingY(?int $tenantId = null): ?string
    {
        $padding = getPlatformContentPadding($tenantId);
        return $padding['y'] ?? null;
    }
}

if (!function_exists('getPlatformBrandingSettings')) {
    function getPlatformBrandingSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        return getPlatformSettingsByCardGroup('branding', $tenantId);
    }
}

if (!function_exists('getPlatformLayoutSettings')) {
    function getPlatformLayoutSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        return getPlatformSettingsByCategory('layout', $tenantId);
    }
}

if (!function_exists('getPlatformThemeSettings')) {
    function getPlatformThemeSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        return getPlatformSettingsByCategory('theme', $tenantId);
    }
}

if (!function_exists('getPlatformSystemSettings')) {
    function getPlatformSystemSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        return getPlatformSettingsByCategory('system', $tenantId);
    }
}

if (!function_exists('getPlatformPwaSettings')) {
    function getPlatformPwaSettings(?int $tenantId = null): \Illuminate\Support\Collection
    {
        return getPlatformSettingsByCategory('pwa', $tenantId);
    }
}

if (!function_exists('getPlatformMetaTitle')) {
    function getPlatformMetaTitle(?int $tenantId = null): ?string
    {
        return getPlatformValue('META_TITLE', $tenantId) ?? getPlatformName($tenantId);
    }
}

if (!function_exists('getPlatformMetaDescription')) {
    function getPlatformMetaDescription(?int $tenantId = null): ?string
    {
        return getPlatformValue('META_DESCRIPTION', $tenantId) ?? getPlatformDescription($tenantId);
    }
}

if (!function_exists('getPlatformMetaKeywords')) {
    function getPlatformMetaKeywords(?int $tenantId = null): ?string
    {
        return getPlatformValue('META_KEYWORDS', $tenantId);
    }
}

if (!function_exists('getPlatformOgTitle')) {
    function getPlatformOgTitle(?int $tenantId = null): ?string
    {
        return getPlatformValue('OG_TITLE', $tenantId) ?? getPlatformName($tenantId);
    }
}

if (!function_exists('getPlatformOgDescription')) {
    function getPlatformOgDescription(?int $tenantId = null): ?string
    {
        return getPlatformValue('OG_DESCRIPTION', $tenantId) ?? getPlatformDescription($tenantId);
    }
}

if (!function_exists('getPlatformOgImage')) {
    function getPlatformOgImage(?int $tenantId = null): ?string
    {
        return PlatformSetting::getAbsoluteUrl('OG_IMAGE', $tenantId) ?? getPlatformLogo($tenantId);
    }
}

if (!function_exists('getPlatformOgType')) {
    function getPlatformOgType(?int $tenantId = null): ?string
    {
        return getPlatformValue('OG_TYPE', $tenantId) ?? 'website';
    }
}

if (!function_exists('getPlatformOgUrl')) {
    function getPlatformOgUrl(?int $tenantId = null): ?string
    {
        return getPlatformValue('OG_URL', $tenantId);
    }
}

if (!function_exists('getPlatformTwitterCard')) {
    function getPlatformTwitterCard(?int $tenantId = null): ?string
    {
        return getPlatformValue('TWITTER_CARD', $tenantId) ?? 'summary_large_image';
    }
}

if (!function_exists('getPlatformTwitterSite')) {
    function getPlatformTwitterSite(?int $tenantId = null): ?string
    {
        return getPlatformValue('TWITTER_SITE', $tenantId);
    }
}

if (!function_exists('getPlatformTwitterCreator')) {
    function getPlatformTwitterCreator(?int $tenantId = null): ?string
    {
        return getPlatformValue('TWITTER_CREATOR', $tenantId);
    }
}

if (!function_exists('getPlatformGoogleAnalyticsId')) {
    function getPlatformGoogleAnalyticsId(?int $tenantId = null): ?string
    {
        return getPlatformValue('GOOGLE_ANALYTICS_ID', $tenantId);
    }
}

if (!function_exists('getPlatformGoogleTagManagerId')) {
    function getPlatformGoogleTagManagerId(?int $tenantId = null): ?string
    {
        return getPlatformValue('GOOGLE_TAG_MANAGER_ID', $tenantId);
    }
}

if (!function_exists('getPlatformFacebookPixelId')) {
    function getPlatformFacebookPixelId(?int $tenantId = null): ?string
    {
        return getPlatformValue('FACEBOOK_PIXEL_ID', $tenantId);
    }
}

if (!function_exists('getPlatformCanonicalUrl')) {
    function getPlatformCanonicalUrl(?int $tenantId = null): ?string
    {
        return getPlatformValue('CANONICAL_URL', $tenantId);
    }
}

if (!function_exists('getPlatformRobotsContent')) {
    function getPlatformRobotsContent(?int $tenantId = null): ?string
    {
        return getPlatformValue('ROBOTS_CONTENT', $tenantId) ?? 'index, follow';
    }
}

if (!function_exists('getPlatformLanguage')) {
    function getPlatformLanguage(?int $tenantId = null): ?string
    {
        return getPlatformValue('PLATFORM_LANGUAGE', $tenantId) ?? 'en';
    }
}

if (!function_exists('getPlatformTimezone')) {
    function getPlatformTimezone(?int $tenantId = null): ?string
    {
        return getPlatformValue('PLATFORM_TIMEZONE', $tenantId) ?? 'UTC';
    }
}

if (!function_exists('getPlatformCurrency')) {
    function getPlatformCurrency(?int $tenantId = null): ?string
    {
        return getPlatformValue('PLATFORM_CURRENCY', $tenantId) ?? 'USD';
    }
}

if (!function_exists('getPlatformDateFormat')) {
    function getPlatformDateFormat(?int $tenantId = null): ?string
    {
        return getPlatformValue('DATE_FORMAT', $tenantId) ?? 'Y-m-d';
    }
}

if (!function_exists('getPlatformTimeFormat')) {
    function getPlatformTimeFormat(?int $tenantId = null): ?string
    {
        return getPlatformValue('TIME_FORMAT', $tenantId) ?? 'H:i:s';
    }
}

if (!function_exists('isMaintenanceMode')) {
    function isMaintenanceMode(?int $tenantId = null): bool
    {
        return getPlatformValue('MAINTENANCE_MODE', $tenantId) === 'true';
    }
}

if (!function_exists('getPlatformMaintenanceMessage')) {
    function getPlatformMaintenanceMessage(?int $tenantId = null): ?string
    {
        return getPlatformValue('MAINTENANCE_MESSAGE', $tenantId);
    }
}

if (!function_exists('isDebugMode')) {
    function isDebugMode(?int $tenantId = null): bool
    {
        return getPlatformValue('DEBUG_MODE', $tenantId) === 'true';
    }
}

if (!function_exists('getPlatformContactEmail')) {
    function getPlatformContactEmail(?int $tenantId = null): ?string
    {
        return getPlatformValue('CONTACT_EMAIL', $tenantId);
    }
}

if (!function_exists('getPlatformSupportEmail')) {
    function getPlatformSupportEmail(?int $tenantId = null): ?string
    {
        return getPlatformValue('SUPPORT_EMAIL', $tenantId);
    }
}

if (!function_exists('getPlatformTermsUrl')) {
    function getPlatformTermsUrl(?int $tenantId = null): ?string
    {
        return getPlatformValue('TERMS_URL', $tenantId);
    }
}

if (!function_exists('getPlatformPrivacyUrl')) {
    function getPlatformPrivacyUrl(?int $tenantId = null): ?string
    {
        return getPlatformValue('PRIVACY_URL', $tenantId);
    }
}

if (!function_exists('getPlatformCopyrightText')) {
    function getPlatformCopyrightText(?int $tenantId = null): ?string
    {
        $value = getPlatformValue('COPYRIGHT_TEXT', $tenantId);
        return $value ? lang($value) : null;
    }
}

if (!function_exists('getPlatformApiVersion')) {
    function getPlatformApiVersion(?int $tenantId = null): ?string
    {
        return getPlatformValue('API_VERSION', $tenantId) ?? '1.0';
    }
}

if (!function_exists('getPlatformMaxUploadSize')) {
    function getPlatformMaxUploadSize(?int $tenantId = null): ?string
    {
        return getPlatformValue('MAX_UPLOAD_SIZE', $tenantId);
    }
}

if (!function_exists('getPlatformAllowedFileTypes')) {
    function getPlatformAllowedFileTypes(?int $tenantId = null): ?string
    {
        return getPlatformValue('ALLOWED_FILE_TYPES', $tenantId);
    }
}

if (!function_exists('getPlatformSessionTimeout')) {
    function getPlatformSessionTimeout(?int $tenantId = null): ?string
    {
        return getPlatformValue('SESSION_TIMEOUT', $tenantId);
    }
}

if (!function_exists('isRegistrationEnabled')) {
    function isRegistrationEnabled(?int $tenantId = null): bool
    {
        return getPlatformValue('REGISTRATION_ENABLED', $tenantId) === 'true';
    }
}

if (!function_exists('is2faEnabled')) {
    function is2faEnabled(?int $tenantId = null): bool
    {
        return getPlatformValue('TWO_FACTOR_ENABLED', $tenantId) === 'true';
    }
}

if (!function_exists('getPlatformPasswordMinLength')) {
    function getPlatformPasswordMinLength(?int $tenantId = null): ?int
    {
        $length = getPlatformValue('PASSWORD_MIN_LENGTH', $tenantId);
        return $length ? (int) $length : 8;
    }
}

if (!function_exists('getPlatformMetaTags')) {
    function getPlatformMetaTags(?int $tenantId = null): array
    {
        return [
            'title' => getPlatformMetaTitle($tenantId),
            'description' => getPlatformMetaDescription($tenantId),
            'keywords' => getPlatformMetaKeywords($tenantId),
            'robots' => getPlatformRobotsContent($tenantId),
            'canonical' => getPlatformCanonicalUrl($tenantId),
            'language' => getPlatformLanguage($tenantId),
        ];
    }
}

if (!function_exists('getPlatformMetaTagsHtml')) {
    function getPlatformMetaTagsHtml(?int $tenantId = null): string
    {
        $tags = getPlatformMetaTags($tenantId);
        $html = '';

        if (!empty($tags['title'])) {
            $html .= '<title>' . e($tags['title']) . '</title>' . PHP_EOL;
        }

        if (!empty($tags['description'])) {
            $html .= '<meta name="description" content="' . e(lang($tags['description'])) . '">' . PHP_EOL;
        }

        if (!empty($tags['keywords'])) {
            $html .= '<meta name="keywords" content="' . e($tags['keywords']) . '">' . PHP_EOL;
        }

        if (!empty($tags['robots'])) {
            $html .= '<meta name="robots" content="' . e($tags['robots']) . '">' . PHP_EOL;
        }

        if (!empty($tags['canonical'])) {
            $html .= '<link rel="canonical" href="' . e($tags['canonical']) . '">' . PHP_EOL;
        }

        if (!empty($tags['language'])) {
            $html .= '<meta name="language" content="' . e($tags['language']) . '">' . PHP_EOL;
        }

        return $html;
    }
}

if (!function_exists('getPlatformOpenGraphTags')) {
    function getPlatformOpenGraphTags(?int $tenantId = null): array
    {
        return [
            'og:title' => getPlatformOgTitle($tenantId),
            'og:description' => getPlatformOgDescription($tenantId),
            'og:image' => getPlatformOgImage($tenantId),
            'og:type' => getPlatformOgType($tenantId),
            'og:url' => getPlatformOgUrl($tenantId),
        ];
    }
}

if (!function_exists('getPlatformOpenGraphTagsHtml')) {
    function getPlatformOpenGraphTagsHtml(?int $tenantId = null): string
    {
        $tags = getPlatformOpenGraphTags($tenantId);
        $html = '';

        foreach ($tags as $property => $content) {
            if ($content) {
                $html .= '<meta property="' . e($property) . '" content="' . e($content) . '">' . PHP_EOL;
            }
        }

        return $html;
    }
}

if (!function_exists('getPlatformTwitterTags')) {
    function getPlatformTwitterTags(?int $tenantId = null): array
    {
        return [
            'twitter:card' => getPlatformTwitterCard($tenantId),
            'twitter:site' => getPlatformTwitterSite($tenantId),
            'twitter:creator' => getPlatformTwitterCreator($tenantId),
            'twitter:title' => getPlatformOgTitle($tenantId),
            'twitter:description' => getPlatformOgDescription($tenantId),
            'twitter:image' => getPlatformOgImage($tenantId),
        ];
    }
}

if (!function_exists('getPlatformTwitterTagsHtml')) {
    function getPlatformTwitterTagsHtml(?int $tenantId = null): string
    {
        $tags = getPlatformTwitterTags($tenantId);
        $html = '';

        foreach ($tags as $name => $content) {
            if ($content) {
                $html .= '<meta name="' . e($name) . '" content="' . ($name === 'twitter:description' ? e(lang($content)) : e($content)) . '">' . PHP_EOL;
            }
        }

        return $html;
    }
}

if (!function_exists('getPlatformAnalyticsTags')) {
    function getPlatformAnalyticsTags(?int $tenantId = null): array
    {
        return [
            'google_analytics' => getPlatformGoogleAnalyticsId($tenantId),
            'google_tag_manager' => getPlatformGoogleTagManagerId($tenantId),
            'facebook_pixel' => getPlatformFacebookPixelId($tenantId),
        ];
    }
}

if (!function_exists('getPlatformAnalyticsTagsHtml')) {
    function getPlatformAnalyticsTagsHtml(?int $tenantId = null): string
    {
        $tags = getPlatformAnalyticsTags($tenantId);
        $html = '';

        if ($googleAnalytics = $tags['google_analytics']) {
            $html .= "<script async src=\"https://www.googletagmanager.com/gtag/js?id=" . e($googleAnalytics) . "\"></script>" . PHP_EOL;
            $html .= "<script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '" . e($googleAnalytics) . "');</script>" . PHP_EOL;
        }

        if ($googleTagManager = $tags['google_tag_manager']) {
            $html .= "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . e($googleTagManager) . "');</script>" . PHP_EOL;
        }

        if ($facebookPixel = $tags['facebook_pixel']) {
            $html .= "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '" . e($facebookPixel) . "');fbq('track', 'PageView');</script>" . PHP_EOL;
        }

        return $html;
    }
}

if (!function_exists('getAbsoluteUrl')) {
    function getAbsoluteUrl(string $key, ?int $tenantId = null, string $baseUrl = 'https://numerimondes.com'): ?string
    {
        return PlatformSetting::getAbsoluteUrl($key, $tenantId, $baseUrl);
    }
}

if (!function_exists('getRawStoredData')) {
    function getRawStoredData(string $key, ?int $tenantId = null): mixed
    {
        return PlatformSetting::getRawStoredData($key, $tenantId);
    }
}

if (!function_exists('getVerifiedStoredData')) {
    function getVerifiedStoredData(string $key, ?int $tenantId = null): ?string
    {
        return PlatformSetting::getVerifiedStoredData($key, $tenantId);
    }
}
