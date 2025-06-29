@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .numerimondes-logo {
font-family: 'Poppins', sans-serif !important;
font-weight: bold !important;
    }
    .numerimondes-dot {
        color: var(--primary-600, #3b82f6) !important;
    }
</style>
@endpush

@php
$adaptation_sys_logo = 'large'; // small, medium, large

$logo_sizes = [
    'small' => [
        'class' => 'font-medium text-current truncate',
        'style' => 'font-size: 0.875rem; line-height: 1rem;'
    ],
    'medium' => [
        'class' => 'font-medium text-current',
        'style' => 'font-size: 1.125rem; line-height: 1.25rem;'
    ],
    'large' => [
        'class' => 'font-medium text-current',
        'style' => 'font-size: 1.7rem; line-height: 1.25rem;'
    ]
];

$current_size = $logo_sizes[$adaptation_sys_logo];
@endphp

<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="fi-filament-info-widget-main">
            <a href="https://numerimondes.com" rel="noopener noreferrer" target="_blank">
                <!-- Logo Numerimondes avec tailles adaptatives -->
                <div class="fi-filament-info-widget-logo-container flex items-center justify-center overflow-hidden" style="max-height: 59px; max-width: 302px; width: 100%;">
                    <span class="{{ $current_size['class'] }} numerimondes-logo"
                          style="font-family: 'Poppins', sans-serif; font-weight: 500; {{ $current_size['style'] }} white-space: nowrap; display: flex; align-items: baseline; color: currentColor;">
                        <span>Numerimondes</span><span class="numerimondes-dot" style="color: var(--primary-600, #3b82f6);">.</span>
                    </span>
                </div>
            </a>

            <p class="fi-filament-info-widget-version text-xs text-gray-600" style="margin-top: 0.25rem;">
                <span x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="flex items-center space-x-3 cursor-pointer">
                    <span>Webkernel</span>
                    <span style="margin-left:4px;">v{{ Webkernel\constants\Application::WEBKERNEL_VERSION }}</span>
                </span>

                <span x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="flex items-center space-x-3 cursor-pointer" style="margin-left:4px;">
                    <span x-show="!hovered" class="font-bold block min-w-[1rem] text-center">F</span>
                    <span x-show="hovered" class="block min-w-[6rem]" style="margin-left:4px;">Filament</span>
                    <span style="margin-left:4px;">{{ \Composer\InstalledVersions::getPrettyVersion('filament/filament') }}</span>
                </span>

                <span x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="flex items-center space-x-3 cursor-pointer" style="margin-left:4px;">
                    <span x-show="!hovered" class="font-bold block min-w-[1rem] text-center">L</span>
                    <span x-show="hovered" class="block min-w-[6rem]" style="margin-left:4px;">Laravel</span>
                    <span style="margin-left:4px;">v{{ Illuminate\Foundation\Application::VERSION }}</span>
                </span>
            </p>
        </div>

        <div class="fi-filament-info-widget-links">
            <x-filament::link
                color="gray"
                href="https://numerimondes.com/docs"
                :icon="\Filament\Support\Icons\Heroicon::BookOpen"
                icon-alias="panels::widgets.filament-info.open-documentation-button"
                rel="noopener noreferrer"
                target="_blank"
            >
                Documentation
            </x-filament::link>

            <x-filament::link
                color="gray"
                href="https://github.com/numerimondes"
                icon-alias="panels::widgets.filament-info.open-github-button"
                rel="noopener noreferrer"
                target="_blank"
            >
                <x-slot name="icon">
                    <svg viewBox="0 0 98 96" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4">
                        <path
                            clip-rule="evenodd"
                            fill="currentColor"
                            fill-rule="evenodd"
                            d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"
                        />
                    </svg>
                </x-slot>
                GitHub
            </x-filament::link>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
