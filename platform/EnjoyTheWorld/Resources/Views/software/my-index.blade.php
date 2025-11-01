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

    {{-- Canonical URL --}}
        <link rel="canonical" href="{{ url()->current() }}">

            @include('website-builder::live-website.layouts.partials.styles')

    <!-- Performance Marker: {{ microtime(true) - LARAVEL_START < 0.01 ? '<0.01µs' : round((microtime(true) - LARAVEL_START) * 1000000, 2) . 'µs' }} -->    {{-- =======================
        HEAD & HEADER
    ======================== --}}
    @include('enjoy-the-world::software.app-elements.head')
    @include('enjoy-the-world::software.app-elements.head-styles')

    <body>

        @include('enjoy-the-world::software.app-elements.main-header')


        {{-- =======================
            BODY SECTIONS
        ======================== --}}

        {{-- === HERO & INTRO === --}}
        @include('enjoy-the-world::software.body.01-0-hero-section')

        {{-- === DESTINATIONS & BENEFITS === --}}
        @include('enjoy-the-world::software.body.02-destination-section')
        @include('enjoy-the-world::software.body.03-benefit-section')

        {{-- === PLACES & ABOUT === --}}
        @include('enjoy-the-world::software.body.04-place-section')
        @include('enjoy-the-world::software.body.05-about-section')

        {{-- === EXPERTS & MEDIA === --}}
        @include('enjoy-the-world::software.body.06-experts-section')
        @include('enjoy-the-world::software.body.07-video-wrap')

        {{-- === CLIENTS & TESTIMONIALS === --}}
        @include('enjoy-the-world::software.body.08-client-section')
        @include('enjoy-the-world::software.body.09-testimonial-section')

        {{-- === UPDATES, FAQ, BLOG, SUPPORT === --}}
        @include('enjoy-the-world::software.body.10-update-section')
        @include('enjoy-the-world::software.body.11-faq-section')
        @include('enjoy-the-world::software.body.12-blog-section')
        @include('enjoy-the-world::software.body.13-support-section')


        {{-- =======================
            FOOTER
        ======================== --}}
        @include('enjoy-the-world::software.app-elements.footer')


        {{-- =======================
            MODALS
        ======================== --}}
        @include('enjoy-the-world::software.modals.login-modal')
        @include('enjoy-the-world::software.modals.register-modal')
        @include('enjoy-the-world::software.modals.change-password')
        @include('enjoy-the-world::software.modals.forgot-modal')


        {{-- =======================
            CURSOR & SCRIPTS
        ======================== --}}
        @include('enjoy-the-world::software.app-elements.cursor')
        @include('enjoy-the-world::software.app-elements.scripts')

    </body>
</html>
