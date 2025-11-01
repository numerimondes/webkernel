<div>
    @php
        // Get real pages from the current project
        $pages = $projectPages ?? [];

        // Add default pages if none exist
        if (empty($pages)) {
            $pages = [['name' => 'Home', 'path' => '/', 'icon' => 'home', 'type' => 'page', 'is_homepage' => true]];
        }

        // Add folder structure for organization
        $folders = [
            ['name' => 'Blog', 'path' => '/blog', 'icon' => 'newspaper', 'type' => 'folder'],
            [
                'name' => 'Personal Projects',
                'path' => '/personal-projects',
                'icon' => 'folder-open',
                'type' => 'folder',
            ],
            ['name' => 'API', 'path' => '/api', 'icon' => 'code', 'type' => 'folder'],
            ['name' => 'Components', 'path' => '/components', 'icon' => 'puzzle', 'type' => 'folder'],
        ];

        $layers = [
            ['name' => 'Body', 'type' => 'container', 'icon' => 'square', 'children' => []],
            [
                'name' => 'Header',
                'type' => 'component',
                'icon' => 'layout-grid',
                'children' => [
                    ['name' => 'Navigation', 'type' => 'component', 'icon' => 'menu'],
                    ['name' => 'Logo', 'type' => 'element', 'icon' => 'image'],
                ],
            ],
            [
                'name' => 'Hero Section',
                'type' => 'section',
                'icon' => 'rectangle-horizontal',
                'active' => true,
                'children' => [
                    ['name' => 'Hero Title', 'type' => 'text', 'icon' => 'type'],
                    ['name' => 'Hero CTA', 'type' => 'button', 'icon' => 'mouse-pointer-click'],
                ],
            ],
            [
                'name' => 'Content',
                'type' => 'section',
                'icon' => 'file-text',
                'children' => [
                    ['name' => 'Article', 'type' => 'text', 'icon' => 'align-left'],
                    ['name' => 'Sidebar', 'type' => 'container', 'icon' => 'sidebar-open'],
                ],
            ],
            ['name' => 'Footer', 'type' => 'component', 'icon' => 'minus', 'children' => []],
        ];

        $assets = [
            'components' => [
                ['name' => 'Hero', 'type' => 'component', 'block_type' => 'Hero', 'icon' => 'image'],
                ['name' => 'Button', 'type' => 'component', 'block_type' => 'Button', 'icon' => 'mouse-pointer-click'],
                ['name' => 'Card', 'type' => 'component', 'block_type' => 'Card', 'icon' => 'square'],
                ['name' => 'Text', 'type' => 'component', 'block_type' => 'Text', 'icon' => 'type'],
                ['name' => 'Input', 'type' => 'component', 'block_type' => 'Input', 'icon' => 'edit'],
                ['name' => 'Header', 'type' => 'component', 'block_type' => 'HeaderBasic', 'icon' => 'layout-grid'],
            ],
            'images' => [
                ['name' => 'hero-bg.jpg', 'size' => '2.4 MB', 'type' => 'image'],
                ['name' => 'logo.png', 'size' => '156 KB', 'type' => 'image'],
                ['name' => 'profile-pic.webp', 'size' => '89 KB', 'type' => 'image'],
                ['name' => 'banner.svg', 'size' => '23 KB', 'type' => 'image'],
            ],
            'icons' => [
                ['name' => 'arrow-right', 'type' => 'icon'],
                ['name' => 'check-circle', 'type' => 'icon'],
                ['name' => 'star', 'type' => 'icon'],
                ['name' => 'heart', 'type' => 'icon'],
                ['name' => 'zap', 'type' => 'icon'],
                ['name' => 'bookmark', 'type' => 'icon'],
            ],
            'fonts' => [
                ['name' => 'Inter-Regular.woff2', 'size' => '87 KB', 'type' => 'font'],
                ['name' => 'Inter-Bold.woff2', 'size' => '92 KB', 'type' => 'font'],
            ],
        ];
    @endphp

    <!-- WebkernelBuilder Sidebar Area -->
    <div id="left-sidebar" class="h-full">

        <!-- Sidebar Content -->
        <div class="h-full overflow-hidden">

                <!-- Collapse/Expand Button -->
                <div class="flex p-2 border-b border-gray-200 dark:border-gray-700" :class="leftCollapsed ? 'justify-center' : 'justify-end'">
                    <button @click="toggleLeft()"
                        class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 transition-colors flex-shrink-0"
                        :title="leftCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <x-lucide-panel-left-close x-show="!leftCollapsed" class="w-4 h-4" />
                        <x-lucide-panel-left-open x-show="leftCollapsed" class="w-4 h-4" />
                    </button>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-gray-200 dark:border-gray-700" x-show="!leftCollapsed">
                    <button
                        class="tab-button active flex-1 px-3 py-2 text-xs font-medium text-builder-accent border-b-2 border-builder-accent bg-builder-accent/5 transition-all duration-200"
                        data-tab="pages">
                        <x-lucide-file-text class="w-3.5 h-3.5 mx-auto mb-1" />
                        <span class="block">Pages</span>
                    </button>
                    <button
                        class="tab-button flex-1 px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-200"
                        data-tab="layers">
                        <x-lucide-layers class="w-3.5 h-3.5 mx-auto mb-1" />
                        <span class="block">Layers</span>
                    </button>
                    <button
                        class="tab-button flex-1 px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-200"
                        data-tab="assets">
                        <x-lucide-package class="w-3.5 h-3.5 mx-auto mb-1" />
                        <span class="block">Assets</span>
                    </button>
                </div>

                <!-- Collapsed Mode - Vertical Tabs -->
                <div class="flex flex-col border-b border-gray-200 dark:border-gray-700" x-show="leftCollapsed">
                    <button
                        class="tab-button active flex items-center justify-center px-1 py-4 text-xs font-medium text-builder-accent border-r-2 border-builder-accent bg-builder-accent/5 transition-all duration-200 w-full"
                        data-tab="pages" title="Pages">
                        <x-lucide-file-text class="w-5 h-5" />
                    </button>
                    <button
                        class="tab-button flex items-center justify-center px-1 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-200 w-full"
                        data-tab="layers" title="Layers">
                        <x-lucide-layers class="w-5 h-5" />
                    </button>
                    <button
                        class="tab-button flex items-center justify-center px-1 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-all duration-200 w-full"
                        data-tab="assets" title="Assets">
                        <x-lucide-package class="w-5 h-5" />
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="overflow-hidden" style="height: calc(100% - 120px);">
                    <!-- Pages Tab -->
                    <div id="pages-tab" class="tab-content h-full">
                        <!-- Search Bar -->
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700" x-show="!leftCollapsed">
                            <div class="relative">
                                <input type="text" placeholder="Search pages..."
                                    class="w-full px-3 py-1.5 pl-8 bg-gray-100 dark:bg-gray-600 border-0 rounded-md text-xs text-gray-700 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-builder-accent">
                                <x-lucide-search class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" />
                            </div>
                        </div>

                        <!-- Pages List -->
                        <div class="flex-1 overflow-y-auto p-3">
                            <div class="flex items-center justify-between mb-3" x-show="!leftCollapsed">
                                <h3
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Explorer</h3>

                                <button onclick="openCreatePageModal()"
                                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 transition-colors"
                                    title="Create new page">
                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            <!-- Collapsed Mode - Just Icons -->
                            <div class="flex flex-col items-center space-y-2 p-2" x-show="leftCollapsed">
                                <button onclick="openCreatePageModal()"
                                    class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 transition-colors"
                                    title="Create new page">
                                    <x-lucide-plus class="w-4 h-4" />
                                </button>
                            </div>


                            <!-- Normal Mode - Full List -->
                            <div class="space-y-0.5" x-show="!leftCollapsed">
                                @foreach ($pages as $page)
                                    <a href="{{ isset($page['id']) && $project->id ? route('filament.system.resources.website-projects.website-builder', $project->id) : '#' }}"
                                        class="page-item group flex items-center px-2 py-1.5 rounded-md text-sm {{ ($currentPage && isset($page['id']) && $currentPage->id === $page['id']) ? 'bg-builder-accent/10 border border-builder-accent/20 text-builder-accent' : 'hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200' }} cursor-pointer transition-all duration-150">
                                        <x-lucide-file-text class="w-4 h-4 mr-2 flex-shrink-0" />
                                        <span class="truncate">{{ $page['name'] }}</span>
                                        @if (isset($page['is_homepage']) && $page['is_homepage'])
                                            <x-lucide-home class="w-3 h-3 ml-auto opacity-60" title="Homepage" />
                                        @endif
                                    </a>
                                @endforeach

                                @foreach ($folders as $folder)
                                    <div
                                        class="page-item group flex items-center px-2 py-1.5 rounded-md text-sm hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 cursor-pointer transition-all duration-150">
                                        <x-dynamic-component :component="'lucide-' . $folder['icon']" class="w-4 h-4 mr-2 flex-shrink-0" />
                                        <span class="truncate">{{ $folder['name'] }}</span>
                                        <x-lucide-chevron-right
                                            class="w-3 h-3 ml-auto text-gray-400 group-hover:text-gray-600 transition-transform duration-150" />
                                    </div>
                                @endforeach
                            </div>

                            <!-- Collapsed Mode - Icons Only -->
                            <div class="flex flex-col items-center space-y-2 p-2" x-show="leftCollapsed">
                                @foreach ($pages as $page)
                                    <a href="{{ isset($page['id']) && $project->id ? route('filament.system.resources.website-projects.website-builder', $project->id) : '#' }}"
                                        class="page-item group flex items-center justify-center p-2 rounded-md {{ ($currentPage && isset($page['id']) && $currentPage->id === $page['id']) ? 'bg-builder-accent/10 border border-builder-accent/20 text-builder-accent' : 'hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200' }} cursor-pointer transition-all duration-150"
                                        title="{{ $page['name'] }}">
                                        <x-lucide-file-text class="w-4 h-4" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Layers Tab -->
                    <div id="layers-tab" class="tab-content h-full hidden">
                        <!-- Search Bar -->
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700" x-show="!leftCollapsed">
                            <div class="relative">
                                <input type="text" placeholder="Search layers..."
                                    class="w-full px-3 py-1.5 pl-8 bg-gray-100 dark:bg-gray-600 border-0 rounded-md text-xs text-gray-700 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-builder-accent">
                                <x-lucide-search class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" />
                            </div>
                        </div>

                        <!-- Layers Tree -->
                        <div class="flex-1 overflow-y-auto p-3">
                            <div class="flex items-center justify-between mb-3" x-show="!leftCollapsed">
                                <h3
                                    class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Structure</h3>
                                <button
                                    class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 transition-colors">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            <div class="space-y-0.5" x-show="!leftCollapsed">
                                @foreach ($layers as $layer)
                                    <div class="layer-group">
                                        <div class="layer-item group flex items-center px-2 py-1.5 rounded-md text-sm {{ isset($layer['active']) ? 'bg-builder-accent/10 border border-builder-accent/20 text-builder-accent' : 'hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200' }} cursor-pointer transition-all duration-150"
                                            data-layer="{{ $layer['name'] }}">
                                            @if (isset($layer['children']) && count($layer['children']) > 0)
                                                <x-lucide-chevron-down
                                                    class="w-3 h-3 mr-1 text-gray-400 transition-transform duration-150" />
                                            @else
                                                <div class="w-4 mr-1"></div>
                                            @endif
                                            <x-dynamic-component :component="'lucide-' . $layer['icon']"
                                                class="w-4 h-4 mr-2 flex-shrink-0 {{ isset($layer['active']) ? 'text-builder-accent' : 'text-gray-500 dark:text-gray-400' }}" />
                                            <span class="truncate">{{ $layer['name'] }}</span>
                                            <div
                                                class="ml-auto flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <x-lucide-eye
                                                    class="w-3 h-3 text-gray-400 hover:text-gray-600 cursor-pointer mr-1" />
                                                <x-lucide-lock
                                                    class="w-3 h-3 text-gray-400 hover:text-gray-600 cursor-pointer" />
                                            </div>
                                        </div>

                                        @if (isset($layer['children']) && count($layer['children']) > 0)
                                            <div class="ml-4 mt-0.5 space-y-0.5">
                                                @foreach ($layer['children'] as $child)
                                                    <div
                                                        class="layer-item group flex items-center px-2 py-1 rounded-md text-sm hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 cursor-pointer transition-all duration-150">
                                                        <div class="w-4 mr-1"></div>
                                                        <x-dynamic-component :component="'lucide-' . $child['icon']"
                                                            class="w-3.5 h-3.5 mr-2 flex-shrink-0 text-gray-400" />
                                                        <span class="truncate text-xs">{{ $child['name'] }}</span>
                                                        <div
                                                            class="ml-auto flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <x-lucide-eye
                                                                class="w-3 h-3 text-gray-400 hover:text-gray-600 cursor-pointer mr-1" />
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Assets Tab -->
                    <div id="assets-tab" class="tab-content h-full hidden">
                        <!-- Search Bar -->
                        <div class="p-3 border-b border-gray-200 dark:border-gray-700" x-show="!leftCollapsed">
                            <div class="relative">
                                <input type="text" placeholder="Search assets..."
                                    class="w-full px-3 py-1.5 pl-8 bg-gray-100 dark:bg-gray-600 border-0 rounded-md text-xs text-gray-700 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-builder-accent">
                                <x-lucide-search class="absolute left-2.5 top-2 w-3.5 h-3.5 text-gray-400" />
                            </div>
                        </div>

                        <!-- Assets Grid -->
                        <div class="flex-1 overflow-y-auto p-3" x-show="!leftCollapsed">
                            @foreach ($assets as $categoryName => $categoryItems)
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3
                                            class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider flex items-center">
                                            @if ($categoryName === 'components')
                                                <x-lucide-puzzle class="w-4 h-4 mr-2" />
                                            @elseif ($categoryName === 'images')
                                                <x-lucide-image class="w-4 h-4 mr-2" />
                                            @elseif($categoryName === 'icons')
                                                <x-lucide-star class="w-4 h-4 mr-2" />
                                            @else
                                                <x-lucide-type class="w-4 h-4 mr-2" />
                                            @endif
                                            {{ ucfirst($categoryName) }}
                                        </h3>
                                        <button
                                            class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 transition-colors">
                                            <x-lucide-plus class="w-3.5 h-3.5" />
                                        </button>
                                    </div>

                                    @if ($categoryName === 'components')
                                        <div class="grid grid-cols-2 gap-2 mb-4">
                                            @foreach ($categoryItems as $item)
                                                <div class="asset-item group relative bg-gray-100 dark:bg-gray-600 rounded-lg aspect-square flex flex-col items-center justify-center cursor-move hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors overflow-hidden draggable-component"
                                                    draggable="true" data-block-type="{{ $item['block_type'] }}"
                                                    @dragstart="dragStart($event, '{{ $item['block_type'] }}')"
                                                    @dragend="dragEnd($event)">
                                                    <x-dynamic-component :component="'lucide-' . $item['icon']"
                                                        class="w-6 h-6 text-gray-600 dark:text-gray-300 mb-1" />
                                                    <span
                                                        class="text-xs text-gray-700 dark:text-gray-200 text-center px-1">{{ $item['name'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($categoryName === 'images')
                                        <div class="grid grid-cols-2 gap-2 mb-4">
                                            @foreach ($categoryItems as $item)
                                                <div
                                                    class="asset-item group relative bg-gray-100 dark:bg-gray-600 rounded-lg aspect-square flex items-center justify-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors overflow-hidden">
                                                    <x-lucide-image class="w-6 h-6 text-gray-400" />
                                                    <div
                                                        class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <div class="truncate">{{ $item['name'] }}</div>
                                                        <div class="text-gray-300">{{ $item['size'] }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($categoryName === 'icons')
                                        <div class="grid grid-cols-3 gap-2 mb-4">
                                            @foreach ($categoryItems as $item)
                                                <div
                                                    class="asset-item group bg-gray-100 dark:bg-gray-600 rounded-lg aspect-square flex flex-col items-center justify-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                                                    <x-dynamic-component :component="'lucide-' . $item['name']"
                                                        class="w-5 h-5 text-gray-600 dark:text-gray-300 mb-1" />
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">{{ $item['name'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="space-y-1">
                                            @foreach ($categoryItems as $item)
                                                <div
                                                    class="asset-item group flex items-center px-2 py-1.5 rounded-md text-sm hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 cursor-pointer transition-all duration-150">
                                                    <x-lucide-type
                                                        class="w-4 h-4 mr-2 flex-shrink-0 text-gray-500 dark:text-gray-400" />
                                                    <div class="flex-1 min-w-0">
                                                        <div class="truncate">{{ $item['name'] }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $item['size'] }}</div>
                                                    </div>
                                                    <x-lucide-download
                                                        class="w-3.5 h-3.5 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
