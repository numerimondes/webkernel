{{--
/**
 * Webkernel Arcanes Module Landing Page - Builder Pattern
 *
 * SIMPLE USAGE WITH BUILDER:
 *
 * Route::get('tester', fn() => view('components.simple-image-background', [
 *     'builder' => new class {
 *         public $id              = 'tester_module';
 *         public $preset          = 'business';
 *         public $interval        = 2;
 *         public $effects         = ['grain'];
 *         public $useImages       = 1;
 *         public $useBaseGradient = 1;
 *     }
 * ]));
 *
 * FUTURE DATABASE VERSION:
 *
 * Route::get('tester', fn() => view('components.simple-image-background', [
 *     'builder' => new class {
 *         public function __construct() {
 *             $data = DB::table('modules')->where('slug', 'tester')->first();
 *             $this->id = $data->id ?? 'tester_module';
 *             $this->preset = $data->preset ?? 'business';
 *             // ... etc
 *         }
 *     }
 * ]));
 */
--}}

@props(['builder'])

@php
    /**
     * Get module data from ArcaneBuildModule registry by id (fallback to path)
     */
    $moduleData = null;
    if (class_exists('Webkernel\Arcanes\Assemble\ArcaneBuildModule')) {
        try {
            // Try by id first (builder provides an id like 'crm_module')
            $registry = app(\Webkernel\Arcanes\Assemble\ArcaneBuildModule::class);

            // Try by id first
            $byId = $registry->search(['id' => $builder->id ?? null]);
            $moduleData = !empty($byId) ? reset($byId) : null;

            // Fallback to path if not found
            if (!$moduleData && property_exists($builder, 'path') && !empty($builder->path ?? null)) {
                $byPath = $registry->search(['path' => $builder->path]);
                $moduleData = !empty($byPath) ? reset($byPath) : null;
            }
        } catch (\Throwable $e) {
            // Silent fail if registry not available
        }
    }

    /**
     * Extract builder properties with intelligent defaults
     */
    $moduleId = $builder->id ?? 'unknown';
    $preset = $builder->preset ?? 'business';
    $interval = $builder->interval ?? 3;
    $effects = $builder->effects ?? ['grain', 'blur'];
    $transition = $builder->transition ?? 'fade';
    $quality = $builder->quality ?? 'high';
    $useImages = $builder->useImages ?? false;
    $useBaseGradient = $builder->useBaseGradient ?? true;
    $customImages = $builder->customImages ?? null;
    $customOverlay = $builder->customOverlay ?? null;

    // Guard defaults to prevent "Undefined array key"
    $defaults = [
        'id' => $moduleId,
        'name' => ucfirst(str_replace(['_', '-'], ' ', $moduleId)),
        'version' => '1.0.0',
        'description' => 'A Webkernel Arcanes module',
        'moduleType' => 'core',
    ];

    // Merge registry data (if any) over defaults
    $data = is_array($moduleData) ? array_merge($defaults, $moduleData) : $defaults;

    $moduleStatus = $moduleData ? 'Active' : 'Registry Not Found';

    /**
     * Configuration Presets with all visual themes
     */
    $presets = [
        'nature' => [
            'images' => [
                'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&h=1080&fit=crop&q=90',
            ],
            'overlay' => 'linear-gradient(to bottom right, rgba(6,78,59,0.4), transparent, rgba(30,58,138,0.3))',
            'baseGradient' => 'linear-gradient(135deg, #065f46 0%, #1e40af 50%, #1e3a8a 100%)',
        ],
        'tech' => [
            'images' => [
                'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1581090700227-1e7e8f3f9b9b?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1920&h=1080&fit=crop&q=90',
            ],
            'overlay' => 'linear-gradient(to bottom right, rgba(88,28,135,0.5), transparent, rgba(22,78,99,0.4))',
            'baseGradient' => 'linear-gradient(135deg, #581c87 0%, #155e75 50%, #0f172a 100%)',
        ],
        'business' => [
            'images' => [
                'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1556740749-887f6717d7e4?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1523958203904-cdcb402032f3?w=1920&h=1080&fit=crop&q=90',
            ],
            'overlay' => 'linear-gradient(to bottom right, rgba(15,23,42,0.6), transparent, rgba(49,46,129,0.4))',
            'baseGradient' => 'linear-gradient(135deg, #0f172a 0%, #312e81 50%, #1e1b4b 100%)',
        ],
        'minimal' => [
            'images' => [
                'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1581291519195-ef11498d1cf5?w=1920&h=1080&fit=crop&q=90',
            ],
            'overlay' => 'linear-gradient(to bottom right, rgba(17,24,39,0.5), transparent, rgba(15,23,42,0.3))',
            'baseGradient' => 'linear-gradient(135deg, #111827 0%, #0f172a 50%, #020617 100%)',
        ],
        'creative' => [
            'images' => [
                'https://images.unsplash.com/photo-1587614382346-ac7f1f5d1d6f?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1581092334421-1e7e8f3f9b9b?w=1920&h=1080&fit=crop&q=90',
                'https://images.unsplash.com/photo-1581091012184-1e7e8f3f9b9b?w=1920&h=1080&fit=crop&q=90',
            ],
            'overlay' => 'linear-gradient(to bottom right, rgba(131,24,67,0.4), transparent, rgba(124,45,18,0.3))',
            'baseGradient' => 'linear-gradient(135deg, #831843 0%, #7c2d12 50%, #451a03 100%)',
        ],
    ];

    // Get preset configuration
    $config = $presets[$preset] ?? $presets['nature'];

    // Handle image configuration logic
    if ($useImages === false || $useImages === 0) {
        $activeImages = [];
        $currentImage = null;
        $responsiveImage = null;

        // Use custom overlay or appropriate gradient when no images
        if ($customOverlay !== null) {
            $activeOverlay = $customOverlay;
        } elseif ($useBaseGradient) {
            $activeOverlay = $config['baseGradient'];
        } else {
            $activeOverlay = $config['overlay'];
        }
    } elseif ($customImages !== null) {
        // Custom images from builder
        $activeImages = is_array($customImages) ? $customImages : [$customImages];
        $activeOverlay = $customOverlay ?? $config['overlay'];
        $currentIndex = floor(time() / $interval) % count($activeImages);
        $currentImage = $activeImages[$currentIndex];
        $qualityParam = $quality === 'high' ? 'q=95' : ($quality === 'medium' ? 'q=80' : 'q=70');
        $responsiveImage = str_replace('q=90', $qualityParam, $currentImage);
    } else {
        // Use preset images
        $activeImages = $config['images'];
        $activeOverlay = $customOverlay ?? $config['overlay'];
        $currentIndex = floor(time() / $interval) % count($activeImages);
        $currentImage = $activeImages[$currentIndex];
        $qualityParam = $quality === 'high' ? 'q=95' : ($quality === 'medium' ? 'q=80' : 'q=70');
        $responsiveImage = str_replace('q=90', $qualityParam, $currentImage);
    }

    // Ensure effects is array
    if (is_string($effects)) {
        $effects = array_filter(array_map('trim', explode(',', str_replace(['[', ']', '"', "'"], '', $effects))));
    } elseif (!is_array($effects)) {
        $effects = ['grain', 'blur'];
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['name'] }} - Webkernel Module</title>

    {{-- Include Filament styles and fonts --}}
    @filamentStyles
    @filamentScripts
    {{ filament()->getTheme()->getHtml() }}
    {{ filament()->getFontHtml() }}
    {{ filament()->getMonoFontHtml() }}
    {{ filament()->getSerifFontHtml() }}

    {{-- Include Alpine.js --}}

</head>

<body>

    <style>
        /**
         * Responsive Adjustments
         * Removes shadows on mobile for cleaner appearance
         */
        @media (max-width: 640px) {
            .fi-simple-main {
                --tw-ring-shadow: none;
            }
        }

        /**
         * Base Background and Layout System
         * Creates full-screen background with smooth transitions
         */
        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            @if ($responsiveImage)
            background-image: url('{{ $responsiveImage }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            transition: background-image 0.8s ease-in-out;
            @else
            background: {{ $activeOverlay }};
            @endif
            font-family: "SF Pro Display", -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, Arial, sans-serif;
            z-index: 1000;
        }

        /**
         * Custom Font Loading
         * SF Pro Display for modern Apple-like appearance
         */
        @font-face {
            font-family: "SF Pro Display";
            src: url("https://cdn.fontcdn.ir/Fonts/SFProDisplay/5bc1142d5fc993d2ec21a8fa93a17718818e8172dffc649b7d8a3ab459cfbf9c.woff2") format("woff2");
            font-weight: 400;
            font-style: normal;
        }

        /**
         * Gradient Overlay System
         * Creates depth and improves text readability
         */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            background: {{ $activeOverlay }};
            pointer-events: none;
        }

        /**
         * Grain Effect Implementation
         * Adds subtle texture when enabled in effects array
         */
        @if (in_array('grain', $effects))
        body::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            background-image: radial-gradient(circle, transparent 1px, rgba(255, 255, 255, 0.15) 1px);
            background-size: 4px 4px;
            pointer-events: none;
            opacity: 0.3;
        }
        @endif

        /**
         * Main Layout Structure
         * Centers content with proper spacing
         */
        .fi-simple-layout {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .fi-simple-main-ctn {
            width: 100%;
            max-width: 1024px;
        }

        /**
         * Glass Morphism Card Design
         * Creates modern frosted glass effect
         */
        .fi-simple-main {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: fadeIn 0.6s ease-out;
            padding: 2rem !important;
        }

        /**
         * Animation Definitions
         * Smooth entry animations for content elements
         */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /**
         * Status Badge Styling
         * Color-coded status indicators
         */
        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: rgb(34, 197, 94);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-registry-not-found {
            background: rgba(251, 191, 36, 0.2);
            color: rgb(251, 191, 36);
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        /**
         * Section and Component Overrides
         * Custom styling for Filament components
         */
        x-filament\:\:section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin: 1.5rem 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: none !important;
        }

        /**
         * Module Logo Styling
         * Responsive logo with proper spacing
         */
        .module-logo {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .module-logo {
                width: 48px;
                height: 48px;
                margin-right: 0.75rem;
            }
        }

        /**
         * Updated Grid Layout
         * Gives more space to title/description column (2/3) vs status column (1/3)
         */
        .pure-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .pure-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        /**
         * Flex Layout Utilities
         * Reusable flex containers for consistent alignment
         */
        .flex-container {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .flex-column {
            display: flex;
            flex-direction: column;
        }

        .spaced-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /**
         * Right-aligned Status Container
         * Aligns status and module ID to the right
         */
        .status-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 1rem;
            text-align: right;
        }

        /**
         * Title with Version Styling
         * Allows version to be displayed inline with title
         */
        .title-version-container {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /**
         * Compact Module Info Grid
         * Reduces spacing for module information display
         */
        .module-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .module-info-item {
            display: flex;
            flex-direction: column;
        }

        .module-info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .module-info-value {
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>

    <div>
        <div class="fi-simple-layout">
            <div class="fi-simple-main-ctn">
                <div class="fi-simple-main fi-width-lg">
                    {{-- SECTION: Module Information Display --}}
                    <div class="slide-up text-white/90 space-y-8" style="animation-delay: 0.2s;">
                        <x-filament::section>
                            {{-- ROW 1: Two columns with improved spacing --}}
                            <div class="">
                                {{-- LEFT COLUMN: Logo + Title + Description (2/3 width) --}}
                                <div class="flex-container">
                                    <div class="shrink-0">
                                        <x-dynamic-asset path="app/tester/Resources/Assets/Branding/Logo/numerimondes.png" width="48" height="48" alt="Logo" />
                                    </div>
                                    <div class="flex-column">
                                        {{-- Title with optional inline version --}}
                                        <div class="title-version-container">
                                            <h1 class="text-2xl font-bold bg-gradient-to-r from-white via-blue-100 to-purple-200 bg-clip-text text-transparent" title="Module ID: {{ $data['id'] }}">
                                                {{ $data['name'] }}
                                            </h1>
                                            <span class="">v{{ $data['version'] }}</span>
                                            <span class="status-badge {{ $moduleData ? 'status-active' : 'status-registry-not-found' }}">
                                                {{ $moduleStatus }}
                                            </span>
                                        </div>
                                        {{-- Muted description --}}
                                        <p class="">
                                            {{ $data['description'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </x-filament::section>
                    </div>
                    {{-- SECTION 2: Action buttons and slide-over --}}
                    <div class="slide-up space-y-6 text-white/90" style="animation-delay: 0.4s;">
                        <x-filament::section style="margin-top:1.9rem !important;">
                            <h3
                            class="text-lg font-semibold mb-4 text-white"
                            style="margin-bottom: 0.5rem !important;">
                            <strong>Quick Actions</strong>
                            </h3>
                            <div class="flex flex-wrap gap-x-4 gap-y-4">

                                {{-- Documentation button --}}
                                <x-filament::button href="https://filamentphp.com/docs" tag="a" color="gray" icon="heroicon-o-book-open">
                                    Documentation
                                </x-filament::button>

                                {{-- Advanced details slide-over trigger --}}
                                <x-filament::button x-data x-on:click="$dispatch('open-modal', { id: 'module-advanced-details' })" color="info" icon="heroicon-o-information-circle">
                                    Details
                                </x-filament::button>

                                {{-- Debug information button --}}
                                @if ($moduleData)
                                    <x-filament::button href="/modules/debug/{{ $data['id'] }}" tag="a" color="warning" icon="heroicon-o-bug-ant">
                                        Debug Info
                                    </x-filament::button>
                                @endif

                            </div>
                        </x-filament::section>
                    </div>

                    {{-- Registry warning --}}
                    @if (!$moduleData)
                        <div class="slide-up" style="animation-delay: 0.6s;">
                            <x-filament::section>
                                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                                    <h4 class="text-yellow-300 font-semibold mb-2 flex items-center">
                                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 mr-2" />
                                        Registry Notice
                                    </h4>
                                    <p class="text-yellow-200/80 text-sm">
                                        This module is not registered in ArcaneBuildModule registry. Using fallback
                                        configuration data.
                                    </p>
                                    <p class="text-yellow-200/60 text-xs mt-2">
                                        To register: Call build() method on your ArcaneBuildModule instance.
                                    </p>
                                </div>
                            </x-filament::section>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Module Advanced Details Slide-over --}}
    <x-filament::modal id="module-advanced-details" slide-over width="2xl">

        <x-slot name="heading">
            Module Advanced Details
        </x-slot>

        <x-slot name="description">
            Complete technical information for {{ $data['name'] }} module
        </x-slot>

        <div class="space-y-6">
            {{-- Builder Configuration Display --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Builder Configuration
                </h4>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Preset</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $preset }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Background Images</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $useImages ? 'Enabled (' . count($activeImages) . ' images)' : 'Disabled' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rotation Interval</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $interval }}s</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Effects</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ implode(', ', $effects) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Dynamic Elements Section --}}
            @if ($moduleData && isset($moduleData['_dynamicElements']) && !empty($moduleData['_dynamicElements']))
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Dynamic Properties
                    </h4>
                    <div class="space-y-3">
                        @foreach ($moduleData['_dynamicElements'] as $elementName => $elementData)
                            <div
                                class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-start justify-between mb-2">
                                    <h5
                                        class="text-sm font-medium text-gray-900 dark:text-gray-100 uppercase tracking-wide">
                                        {{ $elementName }}
                                    </h5>
                                    @if ($elementData['required'])
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Required
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    @if (is_array($elementData['value']))
                                        @php
                                            function arrayToString($array, $depth = 0)
                                            {
                                                if ($depth > 2) {
                                                    return '[...]';
                                                }
                                                $result = [];
                                                foreach ($array as $key => $value) {
                                                    if (is_array($value)) {
                                                        $result[] =
                                                            (is_string($key) ? $key . ': ' : '') .
                                                            '[' .
                                                            arrayToString($value, $depth + 1) .
                                                            ']';
                                                    } else {
                                                        $result[] = is_string($key) ? $key . ': ' . $value : $value;
                                                    }
                                                }
                                                return implode(', ', $result);
                                            }
                                        @endphp
                                        <code class="text-xs">{{ arrayToString($elementData['value']) }}</code>
                                    @else
                                        <code class="text-xs">{{ $elementData['value'] }}</code>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Providers Section --}}
            @if ($moduleData && (!empty($moduleData['providers']) || !empty($moduleData['panelproviders'])))
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Service Providers
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if (!empty($moduleData['providers']))
                            <div
                                class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    Standard Providers
                                </h5>
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ count($moduleData['providers']) }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    provider(s) registered
                                </div>
                            </div>
                        @endif

                        @if (!empty($moduleData['panelproviders']))
                            <div
                                class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    Panel Providers
                                </h5>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ count($moduleData['panelproviders']) }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    panel provider(s) registered
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Module Configuration Section --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Module Configuration
                </h4>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Module Name</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $data['name'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Version</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $data['version'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Module ID</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ $data['id'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">
                                {{ ucfirst($data['moduleType'] ?? 'core') }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $data['description'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <x-slot name="footerActions">
            <x-filament::button x-on:click="$dispatch('close-modal', { id: 'module-advanced-details' })"
                color="gray">
                Close
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    {{-- Background Image Rotation System --}}
    @if ($useImages && !empty($activeImages))
    <script>
       document.addEventListener('DOMContentLoaded', () => {

            const images = @json($activeImages);
            const interval = {{ $interval * 1000 }};
            const qualityParam = '{{ $qualityParam ?? 'q=90' }}';
            let currentIndex = {{ $currentIndex ?? 0 }};

            /**
             * Preload Image Function
             * Ensures smooth transitions by loading images before display
             * @param {string} src - Image URL to preload
             * @returns {Promise} Promise that resolves when image loads
             */
            function preloadImage(src) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.onerror = reject;
                    img.src = src;
                });
            }

            /**
             * Update Background Function
             * Switches to next image in rotation with preloading
             */
            function updateBackground() {
                currentIndex = (currentIndex + 1) % images.length;
                const newImageSrc = images[currentIndex].replace('q=90', qualityParam);

                preloadImage(newImageSrc)
                    .then(() => {
                        document.body.style.backgroundImage = `url('${newImageSrc}')`;
                    })
                    .catch((error) => {
                        console.warn('Background image loading error:', error);
                    });
            }

            /**
             * Preload Next Image Function
             * Loads upcoming image in background for smooth transition
             */
            function preloadNextImage() {
                const nextIndex = (currentIndex + 1) % images.length;
                const nextImageSrc = images[nextIndex].replace('q=90', qualityParam);
                preloadImage(nextImageSrc).catch(() => {
                    // Silent fail for preloading errors
                });
            }

            // Start rotation timer and initial preload
            setInterval(() => {
                updateBackground();
                preloadNextImage();
            }, interval);

            // Preload next image on page load
            preloadNextImage();
        });
    </script>
    @endif

</body>

</html>




{{--
==============================================================================
🚀 SIMPLIFIED USAGE EXAMPLES
==============================================================================

// BASIC USAGE - Just specify builder properties
Route::prefix('modules/landing')->group(function () {

    Route::get('tester', fn() => view('components.simple-image-background', [
        'builder' => new class {
            public $id              = 'tester_module';
            public $preset          = 'business';
            public $interval        = 2;
            public $effects         = ['grain'];
            public $useImages       = 1;
            public $useBaseGradient = 1;
        }
    ]));

    Route::get('analytics', fn() => view('components.simple-image-background', [
        'builder' => new class {
            public $id       = 'analytics_module';
            public $preset   = 'tech';
            public $interval = 5;
            public $effects  = ['grain', 'blur'];
        }
    ]));

    Route::get('dashboard', fn() => view('components.simple-image-background', [
        'builder' => new class {
            public $id              = 'dashboard_module';
            public $preset          = 'minimal';
            public $useImages       = 0; // No images, just gradient
            public $useBaseGradient = 1;
        }
    ]));

})->name('modules.landing');

==============================================================================
🎨 AVAILABLE PRESETS
==============================================================================

$preset = 'nature'    // Green/blue nature scenes
$preset = 'tech'      // Technology/code backgrounds
$preset = 'business'  // Professional office scenes
$preset = 'minimal'   // Clean, minimal backgrounds
$preset = 'creative'  // Artistic/creative imagery

==============================================================================
🛠 BUILDER PROPERTIES REFERENCE
==============================================================================

REQUIRED:
public $id = 'module_name';              // Module identifier

OPTIONAL VISUAL:
public $preset = 'business';             // Theme preset
public $interval = 5;                    // Image rotation (seconds)
public $effects = ['grain', 'blur'];     // Visual effects array
public $useImages = 1;                   // Enable background images (1/0)
public $useBaseGradient = 1;             // Use gradient when no images (1/0)

OPTIONAL ADVANCED:
public $customImages = ['url1', 'url2']; // Override preset images
public $customOverlay = 'linear-gradient(...)'; // Custom overlay CSS
public $quality = 'high';               // Image quality (high/medium/low)
public $transition = 'fade';            // Transition type

==============================================================================
📊 DATABASE EVOLUTION READY
==============================================================================

Future database version (exactly what you want):

Route::get('tester', fn() => view('components.simple-image-background', [
    'builder' => new class {
        public function __construct() {
            $data = DB::table('modules')->where('slug', 'tester')->first();

            $this->id              = $data->id ?? 'tester_module';
            $this->preset          = $data->preset ?? 'business';
            $this->interval        = $data->interval ?? 2;
            $this->effects         = $data->effects ? explode(',', $data->effects) : ['grain'];
            $this->useImages       = $data->use_images ?? 1;
            $this->useBaseGradient = $data->use_base_gradient ?? 1;
        }
    }
]));

Database table structure:
CREATE TABLE modules (
    id VARCHAR(255) PRIMARY KEY,
    slug VARCHAR(255) UNIQUE,
    preset VARCHAR(50) DEFAULT 'nature',
    interval INT DEFAULT 300,
    effects TEXT, -- comma-separated: 'grain,blur'
    use_images BOOLEAN DEFAULT 1,
    use_base_gradient BOOLEAN DEFAULT 1,
    custom_images JSON NULL,
    custom_overlay TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

==============================================================================
--}}
