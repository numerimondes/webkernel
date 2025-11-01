<?php
use Webkernel\Aptitudes\WebsiteBuilder\Services\ThemeQuery;

$siteId = $siteId ?? 'default';
$projectId = $projectId ?? 1;

$themeQuery = ThemeQuery::forProjectWithFallback($projectId);
$cssContent = $themeQuery->getCssContent();

// Register only essential theme data
$themeQuery->registerMinimalHelpers($siteId);
?>

<?php if(!($disableGoogleFonts ?? false)): ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100..900;1,100..900&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
<?php endif; ?>

<?php echo app('Illuminate\Foundation\Vite')(['resources/css/webkernel.css', 'resources/js/webkernel.js']); ?>

<style><?php echo $cssContent; ?></style>

<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                    colors: {
                        primary: 'var(--color-primary)',
                        secondary: 'var(--color-secondary)',
                        accent: 'var(--color-accent)',
                        danger: 'var(--color-danger)',
                        warning: 'var(--color-warning)',
                        success: 'var(--color-success)',
                        info: 'var(--color-info)',
                        text: 'var(--color-text)',
                        background: 'var(--color-background)',
                        muted: 'var(--color-muted)',
                        highlight: 'var(--color-highlight)',
                        gray: 'var(--color-gray)',
                        theme: {
                            DEFAULT: 'var(--color-background)',
                            secondary: 'var(--color-muted)',
                            tertiary: 'var(--color-highlight)',
                            elevated: 'var(--color-background)',
                        },
                        'theme-text': {
                            DEFAULT: 'var(--color-text)',
                            secondary: 'var(--color-gray)',
                            muted: 'var(--color-gray)',
                            accent: 'var(--color-accent)',
                        },
                        'theme-border': {
                            DEFAULT: 'var(--color-muted)',
                            secondary: 'var(--color-gray)',
                        }
                    },
                fontFamily: {
                    sans: ['Inter', 'system-ui', 'sans-serif'],
                    mono: ['JetBrains Mono', 'Monaco', 'Consolas', 'monospace']
                },
                    spacing: { '18': '4.5rem', '88': '22rem', '128': '32rem' },
                    borderRadius: { '4xl': '2rem' },
                boxShadow: {
                    'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                    'strong': '0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                        'theme': 'var(--theme-shadow)',
                        'theme-lg': 'var(--theme-shadow-lg)',
                        'theme-xl': 'var(--theme-shadow-xl)',
                },
                animation: {
                    'fade-in': 'fadeIn 0.3s ease-out',
                    'slide-up': 'slideUp 0.2s ease-out',
                    'slide-down': 'slideDown 0.2s ease-out',
                    'pulse-slow': 'pulse 3s infinite',
                    'bounce-gentle': 'bounceGentle 2s infinite'
                }
            }
        }
        };

// Theme system is handled by Alpine.js in scripts.blade.php
// This ensures instant theme switching
</script>
<!---YAYA----->
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/WebsiteBuilder/Resources/Views/live-website/layouts/partials/styles.blade.php ENDPATH**/ ?>