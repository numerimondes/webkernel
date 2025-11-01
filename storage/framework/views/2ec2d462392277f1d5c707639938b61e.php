<?php
use Webkernel\Aptitudes\WebsiteBuilder\Services\ThemeQuery;

$siteId = $siteId ?? 'default';
$projectId = $projectId ?? 1;

$themeQuery = ThemeQuery::forProjectWithFallback($projectId);
?>


<script>
    // Immediate theme initialization to prevent FOUC
    (function() {
        const savedTheme = localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>';
        const effectiveTheme = savedTheme === 'system'
            ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
            : savedTheme;

        document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
        document.documentElement.style.colorScheme = effectiveTheme;
    })();
</script>


<style>
    /* Theme switcher button styles - compatible with Filament */
    .fi-theme-switcher-btn {
        position: relative;
        overflow: hidden;
    }

    .fi-theme-switcher-btn.fi-active {
        /* No special styling for active state */
    }

    .fi-theme-switcher-btn:hover {
        transform: translateY(-1px);
    }

    /* Instant theme changes - no transitions for better UX */
    * {
        /* Remove transitions during theme changes */
        transition: none !important;
    }

    /* Re-enable transitions after theme change */
    *:not(.theme-changing) {
        transition: background-color 0.15s ease-out, border-color 0.15s ease-out, color 0.15s ease-out;
    }

    /* Prevent flash of unstyled content */
    html {
        color-scheme: light dark;
    }

    /* Dark mode base styles */
    html.dark {
        color-scheme: dark;
    }

    /* Theme switcher animations */
    @keyframes theme-switch {
        0% { opacity: 0.8; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }

    .fi-theme-switcher-btn:active {
        animation: theme-switch 0.15s ease-out;
    }

    /* Loading state for theme changes */
    .theme-loading {
        pointer-events: none;
        opacity: 0.7;
    }

    /* System theme indicator */
    .system-theme-indicator::after {
        content: '🖥️';
        position: absolute;
        top: -2px;
        right: -2px;
        font-size: 10px;
        background: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5e7eb;
    }

    html.dark .system-theme-indicator::after {
        background: #374151;
        border-color: #4b5563;
    }
</style>


<script>
        // Initialize Alpine.js theme store with multi-mode support
        document.addEventListener('alpine:init', () => {
            const defaultTheme = '<?php echo e($defaultTheme ?? 'system'); ?>';
            const theme = localStorage.getItem('theme') ?? defaultTheme;

            // Available modes from theme configuration
            const availableModes = <?php echo \Illuminate\Support\Js::from($themeQuery->getTheme() ? $themeQuery->getTheme()->getAvailableModes() : ['light', 'dark'])->toHtml() ?>;

            // Determine effective theme
            let effectiveTheme = theme;
            if (theme === 'system') {
                effectiveTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            window.Alpine.store('theme', effectiveTheme);

            window.addEventListener('theme-changed', (event) => {
                let theme = event.detail;

                localStorage.setItem('theme', theme);

                // Determine effective theme
                if (theme === 'system') {
                    theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }

                window.Alpine.store('theme', theme);
            });

            window
                .matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', (event) => {
                    if (localStorage.getItem('theme') === 'system') {
                        window.Alpine.store('theme', event.matches ? 'dark' : 'light');
                    }
                });

            window.Alpine.effect(() => {
                const theme = window.Alpine.store('theme');

                // Remove all mode classes
                availableModes.forEach(mode => {
                    if (mode !== 'light' && mode !== 'system') {
                        document.documentElement.classList.remove(mode);
                    }
                });

                // Add current theme class
                if (theme !== 'light') {
                    document.documentElement.classList.add(theme);
                }
            });
        });

    // Global utilities
    window.app = {
        theme: localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>',
        locale: '<?php echo e(app()->getLocale()); ?>',
        csrfToken: '<?php echo e(csrf_token()); ?>',
        baseUrl: '<?php echo e(url('/')); ?>',

        // Theme utilities - compatible with Filament system
        setTheme(theme) {
            // Apply theme immediately for instant feedback
            const effectiveTheme = theme === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : theme;

            // Disable transitions for instant change
            document.documentElement.classList.add('theme-changing');
            document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
            document.documentElement.style.colorScheme = effectiveTheme;

            // Re-enable transitions
            requestAnimationFrame(() => {
                document.documentElement.classList.remove('theme-changing');
            });

            this.theme = theme;
            localStorage.setItem('theme', theme);

            // Update Alpine store immediately
            if (window.Alpine && window.Alpine.store) {
                window.Alpine.store('theme', effectiveTheme);
            }

            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: theme }));

            // Show notification (delayed to not interfere with theme change)
            setTimeout(() => {
                const themeLabels = {
                    'light': 'Light',
                    'dark': 'Dark',
                    'system': 'System'
                };
                this.notify(`Switched to ${themeLabels[theme]} theme`, 'success', 1500);
            }, 50);

            return theme;
        },

        toggleTheme() {
            const currentTheme = localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>';
            let newTheme;

            // Cycle through: light -> dark -> system -> light
            switch (currentTheme) {
                case 'light':
                    newTheme = 'dark';
                    break;
                case 'dark':
                    newTheme = 'system';
                    break;
                default:
                    newTheme = 'light';
                    break;
            }

            return this.setTheme(newTheme);
        },

        // Navigation utilities - Reusable for all navigation components
        nav: {
            // Get navigation x-data object (compatible with Filament theme system)
            getXData() {
                return {
                    mobileMenuOpen: false,
                    activeMobileMenu: null,
                    activeDropdown: null,
                    activeSubDropdown: null,
                    theme: localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>',

                    init() {
                        // Initialize theme from Alpine store if available
                        this.$watch('$store.theme', (value) => {
                            this.theme = localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>';
                        });

                        // Listen for theme changes
                        window.addEventListener('theme-changed', (event) => {
                            this.theme = event.detail;
                        });
                    },

                    toggleMobileMenu(key) {
                        this.activeMobileMenu = this.activeMobileMenu === key ? null : key;
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
                };
            },

            // Get navigation x-init string
            getXInit() {
                return `
                    this.theme = localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>';
                `;
            },

        },

        // Notification system
        notify(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className =
                `fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-strong max-w-md animate-slide-down ${this.getNotificationClasses(type)}`;
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-current opacity-70 hover:opacity-100">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);

            if (duration > 0) {
                setTimeout(() => notification.remove(), duration);
            }
        },

        getNotificationClasses(type) {
            const classes = {
                success: 'bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700 text-success-700 dark:text-success-300',
                error: 'bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-700 text-danger-700 dark:text-danger-300',
                warning: 'bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 text-warning-700 dark:text-warning-300',
                info: 'bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-700 text-info-700 dark:text-info-300'
            };
            return classes[type] || classes.info;
        },

        // AJAX utilities
        async request(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            const response = await fetch(url, {
                ...defaultOptions,
                ...options
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        }
    };

    // DOM Ready utilities
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure theme is synchronized with localStorage
        const savedTheme = localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>';
        app.theme = savedTheme;

        // Initialize theme immediately to prevent flash
        const effectiveTheme = savedTheme === 'system'
            ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
            : savedTheme;

        // Apply theme instantly without transitions
        document.documentElement.classList.add('theme-changing');
        document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
        document.documentElement.style.colorScheme = effectiveTheme;

        // Remove transition class after a frame
        requestAnimationFrame(() => {
            document.documentElement.classList.remove('theme-changing');
        });

        // Auto-hide flash messages
        const flashMessages = document.querySelectorAll('[role="alert"]');
        flashMessages.forEach(message => {
            const closeButton = message.querySelector('button');
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    message.style.animation = 'slideUp 0.3s ease-out forwards';
                    setTimeout(() => message.remove(), 300);
                });
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Form validation feedback
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-danger-500');
                        isValid = false;
                    } else {
                        field.classList.remove('border-danger-500');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    app.notify('Please fill in all required fields', 'error');
                }
            });
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Toggle theme with Ctrl/Cmd + Shift + T
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
            e.preventDefault();
            const newTheme = app.toggleTheme();
        }

        // Direct theme selection with Ctrl/Cmd + Shift + 1/2/3
        if ((e.ctrlKey || e.metaKey) && e.shiftKey) {
            switch(e.key) {
                case '1':
                    e.preventDefault();
                    app.setTheme('light');
                    break;
                case '2':
                    e.preventDefault();
                    app.setTheme('dark');
                    break;
                case '3':
                    e.preventDefault();
                    app.setTheme('system');
                    break;
            }
        }
    });

    // Performance monitoring
    window.addEventListener('load', () => {
        // Report page load performance
        if ('performance' in window) {
            const perfData = performance.getEntriesByType('navigation')[0];
            const loadTime = perfData.loadEventEnd - perfData.fetchStart;

            if (loadTime > 3000) {
                console.warn('Slow page load detected:', loadTime + 'ms');
            }

            <?php if(app()->environment('local')): ?>
                console.log('Performance metrics:', {
                    'Page Load Time': Math.round(loadTime) + 'ms',
                    'DOMContentLoaded': Math.round(perfData.domContentLoadedEventEnd - perfData
                        .fetchStart) + 'ms',
                    'First Paint': performance.getEntriesByType('paint').find(p => p.name ===
                        'first-paint')?.startTime + 'ms' || 'N/A'
                });
            <?php endif; ?>
        }
    });
</script>


<?php if(isset($googleTagManagerId)): ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($googleTagManagerId); ?>" height="0"
            width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>


<?php if(isset($enablePWA) && $enablePWA): ?>
    <script>
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            // Show custom install button
            const installButton = document.getElementById('pwa-install-button');
            if (installButton) {
                installButton.style.display = 'block';
                installButton.addEventListener('click', () => {
                    installButton.style.display = 'none';
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the PWA install prompt');
                        }
                        deferredPrompt = null;
                    });
                });
            }
        });

        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed');
            app.notify('App installed successfully!', 'success');
        });
    </script>
<?php endif; ?>


<script>
    // Skip link functionality
    const skipLink = document.querySelector('.skip-link');
    if (skipLink) {
        skipLink.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector(skipLink.getAttribute('href'));
            if (target) {
                target.focus();
                target.scrollIntoView();
            }
        });
    }

    // Focus management for modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.querySelector('[data-modal].active');
            if (modal) {
                const closeButton = modal.querySelector('[data-modal-close]');
                if (closeButton) closeButton.click();
            }
        }
    });

    // Announce route changes to screen readers
    let currentPath = window.location.pathname;
    new MutationObserver(() => {
        if (window.location.pathname !== currentPath) {
            currentPath = window.location.pathname;
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = `Navigated to ${document.title}`;
            document.body.appendChild(announcement);
            setTimeout(() => announcement.remove(), 1000);
        }
    }).observe(document.querySelector('title'), {
        childList: true
    });
</script>


<script>
    window.addEventListener('error', (e) => {
        console.error('JavaScript error caught:', e.error);

        <?php if(app()->environment('local')): ?>
            app.notify(`JavaScript Error: ${e.error.message}`, 'error', 10000);
        <?php else: ?>
            // In production, you might want to send errors to a logging service
            // Example: Sentry, LogRocket, etc.
        <?php endif; ?>
    });

    window.addEventListener('unhandledrejection', (e) => {
        console.error('Unhandled promise rejection:', e.reason);

        <?php if(app()->environment('local')): ?>
            app.notify(`Promise rejection: ${e.reason}`, 'error', 10000);
        <?php endif; ?>
    });
</script>


<?php echo $__env->yieldPushContent('body-scripts'); ?>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/WebsiteBuilder/Resources/Views/live-website/layouts/partials/scripts.blade.php ENDPATH**/ ?>