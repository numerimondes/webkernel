@php
    $selectorData = (new \Webkernel\Applications\Users\Services\PanelSelectorService())->getSelectorData();
    $dropdownLinks = $selectorData['dropdownLinks'] ?? [];
    $currentPanelId = $selectorData['currentPanelId'] ?? null;
    $dropdownPlacement = $selectorData['dropdownPlacement'] ?? 'bottom-end';
@endphp

@if (!empty($dropdownLinks))
    <x-filament::dropdown :placement="$dropdownPlacement" class="module-selector-ui-dropdown">
        <x-slot name="trigger">
            <button type="button" class="module-selector-ui-button"
            style="
            padding: 6px;
            font-size: 14px;
            font-family: sans-serif;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            transition: all 0.2s ease;
        ">
                <x-filament::icon icon="heroicon-o-squares-2x2" class="help-icon w-4 h-4" />
            </button>
        </x-slot>

        <x-filament::dropdown.header 
            class="font-semibold text-gray-900 dark:text-gray-100"
            icon="heroicon-o-ellipsis-horizontal-circle">
            {{ __('Modules disponibles') }}
        </x-filament::dropdown.header>

        <x-filament::dropdown.list class="w-auto min-w-48 fi-dropdown-list">
            @foreach ($dropdownLinks as $link)
                @php
                    $isCurrentPanel = $currentPanelId === $link['panel_id'];
                    $itemClass = $isCurrentPanel 
                        ? 'fi-dropdown-list-item fi-ac-grouped-action disabled bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 opacity-70 cursor-not-allowed select-none pointer-events-none'
                        : 'fi-dropdown-list-item fi-ac-grouped-action';
                @endphp

                @if ($isCurrentPanel)
                    <div class="{{ $itemClass }}" style="user-select: none;">
                        <x-filament::icon icon="{{ $link['icon'] }}" class="fi-icon fi-size-md" />
                        <span class="fi-dropdown-list-item-label">{{ $link['label'] }}</span>
                        <span class="fi-color fi-color-success fi-text-color-700 dark:fi-text-color-300 fi-badge fi-size-sm">
                            Actuel
                        </span>
                    </div>
                @else
                    <a href="{{ $link['href'] }}" class="{{ $itemClass }}">
                        <x-filament::icon icon="{{ $link['icon'] }}" class="fi-icon fi-size-md" />
                        <span class="fi-dropdown-list-item-label">{{ $link['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
@endif