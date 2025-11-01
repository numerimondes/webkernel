@if (!isset($hideLoader) || !$hideLoader)
    <div x-data="{ loading: true }"
         x-show="loading"
         x-init="setTimeout(() => loading = false, {{ $loadingDuration ?? 500 }})"
         class="fixed inset-0 z-50 bg-white dark:bg-gray-900 flex items-center justify-center transition-all duration-500"
         x-transition:leave="transition-opacity ease-in duration-500"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         role="progressbar"
         aria-label="Loading page">
        <div class="flex flex-col items-center space-y-4">
            <div class="w-8 h-8 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">{{ $loadingText ?? 'Loading...' }}</p>
        </div>
    </div>
@endif
