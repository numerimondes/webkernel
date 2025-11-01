<!-- WebkernelBuilder Main Canvas Area -->
<div class="wkb-canvas-inner" x-data="{
    xrayMode: false,
    draggedBlockType: null,
    activeMode: 'canvas',
    generateCode() {
        return '// Generated code will appear here';
    },
    handleDragOver(event) {
        event.preventDefault();
    },
    handleDragEnter(event) {
        event.preventDefault();
    },
    handleDragLeave(event) {
        event.preventDefault();
    },
    dropBlock(event) {
        event.preventDefault();
    }
}">
    <!-- WebkernelBuilder Canvas Toolbar -->
    <div class="dark:bg-builder-gray border-b border-gray-200 dark:border-gray-700 px-4 py-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <!-- Viewport Selector -->
                <div class="flex items-center space-x-1 bg-gray-100 dark:bg-gray-600 rounded-lg p-1">
                    <button class="viewport-btn px-2 py-1 rounded text-xs font-medium transition-colors"
                            data-viewport="mobile"
                            title="Mobile (375px)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <button class="viewport-btn px-2 py-1 rounded text-xs font-medium transition-colors"
                            data-viewport="tablet"
                            title="Tablet (768px)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <button class="viewport-btn px-2 py-1 rounded text-xs font-medium transition-colors active"
                            data-viewport="desktop"
                            title="Desktop (1920px)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <button class="viewport-btn px-2 py-1 rounded text-xs font-medium transition-colors"
                            data-viewport="wide"
                            title="Wide Desktop (2560px)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Current Viewport Display -->
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200" id="viewport-display">Desktop 1920px</span>
            </div>

            <!-- Canvas Controls -->
            <div class="flex items-center space-x-2">
                <!-- Pointer Tool -->
                <button id="pointer-tool" class="p-2 rounded-lg bg-builder-accent text-white tool-button active" data-tool="pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                </button>

                <!-- Hand Tool -->
                <button id="hand-tool" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors tool-button" data-tool="hand">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                    </svg>
                </button>

                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors" onclick="toggleTheme()">
                    <svg id="sun-icon" class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg id="moon-icon" class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <!-- Grid Toggle -->
                <button id="grid-toggle" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Zoom Controls -->
                <div class="flex items-center space-x-1 bg-gray-100 dark:bg-gray-600 rounded-lg p-1">
                    <button id="zoom-out" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-600 dark:text-gray-300" title="Zoom Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <span id="zoom-level" class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 min-w-[3rem] text-center">100%</span>
                    <button id="zoom-in" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-600 dark:text-gray-300" title="Zoom In">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>

                <!-- Reset View -->
                <button id="reset-view" class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors" title="Reset View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>

                <button class="px-3 py-1 rounded bg-builder-accent text-white text-sm hover:bg-blue-600 transition-colors">
                    Upgrade Now
                </button>
            </div>
        </div>
    </div>

    <!-- WebkernelBuilder Canvas Area -->
    <div id="canvas-container" class="wkb-canvas-area overflow-hidden relative flex-1">
        <div id="canvas-wrapper" class="wkb-canvas-wrapper absolute inset-0 flex items-center justify-center p-8"
            style="transform-origin: center center;">
            <!-- WebkernelBuilder Website Preview -->
            <div id="canvas"
                class="wkb-canvas-frame bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-transform duration-200 ease-out"
                style="transform-origin: center center; max-height: 100%;">
                <!-- WebkernelBuilder Canvas Frame -->
                <div class="wkb-canvas-inner bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
                    id="canvas-frame"
                    style="width: 1920px; max-width: 100%; min-height: 600px; max-height: 100%; transition: width 0.3s ease;">

                    <!-- XRay Grid Overlay -->
                    <div x-show="xrayMode" class="absolute inset-0 pointer-events-none z-10 rounded-lg"
                        style="background-image:
                            linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
                            background-size: 20px 20px;">
                    </div>

                    <!-- Drop Zone -->
                    <div id="dropzone" class="min-h-full p-6 transition-colors duration-200 overflow-hidden"
                        :class="{ 'bg-blue-50 border-2 border-dashed border-blue-300': draggedBlockType }"
                        @dragover.prevent="handleDragOver($event)" @dragenter.prevent="handleDragEnter($event)"
                        @dragleave="handleDragLeave($event)" @drop="dropBlock($event)" style="max-height: 100%;">

                        <!-- Empty State -->
                        <div class="empty-state flex flex-col items-center justify-center min-h-[400px] text-center"
                            style="max-height: 100%; overflow: hidden;">
                            <div class="mb-6">
                                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Start Building Your
                                Website</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md">
                                Drag and drop components from the left sidebar to start creating your website.
                                You can add headers, buttons, text, images, and more.
                            </p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <x-lucide-hand class="w-4 h-4 mr-2" />
                                    Drag & Drop
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <x-lucide-check-circle class="w-4 h-4 mr-2" />
                                    Easy to Use
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    <x-lucide-palette class="w-4 h-4 mr-2" />
                                    Customizable !
                                </span>
                            </div>
                        </div>

                        <!-- Dynamic Elements Container -->
                        <div class="dynamic-elements">
                            <!-- Elements will be added here via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- WebkernelBuilder Code View -->
    <div class="wkb-code-view flex-1 overflow-auto p-4 bg-gray-900" x-show="activeMode === 'code'">
        <pre class="text-green-400 text-sm font-mono leading-relaxed"><code x-text="generateCode()"></code></pre>
    </div>

    <!-- WebkernelBuilder Canvas Footer -->
    <div class="bg-white dark:bg-builder-gray border-t border-gray-200 dark:border-gray-700 px-4 py-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                </button>
                <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                    </svg>
                </button>
                <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
                <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">95%</span>
                <button class="px-3 py-1 rounded bg-builder-accent text-white text-sm hover:bg-blue-600 transition-colors">
                    Upgrade Now
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .viewport-btn {
        color: #6b7280;
        background: transparent;
    }

    .viewport-btn:hover {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .viewport-btn.active {
        background: #3b82f6;
        color: white;
    }

    .dark .viewport-btn {
        color: #9ca3af;
    }

    .dark .viewport-btn:hover {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }

    .dark .viewport-btn.active {
        background: #3b82f6;
        color: white;
    }
</style>

<script>
    // Listen for viewport changes from toolbar
    window.addEventListener('viewport-changed', function(event) {
        const canvasFrame = document.getElementById('canvas-frame');
        const canvas = document.getElementById('canvas');

        if (canvasFrame) {
            canvasFrame.style.width = event.detail.width;
        }

        if (canvas) {
            canvas.style.transform = `scale(${event.detail.scale})`;
            canvas.style.transformOrigin = 'top center';
        }
    });

    // Canvas Toolbar JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const viewportButtons = document.querySelectorAll('.viewport-btn');
        const viewportDisplay = document.getElementById('viewport-display');
        const canvas = document.getElementById('canvas');

        const viewports = {
            mobile: { width: '375px', baseLabel: 'Mobile 375px', scale: 1 },
            tablet: { width: '768px', baseLabel: 'Tablet 768px', scale: 1 },
            desktop: { width: '1920px', baseLabel: 'Desktop 1920px', scale: 0.6 },
            wide: { width: '2560px', baseLabel: 'Wide Desktop 2560px', scale: 0.45 }
        };

        let currentViewport = 'desktop';
        let currentZoom = 1;

        // Function to calculate and display current scale percentage
        function updateViewportDisplay() {
            const viewportData = viewports[currentViewport];
            const totalScale = viewportData.scale * currentZoom;
            const percentage = Math.round(totalScale * 100);
            viewportDisplay.textContent = `${viewportData.baseLabel} (${percentage}%)`;
        }

        viewportButtons.forEach(button => {
            button.addEventListener('click', function() {
                const viewport = this.getAttribute('data-viewport');
                currentViewport = viewport;

                // Update active button
                viewportButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Update canvas width and scale
                if (canvas) {
                    const viewportData = viewports[viewport];
                    const totalScale = viewportData.scale * currentZoom;
                    canvas.style.width = viewportData.width;
                    canvas.style.maxWidth = '100%';
                    canvas.style.transform = `scale(${totalScale})`;
                    canvas.style.transformOrigin = 'top center';
                }

                // Update display with current zoom
                updateViewportDisplay();

                // Dispatch event for other components
                window.dispatchEvent(new CustomEvent('viewport-changed', {
                    detail: {
                        viewport,
                        width: viewports[viewport].width,
                        scale: viewports[viewport].scale * currentZoom
                    }
                }));
            });
        });

        // Listen for zoom changes from existing zoom controls
        window.addEventListener('zoom-changed', function(event) {
            currentZoom = event.detail.zoom;
            updateViewportDisplay();

            // Update canvas scale
            if (canvas) {
                const viewportData = viewports[currentViewport];
                const totalScale = viewportData.scale * currentZoom;
                canvas.style.transform = `scale(${totalScale})`;
                canvas.style.transformOrigin = 'top center';
            }
        });

        // Initialize display
        updateViewportDisplay();
    });
</script>
