<!-- Block Library Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-data="{ open: false }"
     x-show="open"
     x-cloak
     x-on:open-block-library.window="open = true"
     x-on:close-block-library.window="open = false"
     @click="open = false"
     style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] transform transition-all duration-300 ease-out"
         @click.stop>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Block Library</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input type="text" placeholder="Search blocks..."
                           class="w-full px-4 py-2 pl-10 bg-gray-100 dark:bg-gray-700 border-0 rounded-lg text-gray-700 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-lucide-search class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
                </div>
            </div>

            <!-- Block Categories -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                @php
                    $blockCategories = [
                        'Layout' => [
                            ['name' => 'Container', 'icon' => 'square', 'description' => 'Basic container element'],
                            ['name' => 'Row', 'icon' => 'layout-grid', 'description' => 'Horizontal layout row'],
                            ['name' => 'Column', 'icon' => 'layout', 'description' => 'Vertical layout column'],
                            ['name' => 'Section', 'icon' => 'rectangle-horizontal', 'description' => 'Page section divider'],
                        ],
                        'Typography' => [
                            ['name' => 'Heading', 'icon' => 'type', 'description' => 'Text heading element'],
                            ['name' => 'Paragraph', 'icon' => 'align-left', 'description' => 'Text paragraph'],
                            ['name' => 'List', 'icon' => 'list', 'description' => 'Ordered or unordered list'],
                            ['name' => 'Quote', 'icon' => 'quote', 'description' => 'Block quote element'],
                        ],
                        'Media' => [
                            ['name' => 'Image', 'icon' => 'image', 'description' => 'Image element'],
                            ['name' => 'Video', 'icon' => 'video', 'description' => 'Video player'],
                            ['name' => 'Gallery', 'icon' => 'images', 'description' => 'Image gallery'],
                            ['name' => 'Icon', 'icon' => 'star', 'description' => 'Icon element'],
                        ],
                        'Forms' => [
                            ['name' => 'Button', 'icon' => 'mouse-pointer-click', 'description' => 'Clickable button'],
                            ['name' => 'Input', 'icon' => 'edit', 'description' => 'Text input field'],
                            ['name' => 'Textarea', 'icon' => 'align-left', 'description' => 'Multi-line text input'],
                            ['name' => 'Select', 'icon' => 'chevron-down', 'description' => 'Dropdown selection'],
                        ],
                        'Navigation' => [
                            ['name' => 'Menu', 'icon' => 'menu', 'description' => 'Navigation menu'],
                            ['name' => 'Breadcrumb', 'icon' => 'chevron-right', 'description' => 'Breadcrumb navigation'],
                            ['name' => 'Pagination', 'icon' => 'chevrons-left', 'description' => 'Page navigation'],
                            ['name' => 'Tabs', 'icon' => 'folder', 'description' => 'Tabbed content'],
                        ],
                        'Content' => [
                            ['name' => 'Card', 'icon' => 'square', 'description' => 'Content card'],
                            ['name' => 'Accordion', 'icon' => 'chevron-down', 'description' => 'Collapsible content'],
                            ['name' => 'Modal', 'icon' => 'maximize', 'description' => 'Popup modal'],
                            ['name' => 'Tooltip', 'icon' => 'message-circle', 'description' => 'Hover tooltip'],
                        ],
                    ];
                @endphp

                @foreach ($blockCategories as $categoryName => $blocks)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3 flex items-center">
                            <x-lucide-puzzle class="w-4 h-4 mr-2" />
                            {{ $categoryName }}
                        </h4>

                        <div class="space-y-2">
                            @foreach ($blocks as $block)
                                <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors"
                                     draggable="true"
                                     data-block-type="{{ $block['name'] }}"
                                     @dragstart="dragStart($event, '{{ $block['name'] }}')"
                                     @dragend="dragEnd($event)">
                                    <x-dynamic-component :component="'lucide-' . $block['icon']" class="w-4 h-4 mr-3 text-gray-500 dark:text-gray-400" />
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $block['name'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $block['description'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-md transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
