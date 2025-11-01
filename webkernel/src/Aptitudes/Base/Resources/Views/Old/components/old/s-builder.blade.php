{{--
    resources/views/s-builder.blade.php

    Website Builder - Version Améliorée avec Filament PHP
    Chargement dynamique des blocks depuis le filesystem
    Interface moderne avec composants Filament
--}}

@php
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// Configuration des chemins des blocks
const BLOCKS_PATHS = [
    'resources/views/builder/blocks',
    'resources/views/builder/second_dir_blocks'
];

/**
 * Fonction pour scanner et charger tous les blocks disponibles
 */
function loadAvailableBlocks() {
    $availableBlocks = [];

    foreach (BLOCKS_PATHS as $path) {
        $fullPath = base_path($path);
        if (File::exists($fullPath)) {
            $blockFiles = File::files($fullPath);

            foreach ($blockFiles as $file) {
                if ($file->getExtension() === 'php') {
                    $blockId = $file->getFilenameWithoutExtension();
                    $blockPath = $path . '/' . $blockId;

                    // Charger les métadonnées du block
                    $blockData = extractBlockMetadata($blockPath, $blockId);
                    if ($blockData) {
                        $availableBlocks[] = $blockData;
                    }
                }
            }
        }
    }

    // Grouper par catégories
    return collect($availableBlocks)->groupBy('category');
}

/**
 * Extraire les métadonnées d'un block depuis ses commentaires ou variables
 */
function extractBlockMetadata($blockPath, $blockId) {
    try {
        // Essayer de charger le fichier pour extraire les métadonnées
        $blockFile = base_path($blockPath . '.blade.php');
        if (!File::exists($blockFile)) {
            return null;
        }

        $content = File::get($blockFile);

        // Rechercher les métadonnées dans les commentaires du fichier
        $metadata = [
            'id' => $blockId,
            'name' => extractMetadata($content, 'name') ?: Str::title(str_replace(['-', '_'], ' ', $blockId)),
            'icon' => extractMetadata($content, 'icon') ?: '📦',
            'preview_image' => extractMetadata($content, 'preview_image'),
            'category' => extractMetadata($content, 'category') ?: 'Autres',
            'preview' => extractMetadata($content, 'preview') ?: 'Block personnalisé',
            'description' => extractMetadata($content, 'description') ?: '',
            'path' => str_replace('resources/views/', '', $blockPath)
        ];

        return $metadata;

    } catch (Exception $e) {
        return [
            'id' => $blockId,
            'name' => Str::title(str_replace(['-', '_'], ' ', $blockId)),
            'icon' => '📦',
            'category' => 'Autres',
            'preview' => 'Block personnalisé',
            'description' => '',
            'path' => str_replace('resources/views/', '', $blockPath)
        ];
    }
}

/**
 * Extraire une métadonnée spécifique du contenu du fichier
 */
function extractMetadata($content, $key) {
    $patterns = [
        // Format: {{-- @meta key: value --}}
        '/{{--\s*@meta\s+' . $key . ':\s*(.+?)\s*--}}/i',
        // Format: {{-- key: value --}}
        '/{{--.*?' . $key . ':\s*(.+?)(?:\s*--}}|\n)/i',
        // Format PHP: $meta_key = 'value';
        '/\$meta_' . $key . '\s*=\s*[\'"](.+?)[\'"]\s*;/i'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content, $matches)) {
            return trim($matches[1]);
        }
    }

    return null;
}

// Charger tous les blocks disponibles
$blocksByCategory = loadAvailableBlocks();

// Debug: vérifier que les blocks sont chargés
if (empty($blocksByCategory)) {
    $blocksByCategory = collect([
        'Autres' => [
            [
                'id' => 'hero',
                'name' => 'Hero Section',
                'icon' => 'heroicon-o-home',
                'category' => 'Autres',
                'preview' => 'Section d\'accueil',
                'description' => 'Section d\'accueil avec titre et description',
                'path' => 'builder.blocks.hero'
            ],
            [
                'id' => 'text',
                'name' => 'Text Block',
                'icon' => 'heroicon-o-document-text',
                'category' => 'Autres',
                'preview' => 'Bloc de texte',
                'description' => 'Bloc de texte simple',
                'path' => 'builder.blocks.text'
            ]
        ]
    ]);
}

// Configuration des frames et autres données statiques
$frames = [
    [
        'id' => 'desktop',
        'name' => 'Desktop',
        'icon' => 'heroicon-o-computer-desktop',
        'width' => 'w-full',
        'maxWidth' => 'max-w-6xl',
        'scale' => 'scale-100',
        'active' => true
    ],
    [
        'id' => 'tablet-portrait',
        'name' => 'Tablet',
        'icon' => 'heroicon-o-device-tablet',
        'width' => 'w-96',
        'maxWidth' => 'max-w-md',
        'scale' => 'scale-90',
        'active' => false
    ],
    [
        'id' => 'mobile-portrait',
        'name' => 'Mobile',
        'icon' => 'heroicon-o-device-phone-mobile',
        'width' => 'w-80',
        'maxWidth' => 'max-w-xs',
        'scale' => 'scale-75',
        'active' => false
    ]
];

$languages = [
    ['code' => 'fr', 'name' => 'Français', 'flag' => '🇫🇷'],
    ['code' => 'en', 'name' => 'English', 'flag' => '🇺🇸'],
    ['code' => 'es', 'name' => 'Español', 'flag' => '🇪🇸']
];

$pages = [
    ['id' => 'page-1', 'name' => 'Home', 'active' => true],
    ['id' => 'page-2', 'name' => 'About', 'active' => false],
    ['id' => 'page-3', 'name' => 'Contact', 'active' => false]
];

$activeTab = 'pages'; // Tab actif par défaut
@endphp

<!DOCTYPE html>
<html lang="fr" x-bind:class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Builder Pro - Filament Edition</title>

    {{-- Vite + Filament --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
    @filamentStyles
    @filamentScripts

    <style>
        body { font-family: 'Inter', sans-serif; }

        .block-preview-card {
            @apply relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700
                   bg-white dark:bg-gray-800 shadow-sm hover:shadow-lg transition-all duration-200
                   cursor-pointer hover:-translate-y-1 hover:border-primary-300 dark:hover:border-primary-600;
        }

        .block-preview-card:hover .block-preview-overlay {
            @apply opacity-100;
        }

        .block-preview-overlay {
            @apply absolute inset-0 bg-primary-500/10 backdrop-blur-[2px]
                   flex items-center justify-center opacity-0 transition-opacity duration-200;
        }

        .canvas-block {
            @apply relative rounded-lg border-2 border-transparent transition-all duration-200;
        }

        .canvas-block.selected {
            @apply border-primary-500 shadow-lg ring-1 ring-primary-200 dark:ring-primary-800;
        }

        .canvas-block:hover:not(.selected) {
            @apply border-gray-300 dark:border-gray-600 shadow-md;
        }

        .frame-preview {
            min-height: 600px;
            @apply bg-white dark:bg-gray-900 rounded-xl shadow-2xl
                   transition-all duration-300 border border-gray-200 dark:border-gray-700;
        }

        .sidebar-section {
            @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                   rounded-lg p-4 transition-colors;
        }

        .category-header {
            @apply flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-700
                   rounded-md text-sm font-medium text-gray-700 dark:text-gray-300
                   hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors" x-data="websiteBuilderPro()">

    {{-- ===== HEADER TOOLBAR ===== --}}
    <header class="sticky top-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700
                   shadow-sm backdrop-blur-sm bg-white/95 dark:bg-gray-800/95">
        <div class="flex items-center justify-between px-6 py-3">

            {{-- Logo & Title --}}
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <x-filament::icon icon="heroicon-o-puzzle-piece" class="w-8 h-8 text-primary-600" />
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Website Builder Pro</h1>
                </div>

                <div class="hidden md:flex items-center space-x-1">
                    <x-filament::badge color="success" size="sm">v3.0</x-filament::badge>
                    <x-filament::badge color="primary" size="sm">Filament</x-filament::badge>
                </div>
            </div>

            {{-- Quick Actions --}}

            <div class="flex items-center space-x-3">

                {{-- Dark Mode Toggle --}}
                <button
                    type="button"
                    @click="toggleDarkMode()"
                    title="Basculer le mode sombre"
                    class="flex items-center justify-center w-10 h-10 p-0 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors"
                >
                    <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                {{-- Language Selector --}}
                <div class="relative" x-data="{ open: false }">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex items-center h-10 px-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors"
                    >
                        <span x-text="languages.find(l => l.code === currentLanguage)?.flag" class="mr-2 text-lg"></span>
                        <span x-text="languages.find(l => l.code === currentLanguage)?.name" class="font-medium"></span>
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200"
                             x-bind:class="open ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute top-full right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                rounded-lg shadow-xl z-50 min-w-[160px] py-1">
                        <template x-for="lang in languages" :key="lang.code">
                            <button @click="changeLanguage(lang.code); open = false"
                                    class="flex items-center space-x-3 w-full px-4 py-3 text-left hover:bg-gray-100
                                           dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300
                                           first:rounded-t-lg last:rounded-b-lg transition-all duration-200">
                                <span x-text="lang.flag" class="text-lg"></span>
                                <span x-text="lang.name" class="font-medium"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="h-8 w-px bg-gray-300 dark:bg-gray-600"></div>

                {{-- History Actions --}}
                <div class="flex items-center space-x-1">
                    <button
                        type="button"
                        @click="undo()"
                        x-bind:disabled="!canUndo"
                        title="Annuler (Ctrl+Z)"
                        class="flex items-center justify-center w-10 h-10 p-0 transition-all duration-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg"
                        x-bind:class="!canUndo ? 'opacity-50 cursor-not-allowed' : ''"
                    >
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        @click="redo()"
                        x-bind:disabled="!canRedo"
                        title="Rétablir (Ctrl+Y)"
                        class="flex items-center justify-center w-10 h-10 p-0 transition-all duration-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg"
                        x-bind:class="!canRedo ? 'opacity-50 cursor-not-allowed' : ''"
                    >
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l6-6m0 0l-6-6M6 9h12a6 6 0 000 12H9" />
                        </svg>
                    </button>
                </div>

                <div class="h-8 w-px bg-gray-300 dark:bg-gray-600"></div>

                {{-- Main Actions --}}
                <div class="flex items-center space-x-2">
                    <x-filament::button color="success" variant="ghost" size="sm" @click="preview()"
                                        class="flex items-center h-10 px-4 font-medium">
                        <x-filament::icon icon="heroicon-o-eye" class="w-4 h-4 mr-2" />
                        Preview
                    </x-filament::button>

                    <x-filament::button color="primary" size="sm" @click="viewCode()"
                                        class="flex items-center h-10 px-4 font-medium">
                        <x-filament::icon icon="heroicon-o-code-bracket" class="w-4 h-4 mr-2" />
                        View Code
                    </x-filament::button>
                </div>

                {{-- More Actions Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <x-filament::button
                        color="gray"
                        variant="ghost"
                        size="sm"
                        @click="open = !open"
                        class="flex items-center justify-center w-10 h-10 p-0"
                    >
                        <x-filament::icon icon="heroicon-o-ellipsis-vertical" class="w-5 h-5" />
                    </x-filament::button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute top-full right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                rounded-lg shadow-xl z-50 min-w-[200px] py-2">
                        <button @click="fullscreen(); open = false"
                                class="flex items-center space-x-3 w-full px-4 py-3 text-left hover:bg-gray-100
                                       dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium transition-all duration-200">
                            <x-filament::icon icon="heroicon-o-arrows-pointing-out" class="w-4 h-4 flex-shrink-0" />
                            <span>Plein écran</span>
                        </button>
                        <button @click="downloadHtml(); open = false"
                                class="flex items-center space-x-3 w-full px-4 py-3 text-left hover:bg-gray-100
                                       dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium transition-all duration-200">
                            <x-filament::icon icon="heroicon-o-arrow-down-tray" class="w-4 h-4 flex-shrink-0" />
                            <span>Télécharger HTML</span>
                        </button>
                        <hr class="my-2 border-gray-200 dark:border-gray-700">
                        <button @click="clearCanvas(); open = false"
                                class="flex items-center space-x-3 w-full px-4 py-3 text-left hover:bg-red-50
                                       dark:hover:bg-red-900/20 text-red-700 dark:text-red-400 text-sm font-medium transition-all duration-200">
                            <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4 flex-shrink-0" />
                            <span>Vider le canvas</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Page Tabs --}}
        <div class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    @foreach($pages as $page)
                    <div class="relative">
                        <div class="flex items-center space-x-3 px-5 py-3 border-b-2 text-sm font-medium transition-all duration-200 rounded-t-lg"
                             x-bind:class="activePage === '{{ $page['id'] }}' ?
                                        'bg-white dark:bg-gray-900 border-primary-500 text-primary-600 dark:text-primary-400 shadow-sm' :
                                        'bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 border-transparent text-gray-600 dark:text-gray-400'">
                            <button @click="switchPage('{{ $page['id'] }}')" class="flex items-center space-x-2 flex-1">
                                <x-filament::icon icon="heroicon-o-document" class="w-4 h-4" />
                                <span class="font-medium">{{ $page['name'] }}</span>
                            </button>
                            <button @click.stop="closePage('{{ $page['id'] }}')"
                                    class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 flex-shrink-0">
                                <x-filament::icon icon="heroicon-o-x-mark" class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <x-filament::button
                        color="gray"
                        variant="ghost"
                        size="sm"
                        @click="addNewPage()"
                        tooltip="Nouvelle page"
                        class="h-10 w-10 p-0 flex items-center justify-center"
                    >
                        <x-filament::icon icon="heroicon-o-plus" class="w-5 h-5" />
                    </x-filament::button>
                </div>

                {{-- Canvas Mode Toggle --}}
                <div class="flex items-center space-x-3 py-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Canvas:</span>
                    <button
                        type="button"
                        @click="toggleCanvasMode()"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        :class="canvasDarkMode ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700'"
                    >
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                            :class="canvasDarkMode ? 'translate-x-6' : 'translate-x-1'"
                        ></span>
                    </button>
                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="canvasDarkMode ? 'Dark' : 'Light'"></span>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== MAIN LAYOUT ===== --}}
    <div class="flex h-[calc(100vh-140px)]">

        {{-- ===== LEFT SIDEBAR ===== --}}
        <aside class="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700
                      overflow-y-auto transition-colors">

                                                            {{-- Sidebar Tabs --}}
            <x-filament::tabs>
                <x-filament::tabs.item
                    x-bind:class="activeTab === 'pages' ? 'fi-active' : ''"
                    x-on:click="switchTab('pages')"
                    icon="heroicon-o-document-duplicate"
                >
                    Pages
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    x-bind:class="activeTab === 'blocks' ? 'fi-active' : ''"
                    x-on:click="switchTab('blocks')"
                    icon="heroicon-o-cube"
                >
                    Blocks
                </x-filament::tabs.item>
            </x-filament::tabs>

            {{-- Pages Content --}}
            <div class="p-4 space-y-4" x-show="activeTab === 'pages'">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mes Pages</h3>
                    <x-filament::icon-button
                        icon="heroicon-o-plus"
                        @click="addNewPage()"
                        label="Nouveau"
                        color="primary"
                        size="sm"
                    />
                </div>

                <div class="space-y-3">
                    @foreach($pages as $page)
                    <div class="sidebar-section cursor-pointer transition-all duration-200 hover:shadow-md"
                         x-bind:class="activePage === '{{ $page['id'] }}' ? 'ring-2 ring-primary-500 border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20' : 'hover:border-gray-300 dark:hover:border-gray-600'"
                         @click="switchPage('{{ $page['id'] }}')">
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <x-filament::icon icon="heroicon-o-document" class="w-5 h-5 text-gray-400" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $page['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $page['id'] }}</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                <x-filament::icon-button
                                    icon="heroicon-o-document-duplicate"
                                    @click.stop="duplicatePage('{{ $page['id'] }}')"
                                    label="Dupliquer"
                                    color="gray"
                                    size="sm"
                                />
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Blocks Content --}}
            <div class="p-4 space-y-4" x-show="activeTab === 'blocks'">
                {{-- Search --}}
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Rechercher un block..."
                        x-model="searchQuery"
                        class="w-full px-3 py-2 pl-10 bg-white dark:bg-gray-700 border border-gray-300
                               dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2
                               focus:ring-primary-500 text-gray-900 dark:text-gray-100 transition-colors"
                    />
                    <x-filament::icon
                        icon="heroicon-o-magnifying-glass"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                    />
                </div>

                {{-- Blocks by Category --}}
                <div x-data="{ expanded: true }">
                    <div class="category-header" @click="expanded = !expanded">
                        <div class="flex items-center space-x-2">
                            <x-filament::icon icon="heroicon-o-folder" class="w-4 h-4" />
                            <span>Blocks disponibles</span>
                            <x-filament::badge size="sm" color="gray" x-text="availableBlocks.length"></x-filament::badge>
                        </div>
                        <x-filament::icon icon="heroicon-o-chevron-right"
                                          class="w-4 h-4 transition-transform duration-200"
                                          x-bind:class="expanded ? 'rotate-90' : ''" />
                    </div>

                    {{-- Category Blocks --}}
                    <div x-show="expanded" x-collapse class="space-y-2 ml-2">
                        <template x-for="block in availableBlocks" :key="block.id">
                            <div class="block-preview-card"
                                 x-show="!searchQuery || block.name.toLowerCase().includes(searchQuery.toLowerCase())"
                                 @click="addBlock(block.id, block.path)">

                                {{-- Block Preview Image/Icon --}}
                                <div class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200
                                            dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                                    <span class="text-3xl" x-text="block.icon || '📦'"></span>
                                </div>

                                {{-- Block Info --}}
                                <div class="p-3">
                                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm mb-1" x-text="block.name">
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2" x-text="block.preview || 'Block personnalisé'">
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 line-clamp-2" x-text="block.description || ''">
                                    </p>
                                </div>

                                {{-- Hover Overlay --}}
                                <div class="block-preview-overlay">
                                    <x-filament::icon-button
                                        icon="heroicon-o-plus"
                                        label="Ajouter"
                                        color="primary"
                                        size="sm"
                                    />
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ===== CANVAS CENTER ===== --}}
        <main class="flex-1 bg-gray-100 dark:bg-gray-900 overflow-auto transition-colors">

            {{-- Frame Selector --}}
            <div class="sticky top-0 z-10 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm
                        border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center justify-center space-x-4">

                    {{-- Frame Buttons --}}
                    <div class="flex items-center space-x-2">
                        @foreach($frames as $frame)
                        <x-filament::button
                            :color="$frame['active'] ? 'primary' : 'gray'"
                            :variant="$frame['active'] ? 'filled' : 'ghost'"
                            size="sm"
                            @click="switchFrame('{{ $frame['id'] }}')"
                            x-bind:class="activeFrame === '{{ $frame['id'] }}' ? 'ring-2 ring-primary-200 dark:ring-primary-800' : ''"
                            class="h-10 px-4 font-medium transition-all duration-200"
                        >
                            <x-filament::icon icon="{{ $frame['icon'] }}" class="w-4 h-4 mr-2" />
                            {{ $frame['name'] }}
                        </x-filament::button>
                        @endforeach
                    </div>

                    <div class="h-8 w-px bg-gray-300 dark:bg-gray-600"></div>

                    {{-- Zoom Controls --}}
                    <div class="flex items-center space-x-3">
                        <x-filament::button color="gray" variant="ghost" size="sm" @click="zoomOut()"
                                            class="h-10 w-10 p-0 flex items-center justify-center">
                            <x-filament::icon icon="heroicon-o-minus" class="w-5 h-5" />
                        </x-filament::button>

                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-[70px] text-center"
                              x-text="Math.round(zoomLevel * 100) + '%'"></span>

                        <x-filament::button color="gray" variant="ghost" size="sm" @click="zoomIn()"
                                            class="h-10 w-10 p-0 flex items-center justify-center">
                            <x-filament::icon icon="heroicon-o-plus" class="w-5 h-5" />
                        </x-filament::button>

                        <x-filament::button color="gray" variant="ghost" size="sm" @click="resetZoom()"
                                            class="h-10 px-4 font-medium">
                            Fit
                        </x-filament::button>
                    </div>
                </div>
            </div>

            {{-- Canvas Content --}}
            <div class="p-6 flex justify-center min-h-full">
                <div class="transition-all duration-300 origin-top"
                     :style="`transform: scale(${zoomLevel})`">
                    <div class="frame-preview transition-all duration-300"
                         x-bind:class="[
                             getFrameClasses(),
                             canvasDarkMode ? 'dark bg-gray-900' : 'bg-white'
                         ]">

                        {{-- Canvas Content --}}
                        <div id="canvas-content" class="p-6 space-y-6" x-bind:class="canvasDarkMode ? 'dark' : ''">

                            {{-- Default Hero Block --}}
                            <div class="canvas-block p-4 rounded-lg transition-all duration-200"
                                 x-bind:class="selectedBlock === 'hero-default' ? 'selected' : ''"
                                 @click="selectBlock('hero-default', 'hero')">
                                @includeIf('builder.blocks.hero', ['blockId' => 'hero-default'])
                            </div>

                            {{-- Dynamic Blocks --}}
                            <template x-for="(block, index) in canvasBlocks" :key="block.id">
                                <div class="canvas-block p-4 rounded-lg cursor-pointer transition-all duration-200"
                                     x-bind:class="selectedBlock === block.id ? 'selected' : ''"
                                     @click="selectBlock(block.id, block.type)"
                                     x-html="getBlockContent(block.type, block.id, block.path)">
                                </div>
                            </template>

                            {{-- Empty State --}}
                            <div x-show="canvasBlocks.length === 0" class="text-center py-16">
                                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600
                                           rounded-xl p-12 hover:border-primary-400 dark:hover:border-primary-600
                                           transition-colors bg-gray-50 dark:bg-gray-800/50">
                                    <div class="mx-auto w-16 h-16 bg-primary-100 dark:bg-primary-900/20
                                               rounded-full flex items-center justify-center mb-4">
                                        <x-filament::icon icon="heroicon-o-cube" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        Commencez à construire
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Glissez des blocks depuis la sidebar ou cliquez pour les ajouter
                                    </p>
                                                        <x-filament::button color="primary" @click="activeTab = 'blocks'"
                                        class="h-11 px-6 font-medium">
                                        <x-filament::icon icon="heroicon-o-cube" class="w-4 h-4 mr-2" />
                                        Parcourir les blocks
                                    </x-filament::button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- ===== RIGHT SIDEBAR - INSPECTOR ===== --}}
        <aside class="w-80 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700
                      overflow-y-auto transition-colors">

            <div class="p-4 space-y-6">
                {{-- Inspector Header --}}
                <div class="flex items-center space-x-2">
                    <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-5 h-5 text-primary-600" />
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Inspector</h2>
                </div>

                {{-- Selected Block Info --}}
                <div x-show="selectedBlock">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="text-center py-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                <span x-text="selectedBlockType"></span>
                            </span>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Block sélectionné
                            </p>
                        </div>
                    </div>

                    {{-- Block Properties --}}
                    <div class="space-y-4">

                        {{-- Title Property --}}
                        <div x-show="['hero', 'text', 'features', 'contact'].includes(selectedBlockType)">
                            <div class="space-y-2">
                                <label for="block-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Titre
                                </label>
                                <input
                                    id="block-title"
                                    type="text"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300
                                           dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2
                                           focus:ring-primary-500 text-gray-900 dark:text-gray-100 transition-colors"
                                    x-model="blockProperties.title"
                                    @input="updateBlockProperty('title', $event.target.value)"
                                />
                            </div>
                        </div>

                        {{-- Description Property --}}
                        <div x-show="['hero', 'text', 'features', 'contact'].includes(selectedBlockType)">
                            <div class="space-y-2">
                                <label for="block-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea
                                    id="block-description"
                                    rows="3"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300
                                           dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2
                                           focus:ring-primary-500 text-gray-900 dark:text-gray-100 transition-colors resize-none"
                                    x-model="blockProperties.description"
                                    @input="updateBlockProperty('description', $event.target.value)"></textarea>
                            </div>
                        </div>

                        {{-- Alignment Property --}}
                        <div>
                            <div class="space-y-2">
                                <label for="block-alignment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Alignement
                                </label>
                                <select
                                    id="block-alignment"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300
                                           dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2
                                           focus:ring-primary-500 text-gray-900 dark:text-gray-100 transition-colors"
                                    x-model="blockProperties.alignment"
                                    @change="updateBlockProperty('alignment', $event.target.value)">
                                    <option value="left">Gauche</option>
                                    <option value="center">Centre</option>
                                    <option value="right">Droite</option>
                                </select>
                            </div>
                        </div>

                        {{-- Variant Property --}}
                        <div>
                            <div class="space-y-2">
                                <label for="block-variant" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Style
                                </label>
                                <select
                                    id="block-variant"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300
                                           dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2
                                           focus:ring-primary-500 text-gray-900 dark:text-gray-100 transition-colors"
                                    x-model="blockProperties.variant"
                                    @change="updateBlockProperty('variant', $event.target.value)">
                                    <option value="default">Défaut</option>
                                    <option value="primary">Primaire</option>
                                    <option value="secondary">Secondaire</option>
                                    <option value="accent">Accent</option>
                                    <option value="dark">Sombre</option>
                                </select>
                            </div>
                        </div>

                        {{-- Multilingual Support --}}
                        <x-filament::section>
                            <x-slot name="header">
                                <div class="flex items-center space-x-2">
                                    <x-filament::icon icon="heroicon-o-language" class="w-4 h-4" />
                                    <span class="font-medium">Traductions</span>
                                </div>
                            </x-slot>

                            <div class="space-y-3">
                                <template x-for="lang in languages" :key="lang.code">
                                    <div class="flex items-center space-x-2">
                                        <span x-text="lang.flag" class="text-sm flex-shrink-0"></span>
                                        <span x-text="lang.code.toUpperCase()"
                                              class="text-xs text-gray-600 dark:text-gray-400 w-8 flex-shrink-0"></span>
                                        <input type="text"
                                               class="flex-1 px-2 py-1 text-xs bg-white dark:bg-gray-700
                                                      border border-gray-300 dark:border-gray-600 rounded
                                                      focus:outline-none focus:ring-1 focus:ring-primary-500
                                                      text-gray-900 dark:text-gray-100 transition-colors"
                                               :placeholder="`Titre en ${lang.name}...`">
                                    </div>
                                </template>
                            </div>
                        </x-filament::section>

                        {{-- Block Actions --}}
                        <div class="space-y-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-filament::button
                                color="primary"
                                variant="ghost"
                                size="sm"
                                class="w-full h-11 flex items-center justify-center font-medium"
                                @click="duplicateBlock()"
                                x-show="selectedBlock"
                            >
                                <x-filament::icon icon="heroicon-o-document-duplicate" class="w-4 h-4 mr-3" />
                                Dupliquer le block
                            </x-filament::button>

                            <x-filament::button
                                color="danger"
                                variant="ghost"
                                size="sm"
                                class="w-full h-11 flex items-center justify-center font-medium"
                                @click="removeBlock()"
                                x-show="selectedBlock !== 'hero-default'"
                            >
                                <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4 mr-3" />
                                Supprimer le block
                            </x-filament::button>
                        </div>
                    </div>
                </div>

                {{-- No Selection State --}}
                <div x-show="!selectedBlock">
                    <x-filament::section>
                        <div class="text-center py-8">
                            <div class="mx-auto w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full
                                       flex items-center justify-center mb-4">
                                <x-filament::icon icon="heroicon-o-cursor-arrow-rays" class="w-6 h-6 text-gray-400" />
                            </div>
                            <h3 class="font-medium text-gray-900 dark:text-white mb-1">
                                Sélectionnez un block
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Les propriétés apparaîtront ici
                            </p>
                        </div>
                    </x-filament::section>
                </div>
            </div>
        </aside>
    </div>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700
                   px-6 py-3 text-center transition-colors">
        <div class="flex items-center justify-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
            <span>Website Builder Pro v3.0</span>
            <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
            <span>Made with ❤️ Laravel Blade + Filament + Alpine.js</span>
            <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
            <button @click="showAbout = true" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                À propos
            </button>
        </div>
    </footer>

    {{-- ===== MODALS ===== --}}

    {{-- About Modal --}}
    <div x-show="showAbout"
         x-transition.opacity
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
         @click="showAbout = false">
        <div @click.stop class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
            <div class="bg-primary-600 dark:bg-primary-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">À propos</h3>
                    <x-filament::button color="white" variant="ghost" size="sm" @click="showAbout = false">
                        <x-filament::icon icon="heroicon-o-x-mark" class="w-4 h-4" />
                    </x-filament::button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-primary-100 dark:bg-primary-900/20
                               rounded-full flex items-center justify-center mb-4">
                        <x-filament::icon icon="heroicon-o-puzzle-piece" class="w-8 h-8 text-primary-600" />
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Website Builder Pro
                    </h4>
                    <x-filament::badge color="primary">Version 3.0.0</x-filament::badge>
                </div>

                <div class="text-gray-600 dark:text-gray-300 space-y-3">
                    <p class="text-center">
                        Interface moderne de création de sites web avec chargement dynamique des blocks.
                    </p>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h5 class="font-semibold text-gray-900 dark:text-white mb-2">Fonctionnalités :</h5>
                        <ul class="text-sm space-y-1">
                            <li class="flex items-center space-x-2">
                                <x-filament::icon icon="heroicon-o-check" class="w-4 h-4 text-green-500 flex-shrink-0" />
                                <span>Chargement dynamique des blocks</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <x-filament::icon icon="heroicon-o-check" class="w-4 h-4 text-green-500 flex-shrink-0" />
                                <span>Interface Filament native</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <x-filament::icon icon="heroicon-o-check" class="w-4 h-4 text-green-500 flex-shrink-0" />
                                <span>Mode sombre/clair</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <x-filament::icon icon="heroicon-o-check" class="w-4 h-4 text-green-500 flex-shrink-0" />
                                <span>Support multilingue</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <x-filament::icon icon="heroicon-o-check" class="w-4 h-4 text-green-500 flex-shrink-0" />
                                <span>Aperçu responsive</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <x-filament::icon icon="heroicon-o-check" class="w-4 h-4 text-green-500 flex-shrink-0" />
                                <span>Historique undo/redo</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="text-center pt-6">
                    <x-filament::button color="primary" @click="showAbout = false"
                                        class="h-11 px-6 font-medium">
                        Fermer
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    {{-- Clear Canvas Confirmation Modal --}}
    <div x-show="showClearConfirm"
         x-transition.opacity
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
         @click="showClearConfirm = false">
        <div @click.stop class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
            <div class="bg-red-600 dark:bg-red-700 px-6 py-4">
                <div class="flex items-center space-x-2 text-white">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5" />
                    <h3 class="text-lg font-semibold">Confirmation</h3>
                </div>
            </div>

            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Êtes-vous sûr de vouloir supprimer tous les blocks du canvas ?
                    Cette action ne peut pas être annulée.
                </p>

                <div class="flex space-x-4 pt-2">
                    <x-filament::button color="gray" class="flex-1 h-11 font-medium" @click="showClearConfirm = false">
                        Annuler
                    </x-filament::button>
                    <x-filament::button color="danger" class="flex-1 h-11 font-medium flex items-center justify-center" @click="confirmClearCanvas()">
                        <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4 mr-2" />
                        Supprimer tout
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== BLOCK TEMPLATES (Hidden) ===== --}}
    <div style="display: none;">
        {{-- Ces templates sont utilisés par JavaScript pour générer le HTML des blocks --}}
        @foreach($blocksByCategory->flatten() as $block)
        <div id="template-{{ $block['id'] }}">
            @includeIf($block['path'], ['blockId' => 'BLOCK_ID'])
        </div>
        @endforeach
    </div>

    {{-- ===== ALPINE.JS LOGIC ===== --}}
    <script>
        function websiteBuilderPro() {
            return {
                // Application State
                darkMode: localStorage.getItem('darkMode') === 'true',
                canvasDarkMode: localStorage.getItem('canvasDarkMode') === 'true',
                activeFrame: 'desktop',
                selectedBlock: 'hero-default',
                selectedBlockType: 'hero',
                searchQuery: '',
                canvasBlocks: [],
                blockCounter: 0,
                activePage: 'page-1',
                activeTab: '{{ $activeTab }}',
                currentLanguage: localStorage.getItem('currentLanguage') || 'fr',
                zoomLevel: 1,

                // Modal States
                showAbout: false,
                showClearConfirm: false,

                // History Management
                history: [],
                historyIndex: -1,
                canUndo: false,
                canRedo: false,

                // Configuration
                languages: @json($languages),
                availableBlocks: @json($blocksByCategory->flatten()->toArray()),
                blocksByCategory: @json($blocksByCategory->toArray()),

                // Debug
                debugBlocks() {
                    console.log('Available blocks:', this.availableBlocks);
                    console.log('Blocks by category:', this.blocksByCategory);
                },

                // Block Properties
                blockProperties: {
                    title: 'Bienvenue sur notre site',
                    description: 'Découvrez nos services exceptionnels',
                    alignment: 'center',
                    variant: 'primary'
                },

                                // ===== INITIALIZATION =====
                init() {
                    // Appliquer le dark mode au chargement
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }

                    // Debug
                    console.log('Initializing Website Builder Pro...');
                    this.debugBlocks();

                    this.saveToHistory();
                    this.loadBlockProperties(this.selectedBlockType);
                },

                // ===== MODE MANAGEMENT =====
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    // Forcer la mise à jour du DOM
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                toggleCanvasMode() {
                    this.canvasDarkMode = !this.canvasDarkMode;
                    localStorage.setItem('canvasDarkMode', this.canvasDarkMode);
                },

                changeLanguage(langCode) {
                    this.currentLanguage = langCode;
                    localStorage.setItem('currentLanguage', langCode);
                },

                // ===== FRAME MANAGEMENT =====
                switchFrame(frameId) {
                    this.activeFrame = frameId;
                    console.log('Frame switched to:', frameId);
                },

                getFrameClasses() {
                    const frames = @json($frames);
                    const currentFrame = frames.find(f => f.id === this.activeFrame);
                    if (currentFrame) {
                        return `${currentFrame.width} ${currentFrame.maxWidth}`;
                    }
                    return 'w-full max-w-6xl';
                },

                // ===== ZOOM MANAGEMENT =====
                zoomIn() {
                    this.zoomLevel = Math.min(this.zoomLevel + 0.1, 3);
                },

                zoomOut() {
                    this.zoomLevel = Math.max(this.zoomLevel - 0.1, 0.25);
                },

                resetZoom() {
                    this.zoomLevel = 1;
                },

                // ===== PAGE MANAGEMENT =====
                addNewPage() {
                    const timestamp = Date.now();
                    const pageId = `page-${timestamp}`;
                    console.log('Nouvelle page ajoutée:', pageId);
                    // Dans une vraie application, on ajouterait à la collection des pages
                },

                // ===== TAB MANAGEMENT =====
                switchTab(tabName) {
                    this.activeTab = tabName;
                    console.log('Tab switched to:', tabName);
                },

                switchPage(pageId) {
                    this.activePage = pageId;
                    console.log('Changement vers la page:', pageId);
                },

                closePage(pageId) {
                    console.log('Fermeture de la page:', pageId);
                },

                duplicatePage(pageId) {
                    console.log('Duplication de la page:', pageId);
                },

                // ===== BLOCK MANAGEMENT =====
                addBlock(blockType, blockPath = null) {
                    this.blockCounter++;
                    const blockId = `${blockType}-${this.blockCounter}`;

                    this.canvasBlocks.push({
                        id: blockId,
                        type: blockType,
                        path: blockPath || `builder.blocks.${blockType}`
                    });

                    this.selectBlock(blockId, blockType);
                    this.saveToHistory();
                },

                selectBlock(blockId, blockType) {
                    this.selectedBlock = blockId;
                    this.selectedBlockType = blockType;
                    this.loadBlockProperties(blockType);
                },

                removeBlock() {
                    if (this.selectedBlock === 'hero-default') return;

                    this.canvasBlocks = this.canvasBlocks.filter(block => block.id !== this.selectedBlock);
                    this.selectedBlock = null;
                    this.selectedBlockType = null;
                    this.saveToHistory();
                },

                duplicateBlock() {
                    if (!this.selectedBlock) return;

                    const currentBlock = this.canvasBlocks.find(block => block.id === this.selectedBlock);
                    if (currentBlock) {
                        this.addBlock(currentBlock.type, currentBlock.path);
                    } else if (this.selectedBlock === 'hero-default') {
                        this.addBlock('hero', 'builder.blocks.hero');
                    }
                },

                getBlockContent(blockType, blockId, blockPath = null) {
                    const template = document.querySelector(`#template-${blockType}`);
                    if (template) {
                        return template.innerHTML.replace(/BLOCK_ID/g, blockId);
                    }

                    // Fallback content
                    return `
                        <div class="p-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800
                                   rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 text-center">
                            <div class="text-4xl mb-2">📦</div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Block ${blockType}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">ID: ${blockId}</p>
                        </div>
                    `;
                },

                loadBlockProperties(blockType) {
                    const defaults = {
                        hero: {
                            title: 'Bienvenue sur notre site',
                            description: 'Découvrez nos services exceptionnels',
                            alignment: 'center',
                            variant: 'primary'
                        },
                        text: {
                            title: 'Titre du texte',
                            description: 'Contenu de votre paragraphe...',
                            alignment: 'left',
                            variant: 'default'
                        },
                        features: {
                            title: 'Nos fonctionnalités',
                            description: 'Découvrez ce qui nous rend uniques',
                            alignment: 'center',
                            variant: 'default'
                        },
                        contact: {
                            title: 'Contactez-nous',
                            description: 'Nous sommes là pour vous aider',
                            alignment: 'center',
                            variant: 'default'
                        },
                        gallery: {
                            title: 'Galerie d\'images',
                            description: 'Découvrez nos réalisations',
                            alignment: 'center',
                            variant: 'default'
                        },
                        footer: {
                            title: 'Pied de page',
                            description: 'Liens et informations',
                            alignment: 'left',
                            variant: 'dark'
                        }
                    };

                    this.blockProperties = defaults[blockType] || {
                        title: 'Titre',
                        description: 'Description',
                        alignment: 'center',
                        variant: 'default'
                    };
                },

                updateBlockProperty(property, value) {
                    this.blockProperties[property] = value;
                    console.log(`Mise à jour ${property}: ${value} pour block ${this.selectedBlock}`);
                    // Ici on pourrait mettre à jour le contenu en temps réel
                    this.saveToHistory();
                },

                // ===== TOOLBAR ACTIONS =====
                preview() {
                    const previewContent = this.generateHtml();
                    const previewWindow = window.open('', '_blank');
                    previewWindow.document.write(previewContent);
                    previewWindow.document.close();
                },

                fullscreen() {
                    if (document.documentElement.requestFullscreen) {
                        document.documentElement.requestFullscreen();
                    }
                },

                viewCode() {
                    const html = this.generateHtml();
                    const codeWindow = window.open('', '_blank');
                    codeWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Code HTML généré</title>
                            <style>
                                body { font-family: 'Inter', sans-serif; margin: 0; padding: 20px; background: #f9fafb; }
                                .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
                                .header { background: #3b82f6; color: white; padding: 20px; }
                                pre { background: #f3f4f6; padding: 20px; overflow: auto; margin: 0; white-space: pre-wrap; word-wrap: break-word; }
                                code { font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.5; }
                                .copy-btn { background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; float: right; margin: 10px; }
                                .copy-btn:hover { background: #059669; }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="header">
                                    <h1>Code HTML généré</h1>
                                    <button class="copy-btn" onclick="copyToClipboard()">Copier le code</button>
                                </div>
                                <pre><code id="code-content">${this.escapeHtml(html)}</code></pre>
                            </div>
                            <script>
                                function copyToClipboard() {
                                    const code = document.getElementById('code-content').textContent;
                                    navigator.clipboard.writeText(code).then(() => {
                                        alert('Code copié dans le presse-papiers !');
                                    });
                                }
                            </script>
                        </body>
                        </html>
                    `);
                    codeWindow.document.close();
                },

                downloadHtml() {
                    const html = this.generateHtml();
                    const blob = new Blob([html], { type: 'text/html' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `website-${this.activePage}-${new Date().toISOString().slice(0, 10)}.html`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                },

                clearCanvas() {
                    this.showClearConfirm = true;
                },

                confirmClearCanvas() {
                    this.canvasBlocks = [];
                    this.selectedBlock = 'hero-default';
                    this.selectedBlockType = 'hero';
                    this.showClearConfirm = false;
                    this.saveToHistory();
                },

                // ===== HISTORY MANAGEMENT =====
                saveToHistory() {
                    const state = {
                        canvasBlocks: JSON.parse(JSON.stringify(this.canvasBlocks)),
                        selectedBlock: this.selectedBlock,
                        selectedBlockType: this.selectedBlockType,
                        blockProperties: JSON.parse(JSON.stringify(this.blockProperties))
                    };

                    if (this.historyIndex < this.history.length - 1) {
                        this.history = this.history.slice(0, this.historyIndex + 1);
                    }

                    this.history.push(state);
                    this.historyIndex = this.history.length - 1;

                    if (this.history.length > 50) {
                        this.history.shift();
                        this.historyIndex--;
                    }

                    this.updateHistoryButtons();
                },

                undo() {
                    if (this.historyIndex > 0) {
                        this.historyIndex--;
                        this.loadFromHistory();
                    }
                },

                redo() {
                    if (this.historyIndex < this.history.length - 1) {
                        this.historyIndex++;
                        this.loadFromHistory();
                    }
                },

                loadFromHistory() {
                    const state = this.history[this.historyIndex];
                    this.canvasBlocks = JSON.parse(JSON.stringify(state.canvasBlocks));
                    this.selectedBlock = state.selectedBlock;
                    this.selectedBlockType = state.selectedBlockType;
                    this.blockProperties = JSON.parse(JSON.stringify(state.blockProperties));
                    this.updateHistoryButtons();
                },

                updateHistoryButtons() {
                    this.canUndo = this.historyIndex > 0;
                    this.canRedo = this.historyIndex < this.history.length - 1;
                },

                // ===== HTML GENERATION =====
                generateHtml() {
                    const canvasContent = document.querySelector('#canvas-content');
                    if (!canvasContent) return '';

                    return `<!DOCTYPE html>
<html lang="${this.currentLanguage}" ${this.canvasDarkMode ? 'class="dark"' : ''}>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page générée - ${this.activePage}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="${this.canvasDarkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-900'}">
    <div class="min-h-screen">
        ${canvasContent.innerHTML}
    </div>
</body>
</html>`;
                },

                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                e.preventDefault();
                window.builderInstance?.undo();
            }
            if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
                e.preventDefault();
                window.builderInstance?.redo();
            }
        });

        // Store instance for keyboard shortcuts
        document.addEventListener('alpine:init', () => {
            Alpine.data('websiteBuilderPro', websiteBuilderPro);
        });
    </script>

    {{-- Store Alpine instance globally for keyboard shortcuts --}}
    <script>
        document.addEventListener('alpine:initialized', () => {
            window.builderInstance = document.querySelector('[x-data]').__x.$data;
        });


    </script>
</body>
</html>
