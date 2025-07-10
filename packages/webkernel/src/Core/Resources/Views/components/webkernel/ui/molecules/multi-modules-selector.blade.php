@php
    $modules = ['One', 'Two', 'Three'];
    $currentApp = 'two';
    $hasModules = true;
@endphp
@if($hasModules===true)
<div x-data="{ selectedApp: '{{ strtolower($currentApp) }}' }" class="inline-block">
    <x-filament::dropdown teleport=true flip=true placement="bottom-start">
        <x-slot name="trigger">
            <a href="#" class="fi-dropdown-list-item fi-ac-grouped-action flex items-center gap-2 px-3 py-2 rounded-md cursor-pointer select-none
                transition-colors hover:bg-gray-50 dark:hover:bg-white/5 border border-gray-300"
               @click.prevent="$dispatch('toggle-dropdown')"
            >
                <x-filament::icon icon="heroicon-o-chevron-up-down" class="fi-icon fi-size-md" />
                <span class="fi-dropdown-list-item-label text-sm font-medium">Changer</span>
            </a>
        </x-slot>

        <x-filament::dropdown.header class="font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2" icon="heroicon-c-cog">
            Changer d'application
        </x-filament::dropdown.header>

        <x-filament::dropdown.list class="w-48 max-h-60 overflow-y-auto fi-dropdown-list">
            @foreach ($modules as $module)
                <a
                    href="#"
                    @click.prevent="selectedApp = '{{ strtolower($module) }}'"
                    class="fi-dropdown-list-item fi-ac-grouped-action cursor-pointer whitespace-nowrap flex items-center gap-2 px-3 py-2"
                    :class="selectedApp === '{{ strtolower($module) }}' ? 'bg-gray-100 dark:bg-gray-700' : ''"
                >
                    <x-filament::icon icon="heroicon-o-rectangle-stack" class="h-4 w-4 text-gray-500" />
                    <span class="fi-dropdown-list-item-label">{{ $module }}</span>
                    <span x-show="selectedApp === '{{ strtolower($module) }}'">
                        <x-filament::icon icon="heroicon-m-check" class="h-4 w-4 text-primary-500 ml-auto" />
                    </span>
                </a>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
@endif