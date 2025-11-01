<?php
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
?>

<<?php echo e($menu_simple_type->tag); ?> <?php echo $menu_simple_type->getAttributes(); ?>

     x-data="{
        mobileMenuOpen: false,
        activeMobileMenu: null,
        activeDropdown: null,
        activeSubDropdown: null,
        theme: localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'system'); ?>',

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
                    <a href="<?php echo e($menu_simple_type->logoUrl); ?>" class="flex items-center h-16 text-sm font-semibold text-foreground hover:text-primary transition-all duration-200">
                        <img src="<?php echo e($menu_simple_type->logoImage); ?>" alt="<?php echo e($menu_simple_type->logoText); ?> Logo" class="w-8 h-8 mr-3" />
                        <span class="text-sm font-bold"><?php echo e($menu_simple_type->logoText); ?></span>
                    </a>
                </div>

                <!-- Desktop Search -->
                <?php if($menu_simple_type->showSearch): ?>
                <div class="hidden md:flex">
                    <button class="flex items-center bg-transparent hover:bg-muted text-foreground hover:text-primary h-10 px-4 font-medium text-sm rounded-md transition-all duration-200">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-2 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        <span class="mr-3">Search</span>
                        <div class="hidden sm:flex items-center space-x-1">
                            <kbd class="h-5 px-1.5 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-xs">
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-command'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3 h-3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                            </kbd>
                            <kbd class="h-5 px-1.5 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-xs">K</kbd>
                        </div>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Center: Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-8" aria-label="Main navigation">
                <!-- Home Link -->
                <a href="/" class="flex items-center text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md text-sm font-medium transition-all duration-200">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-2 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    <span class="text-sm">Home</span>
                </a>

                <?php if(!empty($menuConfig['navigationItems'])): ?>
                    <?php $__currentLoopData = $menuConfig['navigationItems']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                     <!-- Navigation Item with Dropdown -->
                     <div class="relative">
                         <button
                             @click="activeDropdown = activeDropdown === '<?php echo e($index); ?>' ? null : '<?php echo e($index); ?>'"
                             @keydown.escape="activeDropdown = null"
                             class="flex items-center text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md text-sm font-medium transition-all duration-200">
                             <?php if(!empty($item['icon'])): ?>
                                 <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $item['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-2 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                             <?php endif; ?>
                             <span class="text-sm"><?php echo e($item['title'] ?? 'Menu Item'); ?></span>
                             <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'ml-1 w-4 h-4 text-muted-foreground transition-transform duration-200','x-bind:class' => 'activeDropdown === \''.e($index).'\' ? \'rotate-180\' : \'\'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                         </button>

                         <?php if(!empty($item['submenu'])): ?>
                         <!-- Multi-level Dropdown -->
                         <div x-show="activeDropdown === '<?php echo e($index); ?>'"
                              @click.away="activeDropdown = null"
                              x-transition:enter="transition ease-out duration-200"
                              x-transition:enter-start="opacity-0 transform -translate-y-2"
                              x-transition:enter-end="opacity-100 transform translate-y-0"
                              x-transition:leave="transition ease-in duration-150"
                              x-transition:leave-start="opacity-100 transform translate-y-0"
                              x-transition:leave-end="opacity-0 transform -translate-y-2"
                              class="absolute top-full left-0 mt-2 w-64 bg-background border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50 backdrop-blur-sm">

                            <?php $__currentLoopData = $item['submenu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subIndex => $subItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($subItem['submenu'])): ?>
                                    <!-- Sub-item with nested submenu -->
                                    <div class="relative">
                                        <div @click="activeSubDropdown = activeSubDropdown === '<?php echo e($index); ?>-<?php echo e($subIndex); ?>' ? null : '<?php echo e($index); ?>-<?php echo e($subIndex); ?>'"
                                            class="flex items-center justify-between px-4 py-3 text-xs font-medium text-foreground hover:text-primary hover:bg-primary/10 cursor-pointer transition-all duration-200 hover:shadow-sm">
                                            <div class="flex items-center">
                                                <?php if(!empty($subItem['icon'])): ?>
                                                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $subItem['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-3 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                                <?php endif; ?>
                                                <span class="text-xs"><?php echo e($subItem['title'] ?? 'Sub Item'); ?></span>
                                            </div>
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-chevron-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-muted-foreground transition-transform duration-200','x-bind:class' => 'activeSubDropdown === \''.e($index).'-'.e($subIndex).'\' ? \'rotate-90\' : \'\'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                        </div>

                                        <!-- Nested submenu -->
                                        <div x-show="activeSubDropdown === '<?php echo e($index); ?>-<?php echo e($subIndex); ?>'"
                                             @click.away="activeSubDropdown = null"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 transform -translate-x-2"
                                             x-transition:enter-end="opacity-100 transform translate-x-0"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100 transform translate-x-0"
                                             x-transition:leave-end="opacity-0 transform -translate-x-2"
                                             class="absolute top-0 left-full ml-2 w-56 bg-background/95 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50 backdrop-blur-sm">
                                            <?php $__currentLoopData = $subItem['submenu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nestedItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e($nestedItem['href'] ?? '#'); ?>"
                                                class="flex items-center px-4 py-3 text-xs text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200 hover:shadow-sm">
                                                <?php if(!empty($nestedItem['icon'])): ?>
                                                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $nestedItem['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-3 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                                <?php endif; ?>
                                                <span class="text-xs"><?php echo e($nestedItem['title'] ?? 'Nested Item'); ?></span>
                                            </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Simple sub-item -->
                                    <a href="<?php echo e($subItem['href'] ?? '#'); ?>"
                                        class="flex items-center px-4 py-3 text-xs text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200 hover:shadow-sm">
                                        <?php if(!empty($subItem['icon'])): ?>
                                            <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $subItem['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-3 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                        <?php endif; ?>
                                        <span class="text-xs"><?php echo e($subItem['title'] ?? 'Sub Item'); ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </nav>

            <!-- Right Side: Theme Toggle + Social Links + Mobile Menu -->
            <div class="flex items-center space-x-3 flex-shrink-0">
                <!-- Social Links -->
                <?php if(!empty($menuConfig['socialLinks'])): ?>
                <div class="hidden md:flex items-center space-x-1">
                    <?php $__currentLoopData = $menuConfig['socialLinks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $socialLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($socialLink['url']); ?>"
                        aria-label="<?php echo e($socialLink['label']); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center bg-transparent hover:bg-muted text-foreground hover:text-primary w-9 h-9 rounded-md transition-all duration-200">
                        <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $socialLink['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <!-- Separator -->
                <?php if($menu_simple_type->showThemeToggle && !empty($menuConfig['socialLinks'])): ?>
                <div class="w-px h-6 bg-border"></div>
                <?php endif; ?>

                <!-- Theme Switcher -->
                <?php if($menu_simple_type->showThemeToggle): ?>
                <?php if (isset($component)) { $__componentOriginal72158f852ce7e00889093c0f4f47e953 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal72158f852ce7e00889093c0f4f47e953 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'ui::components.ThemeSwitcher.index','data' => ['variant' => 'dropdown','size' => 'sm','showLabels' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui::ThemeSwitcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'dropdown','size' => 'sm','show-labels' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal72158f852ce7e00889093c0f4f47e953)): ?>
<?php $attributes = $__attributesOriginal72158f852ce7e00889093c0f4f47e953; ?>
<?php unset($__attributesOriginal72158f852ce7e00889093c0f4f47e953); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal72158f852ce7e00889093c0f4f47e953)): ?>
<?php $component = $__componentOriginal72158f852ce7e00889093c0f4f47e953; ?>
<?php unset($__componentOriginal72158f852ce7e00889093c0f4f47e953); ?>
<?php endif; ?>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <div class="lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="flex items-center justify-center bg-transparent hover:bg-muted text-foreground hover:text-primary w-10 h-10 rounded-md transition-all duration-200"
                        :aria-expanded="mobileMenuOpen"
                        aria-label="Toggle mobile navigation">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => '!mobileMenuOpen','class' => 'w-6 h-6 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-x'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'mobileMenuOpen','class' => 'w-6 h-6 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
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
            <?php if($menu_simple_type->showSearch): ?>
            <div class="mb-4">
                <button class="flex items-center bg-transparent hover:bg-muted text-foreground hover:text-primary w-full h-12 px-4 font-medium text-sm rounded-md transition-all duration-200">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 mr-3 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    <span>Search</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Mobile Home Link -->
            <a href="/" class="flex items-center text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md text-sm font-medium transition-all duration-200">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-home'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 mr-3 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                <span class="text-sm">Home</span>
            </a>

            <?php if(!empty($menuConfig['navigationItems'])): ?>
                <?php $__currentLoopData = $menuConfig['navigationItems']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <!-- Mobile Navigation Items -->
                <div>
                    <button @click="toggleMobileMenu(<?php echo e($index); ?>)"
                        class="flex items-center justify-between text-foreground hover:text-primary hover:bg-muted px-3 py-2 rounded-md w-full font-medium transition-all duration-200"
                        x-bind:class="activeMobileMenu === <?php echo e($index); ?> ? 'bg-muted' : ''"
                        data-index="<?php echo e($index); ?>">
                        <div class="flex items-center">
                            <?php if(!empty($item['icon'])): ?>
                                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $item['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 mr-3 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                            <?php endif; ?>
                            <span class="text-sm"><?php echo e($item['title'] ?? 'Menu Item'); ?></span>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-muted-foreground transition-transform duration-200','x-bind:class' => 'activeMobileMenu === '.e($index).' ? \'rotate-180\' : \'\'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </button>

                    <?php if(!empty($item['submenu'])): ?>
                    <div x-show="activeMobileMenu === <?php echo e($index); ?>"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 ml-8 space-y-1">
                        <?php $__currentLoopData = $item['submenu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($subItem['href'] ?? '#'); ?>"
                            class="flex items-center px-4 py-2 rounded-lg text-sm text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200">
                            <?php if(!empty($subItem['icon'])): ?>
                                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $subItem['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 mr-3 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                            <?php endif; ?>
                            <span class="text-xs"><?php echo e($subItem['title'] ?? 'Sub Item'); ?></span>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <!-- Mobile Theme Switcher -->
            <?php if($menu_simple_type->showThemeToggle): ?>
            <div class="mt-4">
                <?php if (isset($component)) { $__componentOriginal72158f852ce7e00889093c0f4f47e953 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal72158f852ce7e00889093c0f4f47e953 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'ui::components.ThemeSwitcher.index','data' => ['variant' => 'inline','size' => 'md','showLabels' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui::ThemeSwitcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'inline','size' => 'md','show-labels' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal72158f852ce7e00889093c0f4f47e953)): ?>
<?php $attributes = $__attributesOriginal72158f852ce7e00889093c0f4f47e953; ?>
<?php unset($__attributesOriginal72158f852ce7e00889093c0f4f47e953); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal72158f852ce7e00889093c0f4f47e953)): ?>
<?php $component = $__componentOriginal72158f852ce7e00889093c0f4f47e953; ?>
<?php unset($__componentOriginal72158f852ce7e00889093c0f4f47e953); ?>
<?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Mobile Social Links -->
            <?php if(!empty($menuConfig['socialLinks'])): ?>
            <div class="border-t border-border mt-4 pt-4">
                <div class="grid grid-cols-3 gap-2">
                    <?php $__currentLoopData = $menuConfig['socialLinks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $socialLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($socialLink['url']); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center px-4 py-3 rounded-lg text-xs font-medium text-foreground hover:text-primary hover:bg-primary/10 transition-all duration-200">
                        <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $socialLink['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 mr-2 text-muted-foreground']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                        <span><?php echo e($socialLink['label']); ?></span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</<?php echo e($menu_simple_type->tag); ?>>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/UI/Resources/Views/components/MenuSimpleType/index.blade.php ENDPATH**/ ?>