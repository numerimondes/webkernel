<!DOCTYPE html>
<html lang="fr" x-data="{ theme: $persist('dark') }" :class="{ 'dark': theme === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Responsive</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'nav-bg': '#1b1b1f',
                        'nav-text': '#f9fafb',
                        'nav-text-secondary': '#9ca3af',
                        'nav-hover': '#374151',
                        'search-bg': '#0f0f11',
                        'border-color': '#4b5563'
                    }
                }
            }
        }
    </script>
</head>
<div class="bg-gray-100 dark:bg-gray-900 transition-colors duration-300">

<div x-data="navigationData()">
    <header class="fixed top-0 left-0 z-30 w-full text-nav-text font-sans bg-nav-bg">
        <div class="relative h-16 whitespace-nowrap px-4 md:px-8">
            <div class="flex justify-between h-16 mx-auto max-w-7xl">
                <!-- Left Side -->
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0">
                        <a :href="brand.url" class="flex items-center h-16 text-sm font-semibold text-nav-text">
                            <img :src="brand.logo" :alt="brand.name + ' Logo'" class="max-w-10 mr-2 h-6" />
                            <span class="text-[16px]" x-text="brand.name"></span>
                        </a>
                    </div>

                    <!-- Desktop Search -->
                    <div class="hidden md:flex items-center flex-grow pl-8" x-show="search.enabled">
                        <div class="rounded-lg bg-search-bg">
                            <button class="flex items-center h-10 px-3.5 rounded-lg bg-transparent text-nav-text-secondary hover:text-nav-text font-medium text-sm transition-colors">
                                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="mr-2" x-text="search.placeholder"></span>
                                <span class="flex items-center">
                                    <div class="flex bg-gray-900">
                                        <kbd class="flex items-center justify-center bg-transparent border border-border-color border-r-0 rounded-l-md shadow-sm text-nav-text-secondary px-1.5 py-0 h-5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3"></path>
                                            </svg>
                                        </kbd>
                                        <kbd class="flex items-center justify-center bg-transparent border border-border-color rounded-r-md shadow-sm text-nav-text-secondary px-1.5 py-0 text-sm h-5">
                                            K
                                        </kbd>
                                    </div>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex justify-between h-16">
                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center" aria-label="Main navigation">
                        <!-- Home Link -->
                        <a :href="homeLink.url"
                           class="flex items-center px-3 h-16 text-sm font-medium transition-colors"
                           :class="homeLink.active ? 'text-indigo-600 hover:text-indigo-500' : 'text-nav-text hover:text-gray-100'">
                            <span x-text="homeLink.title"></span>
                        </a>

                        <!-- Visible Navigation Items -->
                        <template x-for="(item, index) in visibleItems" :key="index">
                            <div class="relative group">
                                <button class="flex items-center px-3 h-16 text-sm font-medium text-nav-text hover:text-gray-100 transition-colors">
                                    <span x-text="item.title"></span>
                                    <svg class="ml-1 w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>

                                <!-- Submenu -->
                                <div x-show="item.submenu && item.submenu.length > 0"
                                     class="absolute top-full left-0 mt-1 w-48 bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <template x-for="subItem in item.submenu" :key="subItem.title">
                                        <a :href="subItem.url"
                                           class="block px-3 py-2 text-sm font-medium text-nav-text hover:bg-nav-hover hover:text-gray-100 rounded-md transition-colors"
                                           x-text="subItem.title">
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- More Dropdown -->
                        <div x-show="hiddenItems.length > 0" class="relative group">
                            <button class="flex items-center px-3 h-16 text-sm font-medium text-nav-text hover:text-gray-100 transition-colors">
                                <span x-text="moreButton.title"></span>
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div class="absolute top-full left-0 mt-1 w-64 bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-h-96 overflow-y-auto">
                                <template x-for="item in hiddenItems" :key="item.title">
                                    <div class="border-b border-gray-700 last:border-b-0">
                                        <div class="px-3 py-2 text-xs font-semibold text-nav-text-secondary uppercase tracking-wider" x-text="item.title"></div>
                                        <template x-for="subItem in item.submenu" :key="subItem.title">
                                            <a :href="subItem.url"
                                               class="block px-3 py-2 text-sm font-medium text-nav-text hover:bg-nav-hover hover:text-gray-100 rounded-md transition-colors"
                                               x-text="subItem.title">
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </nav>

                    <!-- Theme Toggle -->
                    <div x-show="themeToggle.enabled" class="hidden md:flex items-center">
                        <div class="w-px h-6 bg-border-color mx-4"></div>
                        <button @click="toggleTheme()"
                                class="w-10 h-6 rounded-full border border-border-color bg-gray-700 relative transition-colors duration-200 hover:bg-gray-600">
                            <div class="w-4 h-4 bg-white rounded-full absolute top-0.5 transition-all duration-200 flex items-center justify-center"
                                 :class="theme === 'dark' ? 'translate-x-4' : 'translate-x-0.5'">
                                <svg x-show="theme === 'light'" class="w-2.5 h-2.5 text-gray-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                <svg x-show="theme === 'dark'" class="w-2.5 h-2.5 text-gray-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- GitHub Link -->
                    <div x-show="socialLinks.github.enabled" class="hidden md:flex items-center">
                        <div class="w-px h-6 bg-border-color mx-4"></div>
                        <a :href="socialLinks.github.url"
                           :aria-label="socialLinks.github.label"
                           target="_blank"
                           rel="noopener"
                           class="flex justify-center items-center w-9 h-9 text-nav-text-secondary hover:text-nav-text transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <div class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                                type="button"
                                aria-label="Toggle mobile navigation"
                                :aria-expanded="mobileMenuOpen"
                                class="flex justify-center items-center w-10 h-10 text-nav-text hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-inset rounded-md transition-colors">
                            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="md:hidden bg-nav-bg border-t border-border-color">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <!-- Mobile Search -->
                <div x-show="search.enabled" class="mb-4">
                    <div class="rounded-lg bg-search-bg">
                        <button class="flex items-center w-full h-10 px-3.5 rounded-lg bg-transparent text-nav-text-secondary hover:text-nav-text font-medium text-sm transition-colors">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span x-text="search.placeholder"></span>
                        </button>
                    </div>
                </div>

                <!-- Mobile Home Link -->
                <a :href="homeLink.url"
                   class="block px-3 py-2 rounded-md text-base font-medium transition-colors"
                   :class="homeLink.active ? 'text-indigo-600 hover:text-indigo-500 hover:bg-gray-800' : 'text-nav-text hover:text-gray-100 hover:bg-gray-800'">
                    <span x-text="homeLink.title"></span>
                </a>

                <!-- Mobile Navigation Items -->
                <template x-for="item in allItems" :key="item.title">
                    <div>
                        <button @click="item.expanded = !item.expanded"
                                class="flex items-center justify-between w-full px-3 py-2 rounded-md text-base font-medium text-nav-text hover:text-gray-100 hover:bg-gray-800 transition-colors">
                            <span x-text="item.title"></span>
                            <svg class="w-4 h-4 transition-transform duration-200"
                                 :class="item.expanded ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="item.expanded"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-1"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="mt-2 ml-4 space-y-1">
                            <template x-for="subItem in item.submenu" :key="subItem.title">
                                <a :href="subItem.url"
                                   class="block px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-gray-100 hover:bg-gray-800 transition-colors"
                                   x-text="subItem.title">
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Mobile Theme Toggle -->
                <div x-show="themeToggle.enabled" class="flex items-center justify-between px-3 py-2">
                    <span class="text-base font-medium text-nav-text" x-text="themeToggle.label"></span>
                    <button @click="toggleTheme()"
                            class="w-10 h-6 rounded-full border border-border-color bg-gray-700 relative transition-colors">
                        <div class="w-4 h-4 bg-white rounded-full absolute top-0.5 transition-all duration-200"
                             :class="theme === 'dark' ? 'translate-x-4' : 'translate-x-0.5'">
                        </div>
                    </button>
                </div>

                <!-- Mobile GitHub Link -->
                <a x-show="socialLinks.github.enabled"
                   :href="socialLinks.github.url"
                   target="_blank"
                   rel="noopener"
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium text-nav-text hover:text-gray-100 hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3"></path>
                    </svg>
                    <span x-text="socialLinks.github.label"></span>
                </a>
            </div>
        </div>

        <div class="w-full h-px border-t border-border-color"></div>
    </header>

    <!-- Demo Content -->
    <main class="pt-16 min-h-screen">
        <div class="container mx-auto px-4 md:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Navigation Responsive</h1>
            <p class="text-gray-600 dark:text-gray-400">
                Cette navigation s'adapte automatiquement selon la taille de l'écran, avec un système de "More" dropdown pour les éléments cachés.
            </p>

            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-blue-800 dark:text-blue-200 font-semibold mb-2">Fonctionnalités implémentées :</p>
                <ul class="text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                    <li>Navigation responsive avec Alpine.js</li>
                    <li>Système de "More" dropdown automatique</li>
                    <li>Menu mobile avec animations</li>
                    <li>Toggle thème persistant</li>
                    <li>Configuration via props/data</li>
                    <li>Ajustement automatique au redimensionnement</li>
                    <li>Préservation du style original</li>
                </ul>
            </div>

            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-green-800 dark:text-green-200 font-semibold mb-2">Configuration personnalisable :</p>
                <ul class="text-green-700 dark:text-green-300 list-disc list-inside space-y-1">
                    <li>Nombre d'éléments visibles par taille d'écran</li>
                    <li>Couleurs et styles via Tailwind CSS</li>
                    <li>Éléments de navigation dynamiques</li>
                    <li>Activation/désactivation des fonctionnalités</li>
                </ul>
            </div>

            <!-- Test content for scrolling -->
            <div class="mt-12 space-y-8">
                <template x-for="i in 10" :key="i">
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4" x-text="`Section ${i}`"></h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Contenu de test pour vérifier le comportement de la navigation fixe. Redimensionnez la fenêtre pour voir l'adaptation automatique.
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </main>
</div>

<script>
function navigationData() {
    return {
        // === CONFIGURATION PROPS ===
        // Brand configuration
        brand: {
            name: 'Numerimondes',
            logo: 'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png',
            url: '/'
        },

        // Home link configuration
        homeLink: {
            title: 'Home',
            url: '/',
            active: true
        },

        // Search configuration
        search: {
            enabled: true,
            placeholder: 'Search'
        },

        // Theme toggle configuration
        themeToggle: {
            enabled: true,
            label: 'Dark Mode'
        },

        // Social links configuration
        socialLinks: {
            github: {
                enabled: true,
                url: 'https://github.com/numerimondes',
                label: 'GitHub'
            }
        },

        // More button configuration
        moreButton: {
            title: 'More'
        },

        // Navigation items data
        allItems: [
            {
                title: 'Documentation 1',
                url: '/doc1',
                expanded: false,
                submenu: [
                    { title: 'Getting Started', url: '/doc1/start' },
                    { title: 'Installation', url: '/doc1/install' },
                ]
            },
            {
                title: 'Documentation 4',
                url: '/doc4',
                expanded: false,
                submenu: [
                    { title: 'Guides', url: '/doc4/guides' },
                    { title: 'Best Practices', url: '/doc4/best-practices' },
                ]
            },
            {
                title: 'Documentation 5',
                url: '/doc5',
                expanded: false,
                submenu: [
                    { title: 'Advanced Topics', url: '/doc5/advanced' },
                    { title: 'Troubleshooting', url: '/doc5/troubleshooting' },
                ]
            }
        ],

        // Responsive configuration
        responsiveConfig: {
            sm: 2,   // 640px+
            md: 3,   // 768px+
            lg: 4,   // 1024px+
            xl: 5,   // 1280px+
            '2xl': 6 // 1536px+
        },

        // === STATE ===
        mobileMenuOpen: false,
        currentBreakpoint: 'lg',
        visibleItemsCount: 4,

        // === COMPUTED PROPERTIES ===
        get visibleItems() {
            return this.allItems.slice(0, this.visibleItemsCount);
        },

        get hiddenItems() {
            return this.allItems.slice(this.visibleItemsCount);
        },

        // === METHODS ===
        init() {
            this.updateResponsiveLayout();
            this.setupResizeListener();
        },

        updateResponsiveLayout() {
            const width = window.innerWidth;
            let newCount = 4; // default

            if (width >= 1536) {
                newCount = this.responsiveConfig['2xl'];
                this.currentBreakpoint = '2xl';
            } else if (width >= 1280) {
                newCount = this.responsiveConfig.xl;
                this.currentBreakpoint = 'xl';
            } else if (width >= 1024) {
                newCount = this.responsiveConfig.lg;
                this.currentBreakpoint = 'lg';
            } else if (width >= 768) {
                newCount = this.responsiveConfig.md;
                this.currentBreakpoint = 'md';
            } else {
                newCount = this.responsiveConfig.sm;
                this.currentBreakpoint = 'sm';
            }

            this.visibleItemsCount = Math.min(newCount, this.allItems.length);
        },

        setupResizeListener() {
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    this.updateResponsiveLayout();
                }, 100);
            });
        },

        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
        },

        // === UTILITY METHODS FOR LIVEWIRE INTEGRATION ===
        addNavigationItem(item) {
            this.allItems.push({
                ...item,
                expanded: false
            });
            this.updateResponsiveLayout();
        },

        removeNavigationItem(index) {
            if (index >= 0 && index < this.allItems.length) {
                this.allItems.splice(index, 1);
                this.updateResponsiveLayout();
            }
        },

        updateNavigationItem(index, item) {
            if (index >= 0 && index < this.allItems.length) {
                this.allItems[index] = {
                    ...this.allItems[index],
                    ...item
                };
            }
        }
    };
}
</script>

</div>
