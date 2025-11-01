@if (app()->environment('local') && (!isset($hideDebug) || !$hideDebug))
    <div class="fixed bottom-4 left-4 z-50 text-xs bg-black/75 text-white px-3 py-2 rounded-lg font-mono max-w-sm"
         x-data="{
            expanded: false,
            stats: {
                theme: 'loading...',
                primaryColor: 'loading...',
                loadTime: 'calculating...',
                memoryUsage: '{{ round(memory_get_usage(true) / 1024 / 1024, 2) }} MB',
                peakMemory: '{{ round(memory_get_peak_usage(true) / 1024 / 1024, 2) }} MB'
            }
         }"
         x-init="
            stats.theme = localStorage.getItem('theme') || 'dark';
            stats.primaryColor = primaryColor;
            window.addEventListener('load', () => {
                const perfData = performance.getEntriesByType('navigation')[0];
                stats.loadTime = Math.round(perfData.loadEventEnd - perfData.fetchStart) + 'ms';
            });
         ">

        <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
            <span class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span>Debug Info</span>
            </span>
            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </div>

        <div x-show="expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-3 space-y-1 border-t border-gray-600 pt-2">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <div class="text-gray-400">Environment:</div>
                    <div class="text-green-400">{{ app()->environment() }}</div>
                </div>
                <div>
                    <div class="text-gray-400">Laravel:</div>
                    <div>{{ app()->version() }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <div class="text-gray-400">Theme:</div>
                    <div x-text="stats.theme"></div>
                </div>
                <div>
                    <div class="text-gray-400">Primary:</div>
                    <div x-text="stats.primaryColor"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <div class="text-gray-400">Load Time:</div>
                    <div x-text="stats.loadTime"></div>
                </div>
                <div>
                    <div class="text-gray-400">DB Queries:</div>
                    <div>{{ count(DB::getQueryLog()) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <div class="text-gray-400">Memory:</div>
                    <div x-text="stats.memoryUsage"></div>
                </div>
                <div>
                    <div class="text-gray-400">Peak:</div>
                    <div x-text="stats.peakMemory"></div>
                </div>
            </div>

            <div>
                <div class="text-gray-400">Route:</div>
                <div class="text-xs break-all">{{ request()->route() ? request()->route()->getName() ?? request()->path() : request()->path() }}</div>
            </div>

            @if(auth()->check())
                <div>
                    <div class="text-gray-400">User:</div>
                    <div class="text-xs">{{ auth()->user()->email ?? auth()->id() }}</div>
                </div>
            @endif

            <div class="flex space-x-2 pt-2 border-t border-gray-600">
                <button onclick="console.clear(); app.notify('Console cleared', 'info', 1000);"
                        class="text-xs bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded">
                    Clear Console
                </button>
                <button onclick="location.reload()"
                        class="text-xs bg-blue-700 hover:bg-blue-600 px-2 py-1 rounded">
                    Reload
                </button>
            </div>
        </div>
    </div>

    {{-- Performance Monitor --}}
    <script>
        // Advanced debug information
        if (typeof console !== 'undefined') {
            console.group('%c🚀 Application Debug Info', 'color: #3b82f6; font-weight: bold;');
            console.log('%cEnvironment:', 'font-weight: bold;', '{{ app()->environment() }}');
            console.log('%cLaravel Version:', 'font-weight: bold;', '{{ app()->version() }}');
            console.log('%cPHP Version:', 'font-weight: bold;', '{{ phpversion() }}');
            console.log('%cRoute:', 'font-weight: bold;', '{{ request()->route() ? request()->route()->getName() ?? request()->path() : request()->path() }}');
            @if(auth()->check())
                console.log('%cAuthenticated User:', 'font-weight: bold;', {{ json_encode(['id' => auth()->id(), 'email' => auth()->user()->email ?? 'N/A']) }});
            @endif
            console.log('%cMemory Usage:', 'font-weight: bold;', '{{ round(memory_get_usage(true) / 1024 / 1024, 2) }} MB');
            console.log('%cDatabase Queries:', 'font-weight: bold;', {{ count(DB::getQueryLog()) }});
            console.groupEnd();
        }

        // Monitor page performance
        window.addEventListener('load', () => {
            setTimeout(() => {
                if ('performance' in window) {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    const metrics = {
                        'DNS Lookup': Math.round(perfData.domainLookupEnd - perfData.domainLookupStart) + 'ms',
                        'TCP Connection': Math.round(perfData.connectEnd - perfData.connectStart) + 'ms',
                        'Server Response': Math.round(perfData.responseStart - perfData.requestStart) + 'ms',
                        'Content Download': Math.round(perfData.responseEnd - perfData.responseStart) + 'ms',
                        'DOM Processing': Math.round(perfData.domContentLoadedEventStart - perfData.responseEnd) + 'ms',
                        'Resource Loading': Math.round(perfData.loadEventStart - perfData.domContentLoadedEventStart) + 'ms'
                    };

                    console.group('%c⚡ Performance Metrics', 'color: #f59e0b; font-weight: bold;');
                    Object.entries(metrics).forEach(([key, value]) => {
                        console.log(`%c${key}:`, 'font-weight: bold;', value);
                    });
                    console.groupEnd();

                    // Performance warning
                    const totalLoadTime = perfData.loadEventEnd - perfData.fetchStart;
                    if (totalLoadTime > 3000) {
                        console.warn(`%c⚠️  Slow page load detected: ${Math.round(totalLoadTime)}ms`, 'color: #ef4444; font-weight: bold; font-size: 14px;');
                    }
                }
            }, 100);
        });
    </script>
@endif
