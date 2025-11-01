@php
use Webkernel\Aptitudes\UI\Resources\Views\components\MenuSimpleType\MenuSimpleType;

// Récupération de la configuration depuis un modèle Eloquent ou configuration par défaut
$defaultConfig = [
    'logoUrl' => '/',
    'logoImage' => 'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png',
    'logoText' => 'Numerimondes',
    'showSearch' => true,
    'showThemeToggle' => true,
    'showGitHub' => true,
    'githubUrl' => 'https://github.com/your-repo',
    'socialLinks' => [
        ['icon' => 'github', 'url' => 'https://github.com/your-repo', 'label' => 'GitHub'],
        ['icon' => 'youtube', 'url' => 'https://youtube.com/your-channel', 'label' => 'YouTube'],
        ['icon' => 'twitter', 'url' => 'https://twitter.com/your-account', 'label' => 'Twitter'],
    ],
    'navigationItems' => [
        [
            'title' => 'Documentation',
            'icon' => 'book-open',
            'submenu' => [
                [
                    'title' => 'Getting Started',
                    'href' => '/docs/getting-started',
                    'icon' => 'play-circle',
                    'submenu' => [
                        ['title' => 'Installation', 'href' => '/docs/getting-started/installation', 'icon' => 'download'],
                        ['title' => 'Quick Setup', 'href' => '/docs/getting-started/quick-setup', 'icon' => 'zap']
                    ]
                ],
                ['title' => 'Configuration', 'href' => '/docs/configuration', 'icon' => 'settings'],
                ['title' => 'Examples', 'href' => '/docs/examples', 'icon' => 'code']
            ]
        ],
        [
            'title' => 'Guides',
            'icon' => 'compass',
            'submenu' => [
                ['title' => 'Best Practices', 'href' => '/guides/best-practices', 'icon' => 'star'],
                ['title' => 'Troubleshooting', 'href' => '/guides/troubleshooting', 'icon' => 'life-buoy'],
                ['title' => 'Performance', 'href' => '/guides/performance', 'icon' => 'zap']
            ]
        ],
        [
            'title' => 'API Reference',
            'icon' => 'terminal',
            'submenu' => [
                ['title' => 'REST API', 'href' => '/api/rest', 'icon' => 'server'],
                ['title' => 'GraphQL', 'href' => '/api/graphql', 'icon' => 'layers'],
                ['title' => 'Webhooks', 'href' => '/api/webhooks', 'icon' => 'webhook']
            ]
        ]
    ]
];

$menuConfig = $defaultConfig;

// Initialize attributes if not provided (when included directly)
if (!isset($attributes)) {
    $attributes = new \Illuminate\View\ComponentAttributeBag();
}

$menu_simple_type = new MenuSimpleType(array_merge($attributes->getAttributes(), $menuConfig));
@endphp

<{{ $menu_simple_type->tag }} {!! $menu_simple_type->getAttributes() !!}
     x-data="{
        mobileMenuOpen: false,
        activeMobileMenu: null,
        activeDropdown: null,
        activeSubDropdown: null,
        theme: localStorage.getItem('theme') || '{{ $defaultTheme ?? 'system' }}',

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
     }">

    <div class="relative h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 mx-auto max-w-7xl">
            <!-- Left Side: Logo + Search -->
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ $menu_simple_type->logoUrl }}" class="flex items-center h-16 text-sm font-semibold text-foreground hover:text-primary transition-all duration-200">
                        <img src="{{ $menu_simple_type->logoImage }}" alt="{{ $menu_simple_type->logoText }} Logo" class="w-8 h-8 mr-3" />
                        <span class="text-sm font-bold">{{ $menu_simple_type->logoText }}</span>
                    </a>
                </div>

                <!-- Desktop Search -->
                @if($menu_simple_type->showSearch)
                <div class="hidden md:flex">
                    <button class="flex items-center bg-transparent hover:bg-muted text-foreground hover:text-primary h-10 px-4 font-medium text-sm rounded-md transition-all duration-200">
                        <x-lucide-search class="w-4 h-4 mr-2 text-muted-foreground" />
                        <span class="mr-3">Search</span>
                        <div class="hidden sm:flex items-center space-x-1">
                            <kbd class="h-5 px-1.5 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-xs">
                                <x-lucide-command class="w-3 h-3" />
                            </kbd>
                            <kbd class="h-5 px-1.5 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-xs">K</kbd>
                        </div>
                    </button>
                </div>
                @endif
            </div>

            <!-- Center: Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-8" aria-label="Main navigation">
                <!-- Home Link -->
                <a href="/" class="flex items-center text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md text-sm font-medium transition-all duration-200">
                    <x-lucide-home class="w-4 h-4 mr-2 text-muted-foreground" />
                    <span class="text-sm">Home</span>
                </a>

                @if(!empty($menuConfig['navigationItems']))
                    @foreach($menuConfig['navigationItems'] as $index => $item)
                     <!-- Navigation Item with Dropdown -->
                     <div class="relative">
                         <button
                             @click="activeDropdown = activeDropdown === '{{ $index }}' ? null : '{{ $index }}'"
                             @keydown.escape="activeDropdown = null"
                             class="flex items-center text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md text-sm font-medium transition-all duration-200">
                             @if(!empty($item['icon']))
                                 <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-4 h-4 mr-2 text-muted-foreground" />
                             @endif
                             <span class="text-sm">{{ $item['title'] ?? 'Menu Item' }}</span>
                             <x-lucide-chevron-down class="ml-1 w-4 h-4 text-muted-foreground transition-transform duration-200" x-bind:class="activeDropdown === '{{ $index }}' ? 'rotate-180' : ''" />
                         </button>

                         @if(!empty($item['submenu']))
                         <!-- Multi-level Dropdown -->
                         <div x-show="activeDropdown === '{{ $index }}'"
                              @click.away="activeDropdown = null"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 transform -translate-y-2"
                              x-transition:enter-end="opacity-100 transform translate-y-0"
                              x-transition:leave="transition ease-in duration-150"
                              x-transition:leave-start="opacity-100 transform translate-y-0"
                              x-transition:leave-end="opacity-0 transform -translate-y-2"
                              class="absolute top-full left-0 mt-2 w-64 bg-background border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50 backdrop-blur-sm">

                            @foreach($item['submenu'] as $subIndex => $subItem)
                                @if(!empty($subItem['submenu']))
                                    <!-- Sub-item with nested submenu -->
                                    <div class="relative">
                                        <div @click="activeSubDropdown = activeSubDropdown === '{{ $index }}-{{ $subIndex }}' ? null : '{{ $index }}-{{ $subIndex }}'"
                                            class="flex items-center justify-between px-4 py-3 text-xs font-medium text-foreground hover:text-primary hover:bg-primary/10 cursor-pointer transition-all duration-200 hover:shadow-sm">
                                            <div class="flex items-center">
                                                @if(!empty($subItem['icon']))
                                                    <x-dynamic-component :component="'lucide-' . $subItem['icon']" class="w-4 h-4 mr-3 text-muted-foreground" />
                                                @endif
                                                <span class="text-xs">{{ $subItem['title'] ?? 'Sub Item' }}</span>
                                            </div>
                                            <x-lucide-chevron-right class="w-4 h-4 text-muted-foreground transition-transform duration-200" x-bind:class="activeSubDropdown === '{{ $index }}-{{ $subIndex }}' ? 'rotate-90' : ''" />
                                        </div>

                                        <!-- Nested submenu -->
                                        <div x-show="activeSubDropdown === '{{ $index }}-{{ $subIndex }}'"
                                             @click.away="activeSubDropdown = null"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 transform -translate-x-2"
                                             x-transition:enter-end="opacity-100 transform translate-x-0"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 transform translate-x-0"
                                             x-transition:leave-end="opacity-0 transform -translate-x-2"
                                             class="absolute top-0 left-full ml-2 w-56 bg-background/95 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50 backdrop-blur-sm">
                                            @foreach($subItem['submenu'] as $nestedItem)
                                            <a href="{{ $nestedItem['href'] ?? '#' }}"
                                                class="flex items-center px-4 py-3 text-xs text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200 hover:shadow-sm">
                                                @if(!empty($nestedItem['icon']))
                                                    <x-dynamic-component :component="'lucide-' . $nestedItem['icon']" class="w-4 h-4 mr-3 text-muted-foreground" />
                                                @endif
                                                <span class="text-xs">{{ $nestedItem['title'] ?? 'Nested Item' }}</span>
                                            </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Simple sub-item -->
                                    <a href="{{ $subItem['href'] ?? '#' }}"
                                        class="flex items-center px-4 py-3 text-xs text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200 hover:shadow-sm">
                                        @if(!empty($subItem['icon']))
                                            <x-dynamic-component :component="'lucide-' . $subItem['icon']" class="w-4 h-4 mr-3 text-muted-foreground" />
                                        @endif
                                        <span class="text-xs">{{ $subItem['title'] ?? 'Sub Item' }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                @endif
            </nav>

            <!-- Right Side: Theme Toggle + Social Links + Mobile Menu -->
            <div class="flex items-center space-x-3 flex-shrink-0">
                <!-- Social Links -->
                @if(!empty($menuConfig['socialLinks']))
                <div class="hidden md:flex items-center space-x-1">
                    @foreach($menuConfig['socialLinks'] as $socialLink)
                    <a href="{{ $socialLink['url'] }}"
                        aria-label="{{ $socialLink['label'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center bg-transparent hover:bg-muted text-foreground hover:text-primary w-9 h-9 rounded-md transition-all duration-200">
                        <x-dynamic-component :component="'lucide-' . $socialLink['icon']" class="w-5 h-5 text-muted-foreground" />
                    </a>
                    @endforeach
                </div>
                @endif

                <!-- Separator -->
                @if($menu_simple_type->showThemeToggle && !empty($menuConfig['socialLinks']))
                <div class="w-px h-6 bg-border"></div>
                @endif

                <!-- Theme Switcher -->
                @if($menu_simple_type->showThemeToggle)
                <x-ui::ThemeSwitcher
                    variant="dropdown"
                    size="sm"
                    :show-labels="false"
                />
                @endif

                <!-- Mobile Menu Toggle -->
                <div class="lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="flex items-center justify-center bg-transparent hover:bg-muted text-foreground hover:text-primary w-10 h-10 rounded-md transition-all duration-200"
                        :aria-expanded="mobileMenuOpen"
                        aria-label="Toggle mobile navigation">
                        <x-lucide-menu x-show="!mobileMenuOpen" class="w-6 h-6 text-muted-foreground" />
                        <x-lucide-x x-show="mobileMenuOpen" class="w-6 h-6 text-muted-foreground" />
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
        class="lg:hidden bg-background/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-700">

        <div class="px-4 pt-4 pb-6 space-y-2 max-h-[70vh] overflow-y-auto">
            <!-- Mobile Search -->
            @if($menu_simple_type->showSearch)
            <div class="mb-4">
                <button class="flex items-center bg-transparent hover:bg-muted text-foreground hover:text-primary w-full h-12 px-4 font-medium text-sm rounded-md transition-all duration-200">
                    <x-lucide-search class="w-5 h-5 mr-3 text-muted-foreground" />
                    <span>Search</span>
                </button>
            </div>
            @endif

            <!-- Mobile Home Link -->
            <a href="/" class="flex items-center text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md text-sm font-medium transition-all duration-200">
                <x-lucide-home class="w-5 h-5 mr-3 text-muted-foreground" />
                <span class="text-sm">Home</span>
            </a>

            @if(!empty($menuConfig['navigationItems']))
                @foreach($menuConfig['navigationItems'] as $index => $item)
                <!-- Mobile Navigation Items -->
                <div>
                    <button @click="toggleMobileMenu({{ $index }})"
                        class="flex items-center justify-between text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md w-full font-medium transition-all duration-200"
                        x-bind:class="activeMobileMenu === {{ $index }} ? 'bg-muted' : ''"
                        data-index="{{ $index }}">
                        <div class="flex items-center">
                            @if(!empty($item['icon']))
                                <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-5 h-5 mr-3 text-muted-foreground" />
                            @endif
                            <span class="text-sm">{{ $item['title'] ?? 'Menu Item' }}</span>
                        </div>
                        <x-lucide-chevron-down class="w-5 h-5 text-muted-foreground transition-transform duration-200"
                            x-bind:class="activeMobileMenu === {{ $index }} ? 'rotate-180' : ''" />
                    </button>

                    @if(!empty($item['submenu']))
                    <div x-show="activeMobileMenu === {{ $index }}"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 ml-8 space-y-1">
                        @foreach($item['submenu'] as $subItem)
                        <a href="{{ $subItem['href'] ?? '#' }}"
                            class="flex items-center px-4 py-2 rounded-lg text-sm text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200">
                            @if(!empty($subItem['icon']))
                                <x-dynamic-component :component="'lucide-' . $subItem['icon']" class="w-4 h-4 mr-3 text-muted-foreground" />
                            @endif
                            <span class="text-xs">{{ $subItem['title'] ?? 'Sub Item' }}</span>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            @endif

            <!-- Mobile Theme Switcher -->
            @if($menu_simple_type->showThemeToggle)
            <div class="mt-4">
                <x-ui::ThemeSwitcher
                    variant="inline"
                    size="md"
                    :show-labels="true"
                />
            </div>
            @endif

            <!-- Mobile Social Links -->
            @if(!empty($menuConfig['socialLinks']))
            <div class="border-t border-border mt-4 pt-4">
                <div class="grid grid-cols-3 gap-2">
                    @foreach($menuConfig['socialLinks'] as $socialLink)
                    <a href="{{ $socialLink['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center px-4 py-3 rounded-lg text-xs font-medium text-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200">
                        <x-dynamic-component :component="'lucide-' . $socialLink['icon']" class="w-5 h-5 mr-2 text-muted-foreground" />
                        <span>{{ $socialLink['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</{{ $menu_simple_type->tag }}>
