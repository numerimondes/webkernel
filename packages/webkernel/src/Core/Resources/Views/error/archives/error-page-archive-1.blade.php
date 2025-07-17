@php
    $error_code = $error_code ?? 404;
    $error_message = $error_message ?? 'Page not found';
    $error_description = $error_description ?? 'Sorry, we couldn\'t find the page you\'re looking for.';
    $error_link = $error_link ?? '#';
    $error_link_text = $error_link_text ?? 'Go back home';
    $error_link_icon = $error_link_icon ?? 'arrow-left';
    $error_link_icon_class = $error_link_icon_class ?? 'w-4 h-4';
    $error_link_icon_class = $error_link_icon_class ?? 'w-4 h-4';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $error_code }} - {{ $error_message }}</title>
    <link rel="icon" href="{{ platformAbsoluteUrlAnyPrivatetoPublic('packages/webkernel/src/Core/Resources/repo-assets/credits/errorpagehelper.png') }}" type="image/png">
    <meta name="description" content="{{ $error_description }}">
    <meta name="keywords" content="error, page, not found">
    <meta name="author" content="Numerimondes">
    <meta name="generator" content="Webkernel - By Numerimondes">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    <meta name="google" content="notranslate">
    <meta name="favicon" content="{{ platformAbsoluteUrlAnyPrivatetoPublic('packages/webkernel/src/Core/Resources/repo-assets/credits/errorpagehelper.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="min-h-screen flex flex-col bg-white dark:bg-gray-900 transition-colors duration-300">
    <header class="bg-white dark:bg-gray-800 shadow-sm transition-colors duration-300">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Navigation principale -->
                <ul class="flex items-center justify-center gap-4 flex-1">
                    <li>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                            Homepage
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                            Library
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                            Knowledge Center
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                            Blog
                        </a>
                    </li>
                </ul>
                
                <!-- Boutons de contrôle du thème -->
                <div class="flex items-center gap-2">
                    <button id="lightModeBtn" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Mode clair">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <button id="darkModeBtn" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Mode sombre">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                    <button id="autoModeBtn" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Mode automatique">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="flex-1 grid min-h-full place-items-center bg-white dark:bg-gray-900 px-6 py-24 sm:py-32 lg:px-8 transition-colors duration-300">
        <div class="text-center">
            <p class="text-base font-semibold text-indigo-600 dark:text-indigo-400">404</p>
            <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-gray-900 dark:text-white sm:text-7xl transition-colors">Page not found</h1>
            <p class="mt-6 text-lg font-medium text-pretty text-gray-500 dark:text-gray-400 sm:text-xl/8 transition-colors">Sorry, we couldn't find the page you're looking for.</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="#" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-600 dark:hover:bg-indigo-500">Existing user? Sign In</a>
                <a href="#" class="text-sm font-semibold text-gray-900 dark:text-gray-400">Contact support <span aria-hidden="true">&rarr;</span></a>
              </div>
            
            <!-- Barre de recherche -->
            <div class="mt-8 mb-8">
                <div class="relative max-w-md mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           placeholder="Rechercher..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
            </div>
            
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-700/10 p-6 hover:shadow-lg dark:hover:shadow-gray-700/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mb-4 transition-colors">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0a2 2 0 01-2 2H10a2 2 0 01-2-2v0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 transition-colors">Library</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors">Accédez à notre collection complète de ressources et documents.</p>
                </div>
                
                <!-- Card 2 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-700/10 p-6 hover:shadow-lg dark:hover:shadow-gray-700/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-4 transition-colors">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 transition-colors">Knowledge Center</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors">Explorez nos guides et tutoriels pour approfondir vos connaissances.</p>
                </div>
                
                <!-- Card 3 -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-700/10 p-6 hover:shadow-lg dark:hover:shadow-gray-700/20 transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4 transition-colors">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 transition-colors">Blog</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors">Découvrez les dernières actualités et articles de notre équipe.</p>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-white dark:bg-gray-800 shadow-sm text-center transition-colors duration-300 mt-auto">
        <div class="text-gray-500 dark:text-gray-400 text-sm py-4 transition-colors">
            @ Numerimondes
        </div>
    </footer>

    <script>
        // Gestion du mode sombre/clair
        class ThemeManager {
            constructor() {
                this.currentTheme = this.getStoredTheme() || 'auto';
                this.init();
            }

            init() {
                this.updateTheme();
                this.bindEvents();
                this.updateButtonStates();
            }

            getStoredTheme() {
                try {
                    return sessionStorage.getItem('theme');
                } catch (e) {
                    return null;
                }
            }

            setStoredTheme(theme) {
                try {
                    sessionStorage.setItem('theme', theme);
                } catch (e) {
                    // Ignore les erreurs de stockage
                }
            }

            getSystemTheme() {
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            updateTheme() {
                const html = document.documentElement;
                
                if (this.currentTheme === 'auto') {
                    const systemTheme = this.getSystemTheme();
                    if (systemTheme === 'dark') {
                        html.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                    }
                } else if (this.currentTheme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
            }

            setTheme(theme) {
                this.currentTheme = theme;
                this.setStoredTheme(theme);
                this.updateTheme();
                this.updateButtonStates();
            }

            updateButtonStates() {
                const buttons = {
                    light: document.getElementById('lightModeBtn'),
                    dark: document.getElementById('darkModeBtn'),
                    auto: document.getElementById('autoModeBtn')
                };

                // Reset tous les boutons
                Object.values(buttons).forEach(btn => {
                    btn.classList.remove('bg-indigo-100', 'dark:bg-indigo-900/30', 'text-indigo-600', 'dark:text-indigo-400');
                    btn.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
                });

                // Activer le bouton correspondant au thème actuel
                if (buttons[this.currentTheme]) {
                    buttons[this.currentTheme].classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
                    buttons[this.currentTheme].classList.add('bg-indigo-100', 'dark:bg-indigo-900/30', 'text-indigo-600', 'dark:text-indigo-400');
                }
            }

            bindEvents() {
                document.getElementById('lightModeBtn').addEventListener('click', () => {
                    this.setTheme('light');
                });

                document.getElementById('darkModeBtn').addEventListener('click', () => {
                    this.setTheme('dark');
                });

                document.getElementById('autoModeBtn').addEventListener('click', () => {
                    this.setTheme('auto');
                });

                // Écouter les changements de préférences système
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    if (this.currentTheme === 'auto') {
                        this.updateTheme();
                    }
                });
            }
        }

        // Initialiser le gestionnaire de thème dès que le DOM est chargé
        document.addEventListener('DOMContentLoaded', () => {
            new ThemeManager();
        });
    </script>
</body>
</html>