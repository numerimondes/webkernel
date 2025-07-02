@php
    $dropdownLinks = [
        [
            'href' => '#',
            'icon' => 'heroicon-m-book-open',
            'label' => lang( 'documentation'),
        ],
        [
            'href' => '#',
            'icon' => 'heroicon-m-chat-bubble-left-right',
            'label' => lang( 'contact_support'),
        ],
        [
            'href' => '#',
            'icon' => 'heroicon-m-information-circle',
            'label' => lang('faq'),
        ],
        [
            'href' => '#',
            'icon' => 'heroicon-m-bug-ant',
            'label' => lang('report_bug'),
        ],
    ];
@endphp

<x-filament::dropdown :placement="$dropdownPlacement">
    <x-slot name="trigger">
        <button type="button" class="universal-help-ui-button"
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
            <x-filament::icon icon="heroicon-m-question-mark-circle" class="help-icon w-4 h-4" />
        </button>
    </x-slot>

    <x-filament::dropdown.list class="fi-dropdown-list">
        @foreach ($dropdownLinks as $link)
            <a href="{{ $link['href'] }}" target="_blank" class="fi-dropdown-list-item fi-ac-grouped-action">
                <x-filament::icon icon="{{ $link['icon'] }}" class="fi-icon fi-size-md" />
                <span class="fi-dropdown-list-item-label">{{ $link['label'] }}</span>
            </a>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
