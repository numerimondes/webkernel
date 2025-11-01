@php
    use Webkernel\Aptitudes\WebsiteBuilder\Services\ThemeQuery;

    $projectId = $projectId ?? 1;
    $themeQuery = ThemeQuery::forProjectWithFallback($projectId);
@endphp

<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}"
    dir="{{ $direction ?? (in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr') }}"
    x-bind:class="getHtmlClasses(theme)"
    x-bind:style="getInlineStyles(theme)"
    x-data="{
        primaryColor: '{{ $primaryColor ?? 'blue' }}',
        theme: localStorage.getItem('theme') || '{{ $defaultTheme ?? 'system' }}',
        themeConfig: {
            useCustomProperties: true,
            initialColorMode: '{{ $defaultTheme ?? 'system' }}',
            colors: @js($themeQuery->getColors()),
            modeConfiguration: @js($themeQuery->getModeConfiguration())
        },

        init() {
            // Initialize theme from Alpine store
            this.$watch('$store.theme', (value) => {
                this.theme = localStorage.getItem('theme') || '{{ $defaultTheme ?? 'system' }}';
            });

            // Listen for theme changes from other components
            window.addEventListener('theme-changed', (event) => {
                this.theme = event.detail;
            });
        },

        getHtmlClasses(theme) {
            const classes = [];

            // Add theme-specific classes
            if (theme !== 'light' && theme !== 'system') {
                classes.push(theme);
            }

            // Add dark class if current theme is dark
            if (this.isDarkMode(theme)) {
                classes.push('dark');
            }

            // Add special classes based on theme type
            classes.push('theme-' + theme);

            return classes.join(' ');
        },

        getBodyClasses(theme) {
            const classes = [
                'min-h-screen',
                'transition-all',
                'duration-300',
                'ease-in-out',
                'antialiased'
            ];

            // Add theme-specific classes
            classes.push('theme-' + theme + '-body');

            // Add type-specific classes
            if (this.isDarkMode(theme)) {
                classes.push('theme-dark-body');
            } else {
                classes.push('theme-light-body');
            }

            return classes.join(' ');
        },

        getInlineStyles(theme) {
            const styles = [];
            const colors = this.getColorsForMode(theme);

            // Get colors for current theme
            for (const [key, value] of Object.entries(colors)) {
                styles.push(`--color-${key}: ${value}`);
            }

            // Add color scheme
            const colorScheme = this.isDarkMode(theme) ? 'dark' : 'light';
            styles.push(`color-scheme: ${colorScheme}`);

            return styles.join('; ');
        },

        getColorsForMode(mode) {
            if (mode === 'light') {
                return this.themeConfig.colors || {};
            }
            return this.themeConfig.colors?.modes?.[mode] || {};
        },

        isDarkMode(mode) {
            const modeConfig = this.themeConfig.modeConfiguration || {};
            return (modeConfig[mode]?.type || 'light') === 'dark';
        },

        setTheme(theme) {
            this.theme = theme;
            window.app.setTheme(theme);
        },

        toggleTheme() {
            const newTheme = window.app.toggleTheme();
            this.theme = newTheme;
            return newTheme;
        }
    }" :class="{ 'dark': $store.theme === 'dark' }">

<!-- Performance Marker: {{ microtime(true) - LARAVEL_START < 0.01 ? '<0.01µs' : round((microtime(true) - LARAVEL_START) * 1000000, 2) . 'µs' }} -->

<head>
    @includeIf('website-builder::live-website.layouts.partials.meta')
    @includeIf('website-builder::live-website.layouts.partials.styles')
    @includeIf('website-builder::live-website.layouts.partials.head-scripts')

    @stack('head')
    @yield('head')
</head>

<body x-bind:class="getBodyClasses(theme)">
    @includeIf('website-builder::live-website.layouts.partials.loading')

    <div class="flex flex-col min-h-screen">
        @includeIf('website-builder::live-website.layouts.partials.navigation')

        <main class="flex-1 relative">
            @includeIf('website-builder::live-website.layouts.partials.flash-messages')

            <div class="animate-fade-in">
                @includeIf('numerimondes::ModuleBlocks.index')

                @yield('content')

                @stack('content')
            </div>
        </main>

        @includeIf('website-builder::live-website.layouts.partials.footer')
    </div>

    @includeIf('website-builder::live-website.layouts.partials.scroll-top')
    @includeIf('website-builder::live-website.layouts.partials.scripts')
    @includeIf('website-builder::live-website.layouts.partials.debug')

    @stack('scripts')
    @yield('scripts')
</body>
</html>
