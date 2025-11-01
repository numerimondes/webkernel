<div id="right-sidebar" :class="rightCollapsed ? 'collapsed' : ''">
    <!-- Properties Panel Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <div x-show="!rightCollapsed">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Properties</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Hero Section</p>
        </div>
        <button @click="toggleRight()"
                class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 transition-colors"
                :title="rightCollapsed ? 'Expand properties panel' : 'Collapse properties panel'">
            <x-lucide-panel-right-close x-show="!rightCollapsed" class="w-4 h-4" />
            <x-lucide-panel-right-open x-show="rightCollapsed" class="w-4 h-4" />
        </button>
    </div>

    <!-- Properties Content -->
    <div class="overflow-y-auto" style="height: calc(100% - 80px);" x-show="!rightCollapsed">
        <!-- Link Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('link')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Link</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="link-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="link-content" class="px-4 pb-4">
                <input type="text" placeholder="Page or URL..." class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-builder-accent">
            </div>
        </div>

        <!-- Position Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('position')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Position</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="position-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="position-content" class="px-4 pb-4">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Type</label>
                        <select class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <option value="relative">Relative</option>
                            <option value="absolute">Absolute</option>
                            <option value="fixed">Fixed</option>
                            <option value="sticky">Sticky</option>
                        </select>
                    </div>
                                </div>
                            </div>
                        </div>

        <!-- Size Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('size')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Size</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="size-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="size-content" class="px-4 pb-4">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Width</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" value="100%" class="flex-1 px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <select class="px-2 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-xs text-gray-700 dark:text-gray-200 focus:outline-none">
                                <option value="rel">Rel</option>
                                <option value="px">px</option>
                                <option value="%">%</option>
                            </select>
                        </div>
                                </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Height</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" value="500" class="flex-1 px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <select class="px-2 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-xs text-gray-700 dark:text-gray-200 focus:outline-none">
                                <option value="fixed">Fixed</option>
                                <option value="rel">Rel</option>
                                <option value="px">px</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Min Max</label>
                        <button class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                            → Add...
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('layout')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Layout</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="layout-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="layout-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Display</label>
                        <select class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <option value="block">Block</option>
                            <option value="flex">Flex</option>
                            <option value="grid">Grid</option>
                            <option value="inline">Inline</option>
                        </select>
                    </div>
                        </div>
                    </div>
                </div>

        <!-- Effects Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('effects')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Effects</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="effects-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
            </button>
            <div id="effects-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Opacity</label>
                        <input type="range" min="0" max="1" step="0.1" value="1" class="w-full">
                    </div>
                </div>
                </div>
            </div>

        <!-- Overlays Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('overlays')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Overlays</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="overlays-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
            </button>
            <div id="overlays-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <button class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                        → Add...
                    </button>
                </div>
            </div>
                                    </div>

        <!-- Cursor Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('cursor')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Cursor</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="cursor-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="cursor-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <select class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                        <option value="default">Default</option>
                        <option value="pointer">Pointer</option>
                        <option value="text">Text</option>
                        <option value="move">Move</option>
                    </select>
                                    </div>
                                </div>
                            </div>

        <!-- Styles Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('styles')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Styles</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform rotate-180" id="styles-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="styles-content" class="px-4 pb-4">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Opacity</label>
                        <input type="range" min="0" max="1" step="0.1" value="1" class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Visible</label>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" checked class="rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-200">Yes</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Fill</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" value="#FFFFFF" class="w-8 h-8 rounded border-0">
                            <input type="text" value="#FFFFFF" class="flex-1 px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Overflow</label>
                        <select class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <option value="hidden">Hidden</option>
                            <option value="visible">Visible</option>
                            <option value="scroll">Scroll</option>
                            <option value="auto">Auto</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Radius</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" value="0" class="w-16 px-2 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <button class="p-2 bg-gray-100 dark:bg-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Border</label>
                        <button class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                            Add...
                        </button>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Shadows</label>
                        <button class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                            Add...
                        </button>
                    </div>
                    </div>
                </div>
            </div>

        <!-- Text Editor Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('text-editor')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Text Editor</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="text-editor-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="text-editor-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Content</label>
                        <textarea class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent" rows="3" placeholder="Enter text content..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Font Size</label>
                        <input type="number" value="16" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Font Weight</label>
                        <select class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <option value="normal">Normal</option>
                            <option value="bold">Bold</option>
                            <option value="lighter">Lighter</option>
                        </select>
                    </div>
                </div>
                                    </div>
                                </div>

        <!-- Image Upload Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('image-upload')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Image Upload</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="image-upload-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="image-upload-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Drop images here or click to upload</p>
                        <input type="file" accept="image/*" multiple class="hidden" id="image-upload-input">
                        <button onclick="document.getElementById('image-upload-input').click()" class="px-4 py-2 bg-builder-accent text-white rounded-lg text-sm hover:bg-blue-600 transition-colors">
                            Choose Files
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-600 rounded-lg">
                            <span class="text-sm text-gray-700 dark:text-gray-200">image1.jpg</span>
                            <button class="text-red-500 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                        </div>
                    </div>

        <!-- Transforms Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('transforms')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Transforms</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="transforms-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="transforms-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Rotate</label>
                        <input type="range" min="0" max="360" value="0" class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Scale</label>
                        <input type="range" min="0.1" max="2" step="0.1" value="1" class="w-full">
                    </div>
                </div>
            </div>
                    </div>

        <!-- Selection Section -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <button class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" onclick="toggleSection('selection')">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Selection</span>
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" id="selection-arrow">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="selection-content" class="px-4 pb-4 hidden">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">User Select</label>
                        <select class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-600 border-0 rounded-lg text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-builder-accent">
                            <option value="auto">Auto</option>
                            <option value="none">None</option>
                            <option value="text">Text</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
