<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" x-data="{
    theme: $persist('{{ $defaultTheme ?? 'dark' }}'),
    primaryColor: '{{ $primaryColor ?? 'blue' }}',
    toggleTheme() { this.theme = this.theme === 'dark' ? 'light' : 'dark' }
}" :class="{ 'dark': theme === 'dark' }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle ?? config('app.name', 'Website Builder') }}</title>
    <meta name="description" content="{{ $pageDescription ?? 'Professional website builder with dynamic themes' }}">

    <!-- Favicon -->
    @if (isset($favicon))
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @endif

    <!-- Preload critical resources -->
    <link rel="preload" href="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" as="script">
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Configuration -->
    <script>
        @php
            // Theme configuration array
            $themeConfig = [
                'colors' => [
                    'primary' => [
                        'blue' => [
                            '50' => '#eff6ff',
                            '100' => '#dbeafe',
                            '200' => '#bfdbfe',
                            '300' => '#93c5fd',
                            '400' => '#60a5fa',
                            '500' => '#3b82f6',
                            '600' => '#2563eb',
                            '700' => '#1d4ed8',
                            '800' => '#1e40af',
                            '900' => '#1e3a8a',
                            '950' => '#172554',
                        ],
                        'red' => [
                            '50' => '#fef2f2',
                            '100' => '#fee2e2',
                            '200' => '#fecaca',
                            '300' => '#fca5a5',
                            '400' => '#f87171',
                            '500' => '#ef4444',
                            '600' => '#dc2626',
                            '700' => '#b91c1c',
                            '800' => '#991b1b',
                            '900' => '#7f1d1d',
                            '950' => '#450a0a',
                        ],
                        'green' => [
                            '50' => '#f0fdf4',
                            '100' => '#dcfce7',
                            '200' => '#bbf7d0',
                            '300' => '#86efac',
                            '400' => '#4ade80',
                            '500' => '#22c55e',
                            '600' => '#16a34a',
                            '700' => '#15803d',
                            '800' => '#166534',
                            '900' => '#14532d',
                            '950' => '#052e16',
                        ],
                        'purple' => [
                            '50' => '#faf5ff',
                            '100' => '#f3e8ff',
                            '200' => '#e9d5ff',
                            '300' => '#d8b4fe',
                            '400' => '#c084fc',
                            '500' => '#a855f7',
                            '600' => '#9333ea',
                            '700' => '#7c3aed',
                            '800' => '#6b21a8',
                            '900' => '#581c87',
                            '950' => '#3b0764',
                        ],
                        'orange' => [
                            '50' => '#fff7ed',
                            '100' => '#ffedd5',
                            '200' => '#fed7aa',
                            '300' => '#fdba74',
                            '400' => '#fb923c',
                            '500' => '#f97316',
                            '600' => '#ea580c',
                            '700' => '#c2410c',
                            '800' => '#9a3412',
                            '900' => '#7c2d12',
                            '950' => '#431407',
                        ],
                        'pink' => [
                            '50' => '#fdf2f8',
                            '100' => '#fce7f3',
                            '200' => '#fbcfe8',
                            '300' => '#f9a8d4',
                            '400' => '#f472b6',
                            '500' => '#ec4899',
                            '600' => '#db2777',
                            '700' => '#be185d',
                            '800' => '#9d174d',
                            '900' => '#831843',
                            '950' => '#500724',
                        ],
                    ],
                    'gray' => [
                        '50' => '#f9fafb',
                        '100' => '#f3f4f6',
                        '200' => '#e5e7eb',
                        '300' => '#d1d5db',
                        '400' => '#9ca3af',
                        '500' => '#6b7280',
                        '600' => '#4b5563',
                        '700' => '#374151',
                        '800' => '#1f2937',
                        '900' => '#111827',
                        '950' => '#030712',
                    ],
                    'success' => [
                        '50' => '#f0fdf4',
                        '500' => '#22c55e',
                        '600' => '#16a34a',
                        '700' => '#15803d',
                    ],
                    'warning' => [
                        '50' => '#fffbeb',
                        '500' => '#f59e0b',
                        '600' => '#d97706',
                        '700' => '#b45309',
                    ],
                    'danger' => [
                        '50' => '#fef2f2',
                        '500' => '#ef4444',
                        '600' => '#dc2626',
                        '700' => '#b91c1c',
                    ],
                    'info' => [
                        '50' => '#eff6ff',
                        '500' => '#3b82f6',
                        '600' => '#2563eb',
                        '700' => '#1d4ed8',
                    ],
                    'nav-bg' => '#1b1b1f',
                    'nav-text' => '#f9fafb',
                    'nav-text-secondary' => '#9ca3af',
                    'nav-hover' => '#374151',
                    'search-bg' => '#0f0f11',
                    'border-color' => '#4b5563',
                ],
            ];
        @endphp

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {!! json_encode($themeConfig['colors']) !!},

                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'mono': ['JetBrains Mono', 'Monaco', 'Consolas', 'monospace']
                    },
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                        '128': '32rem'
                    },
                    borderRadius: {
                        '4xl': '2rem'
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'strong': '0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'pulse-slow': 'pulse 3s infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        slideUp: {
                            '0%': {
                                transform: 'translateY(10px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        slideDown: {
                            '0%': {
                                transform: 'translateY(-10px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        }
                    },
                    backdropBlur: {
                        xs: '2px'
                    }
                }
            },
            safelist: [
                // Primary color variants
                @foreach (['blue', 'red', 'green', 'purple', 'orange', 'pink'] as $color)
                    @foreach (['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'] as $shade)
                        'bg-primary-{{ $color }}-{{ $shade }}',
                        'text-primary-{{ $color }}-{{ $shade }}',
                        'border-primary-{{ $color }}-{{ $shade }}',
                        'hover:bg-primary-{{ $color }}-{{ $shade }}',
                        'hover:text-primary-{{ $color }}-{{ $shade }}',
                        'focus:ring-primary-{{ $color }}-{{ $shade }}',
                    @endforeach
                @endforeach
                // Status colors
                @foreach (['success', 'warning', 'danger', 'info'] as $status)
                    @foreach (['50', '500', '600', '700'] as $shade)
                        'bg-{{ $status }}-{{ $shade }}',
                        'text-{{ $status }}-{{ $shade }}',
                        'border-{{ $status }}-{{ $shade }}',
                        'hover:bg-{{ $status }}-{{ $shade }}',
                        'focus:ring-{{ $status }}-{{ $shade }}',
                    @endforeach
                @endforeach
                // Common utility classes
                'transition-all', 'duration-300', 'ease-in-out',
                'backdrop-blur-sm', 'backdrop-blur-md',
                'shadow-soft', 'shadow-strong',
                'animate-fade-in', 'animate-slide-up', 'animate-pulse-slow'
            ]
        }
    </script>

    <!-- Custom CSS Variables for Dynamic Theming -->
    <style>
        :root {
            --primary-color: {{ $primaryColorValue ?? '#3b82f6' }};
            --primary-color-rgb: {{ $primaryColorRgb ?? '59, 130, 246' }};
        }

        .bg-primary-dynamic {
            background-color: var(--primary-color);
        }

        .text-primary-dynamic {
            color: var(--primary-color);
        }

        .border-primary-dynamic {
            border-color: var(--primary-color);
        }

        .ring-primary-dynamic {
            --tw-ring-color: var(--primary-color);
        }

        .shadow-primary-dynamic {
            --tw-shadow-color: rgba(var(--primary-color-rgb), 0.1);
        }

        /* Theme transition */
        * {
            transition-property: background-color, border-color, color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
    </style>

    <!-- Additional head content -->
    @stack('head')
    @yield('head')
</head>

<body
    class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-all duration-300 ease-in-out antialiased">
    <!-- Theme Toggle Button (Optional) -->
    <button @click="toggleTheme()"
        class="fixed top-4 right-4 z-50 p-3 rounded-full bg-white dark:bg-gray-800 shadow-strong border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all duration-300 group"
        title="Toggle theme">
        <svg x-show="theme === 'dark'" class="w-5 h-5 text-yellow-500 group-hover:scale-110 transition-transform"
            fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                clip-rule="evenodd"></path>
        </svg>
        <svg x-show="theme === 'light'" class="w-5 h-5 text-gray-700 group-hover:scale-110 transition-transform"
            fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
    </button>

    <!-- Loading Overlay (Optional) -->
    @if (!isset($hideLoader) || !$hideLoader)
        <div x-data="{ loading: true }" x-show="loading" x-init="setTimeout(() => loading = false, 500)"
            class="fixed inset-0 z-50 bg-white dark:bg-gray-900 flex items-center justify-center transition-all duration-500"
            x-transition:leave="transition-opacity ease-in duration-500" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-8 h-8 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $loadingText ?? 'Loading...' }}</p>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <div class="flex flex-col min-h-screen">
        <!-- Navigation Area -->
        @if (!isset($hideNavigation) || !$hideNavigation)
            <nav
                class="sticky top-0 z-40 backdrop-blur-md bg-white/80 dark:bg-gray-900/80 border-b border-gray-200 dark:border-gray-800">
                <x-ui::MenuSimpleType />
            </nav>
        @endif

        <!-- Main Content -->
        <main class="flex-1 relative">
            <!-- Flash Messages Area -->
            @if (!isset($hideFlashMessages) || !$hideFlashMessages)
                <div class="fixed top-20 right-4 z-40 space-y-2" x-data="{ messages: [] }">
                    @if (session('success'))
                        <div class="bg-success-50 border border-success-200 text-success-700 px-4 py-3 rounded-lg shadow-soft animate-slide-down"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-danger-50 border border-danger-200 text-danger-700 px-4 py-3 rounded-lg shadow-soft animate-slide-down"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="bg-warning-50 border border-warning-200 text-warning-700 px-4 py-3 rounded-lg shadow-soft animate-slide-down"
                            role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Page Content -->
            <div class="animate-fade-in">
                @include('numerimondes::ModuleBlocks.index')

                <!-- Additional content slots -->
                @yield('content')
                @stack('content')
            </div>
        </main>

        <!-- Footer Area (Optional) -->
        @if (!isset($hideFooter) || !$hideFooter)
            <footer class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-8 mt-auto">
                @yield('footer')
                @stack('footer')
                @if (!isset($footerContent))
                    <div class="container mx-auto px-4 text-center text-gray-600 dark:text-gray-400">
                        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                    </div>
                @else
                    {!! $footerContent !!}
                @endif
            </footer>
        @endif
    </div>

    <!-- Scripts -->
    @stack('scripts')
    @yield('scripts')

    <!-- Debug Info (Development Only) -->
    @if (app()->environment('local') && (!isset($hideDebug) || !$hideDebug))
        <div class="fixed bottom-4 left-4 z-50 text-xs bg-black/75 text-white px-2 py-1 rounded font-mono">
            Theme: <span x-text="theme"></span> |
            Primary: <span x-text="primaryColor"></span> |
            Env: {{ app()->environment() }}
        </div>
    @endif
</body>

</html>
