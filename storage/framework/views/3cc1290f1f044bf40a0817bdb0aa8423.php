<?php
    use Webkernel\Aptitudes\WebsiteBuilder\Services\ThemeQuery;

    $projectId = $projectId ?? 1;
    $themeQuery = ThemeQuery::forProjectWithFallback($projectId);
?>

<!DOCTYPE html>
<html lang="<?php echo e($locale ?? app()->getLocale()); ?>"
    dir="<?php echo e($direction ?? (in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr')); ?>"
    x-bind:class="getHtmlClasses(theme)"
    x-bind:style="getInlineStyles(theme)"
    x-data="{
        primaryColor: '<?php echo e($primaryColor ?? 'blue'); ?>',
        theme: localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>',
        themeConfig: {
            useCustomProperties: true,
            initialColorMode: '<?php echo e($defaultTheme ?? 'system'); ?>',
            colors: <?php echo \Illuminate\Support\Js::from($themeQuery->getColors())->toHtml() ?>,
            modeConfiguration: <?php echo \Illuminate\Support\Js::from($themeQuery->getModeConfiguration())->toHtml() ?>
        },

        init() {
            // Initialize theme from Alpine store
            this.$watch('$store.theme', (value) => {
                this.theme = localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>';
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

<!-- Performance Marker: <?php echo e(microtime(true) - LARAVEL_START < 0.01 ? '<0.01µs' : round((microtime(true) - LARAVEL_START) * 1000000, 2) . 'µs'); ?> -->

<head>
    <?php if ($__env->exists('website-builder::live-website.layouts.partials.meta')) echo $__env->make('website-builder::live-website.layouts.partials.meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if ($__env->exists('website-builder::live-website.layouts.partials.styles')) echo $__env->make('website-builder::live-website.layouts.partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if ($__env->exists('website-builder::live-website.layouts.partials.head-scripts')) echo $__env->make('website-builder::live-website.layouts.partials.head-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('head'); ?>
    <?php echo $__env->yieldContent('head'); ?>
</head>

<body x-bind:class="getBodyClasses(theme)">
    <?php if ($__env->exists('website-builder::live-website.layouts.partials.loading')) echo $__env->make('website-builder::live-website.layouts.partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if ($__env->exists('enjoy-the-world::main')) echo $__env->make('enjoy-the-world::main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if ($__env->exists('website-builder::live-website.layouts.partials.scroll-top')) echo $__env->make('website-builder::live-website.layouts.partials.scroll-top', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if ($__env->exists('website-builder::live-website.layouts.partials.scripts')) echo $__env->make('website-builder::live-website.layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if ($__env->exists('website-builder::live-website.layouts.partials.debug')) echo $__env->make('website-builder::live-website.layouts.partials.debug', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/platform/EnjoyTheWorld/Resources/Views/site.blade.php ENDPATH**/ ?>