@php
use Webkernel\Aptitudes\I18n\Resources\Views\components\LanguageSelector\LanguageSelector;

$language_selector = new LanguageSelector([]);
$isTopbar = filament()->getDatabaseNotificationsPosition() === \Filament\Enums\DatabaseNotificationsPosition::Topbar;
$isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();

$iconConfig = [
    'topbar'   => ['size' => 20, 'class' => 'fi-btn-size-lg fi-icon-btn fi-icon-btn-size-lg'],
    'sidebar'  => ['size' => 20, 'class' => 'p-1'],
    'dropdown' => ['size' => 16, 'class' => '']
];

$currentContext = $isTopbar ? 'topbar' : 'sidebar';
$triggerIconSize = $iconConfig[$currentContext]['size'];
$triggerIconClass = $iconConfig[$currentContext]['class'];
$dropdownIconSize = $iconConfig['dropdown']['size'];
@endphp

<style>
.svg-color-grayed {
    color: var(--gray-400);
}
.dark .svg-color-grayed {
    color: var(--gray-500);
}
</style>

<div
    x-data="{
        selectedLang: '{{ $language_selector->currentCode }}',
        isLoading: false,
        changeLang(lang) {
            if (this.isLoading) return;
            this.isLoading = true;
            this.selectedLang = lang;

            // Navigate to language switch route
            window.location.href = '{{ $language_selector->changeUrl }}/' + lang;
        }
    }"
    id="{{ $language_selector->getId() }}"
>
    <x-filament::dropdown teleport placement="bottom-start">
        <x-slot name="trigger">
            @if($isTopbar)
                {{-- Topbar version --}}
                <button
                    type="button"
                    class="fi-topbar-database-notifications-btn {{ $triggerIconClass }}"
                    aria-label="{{ lang('Available Languages') }}"
                    x-bind:disabled="isLoading"
                >
                    @if($language_selector->showFlags && $language_selector->currentLanguage)
                        <x-base::UIQuery
                            path="webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/{{ $language_selector->currentLanguage->code }}.svg"
                            inject-class="fi-icon fi-size-lg"
                            inject-style="height: {{ $triggerIconSize }}px; width: {{ $triggerIconSize }}px;"
                            width="{{ $triggerIconSize }}"
                            height="{{ $triggerIconSize }}" />
                    @else
                        @svg('heroicon-o-language', 'fi-icon fi-size-lg svg-color-grayed')
                    @endif
                </button>
            @else
                {{-- Sidebar version --}}
                <button
                    class="fi-sidebar-database-notifications-btn"
                    x-bind:disabled="isLoading"
                >
                    <span class="{{ $triggerIconClass }}">
                        @if($language_selector->showFlags && $language_selector->currentLanguage)
                            <x-base::UIQuery
                                path="webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/{{ $language_selector->currentLanguage->code }}.svg"
                                inject-class="fi-icon fi-size-lg"
                                inject-style="height: {{ $triggerIconSize }}px; width: {{ $triggerIconSize }}px;"
                                width="{{ $triggerIconSize }}"
                                height="{{ $triggerIconSize }}" />
                        @else
                            @svg('heroicon-o-language', 'fi-icon fi-size-lg svg-color-grayed')
                        @endif
                    </span>
                    <span
                        @if ($isSidebarCollapsibleOnDesktop)
                            x-show="$store.sidebar.isOpen"
                            x-transition:enter="fi-transition-enter"
                            x-transition:enter-start="fi-transition-enter-start"
                            x-transition:enter-end="fi-transition-enter-end"
                        @endif
                        class="fi-sidebar-database-notifications-btn-label"
                    >
                        {{ lang('Available Languages') }}
                    </span>
                </button>
            @endif
        </x-slot>

        <x-filament::dropdown.header
            class="font-semibold text-gray-900 dark:text-gray-100"
            icon="heroicon-c-language">
            {{ lang('Available Languages') }}
        </x-filament::dropdown.header>

        <x-filament::dropdown.list class="w-40 max-h-60 overflow-y-auto">
            @forelse ($language_selector->languages as $language)
                <a
                    href="javascript:void(0)"
                    @click.prevent="changeLang('{{ $language->code }}')"
                    wire:key="language-{{ $language->code }}"
                    class="fi-dropdown-list-item cursor-pointer whitespace-nowrap flex items-center gap-2 px-3 py-2 rounded-md transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
                    :class="{
                        'bg-gray-100 dark:bg-gray-700': selectedLang === '{{ $language->code }}',
                        'opacity-50 pointer-events-none': isLoading
                    }"
                >
                    @if($language_selector->showFlags)
                        <x-base::UIQuery
                            path="webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/{{ $language->code }}.svg"
                            inject-class="fi-icon"
                            inject-style="height: {{ $dropdownIconSize }}px; width: {{ $dropdownIconSize }}px;"
                            width="{{ $dropdownIconSize }}"
                            height="{{ $dropdownIconSize }}" />
                    @endif
                    <span class="fi-dropdown-list-item-label flex-1">
                        {{ $language->getDisplayName() }}
                    </span>
                    <span x-show="selectedLang === '{{ $language->code }}'">
                        <x-filament::icon
                            name="heroicon-m-check"
                            class="h-4 w-4 text-primary-500" />
                    </span>
                </a>
            @empty
                <div class="fi-dropdown-list-item text-gray-500 text-sm px-3 py-2">
                    {{ lang('No Available Languages') }}
                </div>
            @endforelse
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
