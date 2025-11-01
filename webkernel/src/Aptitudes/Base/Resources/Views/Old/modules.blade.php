<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules Webkernel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite('resources/css/app.css')
    @vite('resources/css/webkernel.css')
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass-effect { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); }
        .dark .glass-effect { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); }
        .hover-lift { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .dark .hover-lift:hover { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2); }
        .module-card { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .module-card:hover { background: rgba(59, 130, 246, 0.05); }
        .dark .module-card:hover { background: rgba(59, 130, 246, 0.1); }
        .status-dot { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        .theme-transition { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
    </style>
    <script>
        function initTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        function toggleTheme() {
            // Try to use Alpine.js theme if available
            if (window.Alpine && document.querySelector('[x-data]')) {
                const alpineElement = document.querySelector('[x-data*="theme"]');
                if (alpineElement && alpineElement._x_dataStack && alpineElement._x_dataStack[0]) {
                    const data = alpineElement._x_dataStack[0];
                    if (data.toggleTheme) {
                        data.toggleTheme();
                        return;
                    }
                }
            }

            // Fallback to direct DOM manipulation
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }

        document.addEventListener('DOMContentLoaded', initTheme);
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen theme-transition">
    <!-- Header -->
    <header class="sticky top-0 z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="w-8 h-8 gradient-bg rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Modules Webkernel</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Système de gestion des modules</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" placeholder="Rechercher un module..."
                               class="w-64 pl-10 pr-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent theme-transition">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()"
                            class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 theme-transition">
                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-200 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-200 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(empty($modules))
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="w-24 h-24 mx-auto gradient-bg rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Aucun module disponible</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">Il n'y a actuellement aucun module configuré dans le système. Les modules apparaîtront ici une fois qu'ils seront installés.</p>
            </div>
        @else
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 hover-lift theme-transition">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $total }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total modules</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 hover-lift theme-transition">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full status-dot"></div>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ collect($modules)->where('instantiated', false)->count() }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Modules prêts</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 hover-lift theme-transition">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full status-dot"></div>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ collect($modules)->where('instantiated', true)->count() }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">En cours</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 hover-lift theme-transition">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $performance['memory_used'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Mémoire utilisée</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modules Grid -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden theme-transition">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Liste des modules</h2>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ count($modules) }} modules affichés</span>
                            <span>•</span>
                            <span>Page {{ $page }}</span>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($modules as $index => $module)
                        <div class="p-6 module-card theme-transition">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4">
                                    <!-- Module Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center">
                                            <span class="text-white font-semibold text-lg">
                                                {{ strtoupper(substr($module['name'] ?? 'M', 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Module Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $module['name'] ?? 'Module sans nom' }}
                                            </h3>
                                            @if(isset($module['version']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                    v{{ $module['version'] }}
                                                </span>
                                            @endif
                                        </div>

                                        @if(isset($module['description']))
                                            <p class="text-gray-600 dark:text-gray-300 mb-3 leading-relaxed">
                                                {{ Str::limit($module['description'], 150) }}
                                            </p>
                                        @endif

                                        <div class="flex items-center space-x-4 text-sm">
                                            <span class="flex items-center text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c1.048 0 2.041.5 2.652 1.244l.637.65A1.5 1.5 0 0016.414 5H21a1 1 0 011 1v8a1 1 0 01-1 1h-4.586a1.5 1.5 0 01-1.061-.44l-.637-.65A1.5 1.5 0 0013.414 14H8a1 1 0 01-1-1V4a1 1 0 011-1z"></path>
                                                </svg>
                                                ID: {{ $module['id'] ?? $index }}
                                            </span>
                                            @if(isset($module['class']))
                                                <span class="flex items-center text-gray-500 dark:text-gray-400 font-mono text-xs">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                                    </svg>
                                                    {{ $module['class'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div class="flex-shrink-0">
                                    @if(isset($module['instantiated']) && $module['instantiated'])
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-2 status-dot"></div>
                                            En cours
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                            Prêt
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($total > $perPage)
                <div class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Affichage de <span class="font-medium">{{ ($page - 1) * $perPage + 1 }}</span> à
                        <span class="font-medium">{{ min($page * $perPage, $total) }}</span> sur
                        <span class="font-medium">{{ $total }}</span> résultats
                    </div>

                    <nav class="flex items-center space-x-2">
                        @if($page > 1)
                            <a href="?page={{ $page - 1 }}&per_page={{ $perPage }}"
                               class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
                                Précédent
                            </a>
                        @endif

                        @for($i = max(1, $page - 2); $i <= min(ceil($total / $perPage), $page + 2); $i++)
                            @if($i == $page)
                                <span class="px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="?page={{ $i }}&per_page={{ $perPage }}"
                                   class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        @if($page * $perPage < $total)
                            <a href="?page={{ $page + 1 }}&per_page={{ $perPage }}"
                               class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 theme-transition">
                                Suivant
                            </a>
                        @endif
                    </nav>
                </div>
            @endif

            <!-- Performance Panel -->
            <div class="mt-8 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 theme-transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informations système</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-300">Système opérationnel</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 theme-transition">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Temps d'exécution</div>
                        <div class="text-lg font-semibold text-green-600 dark:text-green-400 font-mono">{{ $performance['execution_time'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 theme-transition">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Mémoire utilisée</div>
                        <div class="text-lg font-semibold text-blue-600 dark:text-blue-400 font-mono">{{ $performance['memory_used'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 theme-transition">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pic mémoire</div>
                        <div class="text-lg font-semibold text-purple-600 dark:text-purple-400 font-mono">{{ $performance['peak_memory'] }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800/50 rounded-lg p-4 theme-transition">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Timestamp</div>
                        <div class="text-lg font-semibold text-gray-600 dark:text-gray-300 font-mono">{{ $performance['timestamp'] }}</div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        Microtime: <span class="font-mono">{{ number_format($performance['microtime'], 6) }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Éléments par page:</span>
                        <a href="?per_page=25" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 px-2 py-1 rounded">25</a>
                        <a href="?per_page=50" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 px-2 py-1 rounded">50</a>
                        <a href="?per_page=100" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 px-2 py-1 rounded">100</a>
                    </div>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
