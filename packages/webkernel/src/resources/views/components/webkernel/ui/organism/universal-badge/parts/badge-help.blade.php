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
        <a href="https://numerimondes.com/docs" target="_blank" class="fi-dropdown-list-item fi-ac-grouped-action">
            <x-filament::icon icon="heroicon-m-book-open" class="fi-icon fi-size-md" />
            <span class="fi-dropdown-list-item-label">Documentation</span>
        </a>

        <a href="https://numerimondes.com/support" target="_blank" class="fi-dropdown-list-item fi-ac-grouped-action">
            <x-filament::icon icon="heroicon-m-chat-bubble-left-right" class="fi-icon fi-size-md" />
            <span class="fi-dropdown-list-item-label">Contact Support</span>
        </a>

        <a href="https://numerimondes.com/faq" target="_blank" class="fi-dropdown-list-item fi-ac-grouped-action">
            <x-filament::icon icon="heroicon-m-information-circle" class="fi-icon fi-size-md" />
            <span class="fi-dropdown-list-item-label">{{ lang('faq') }}</span>
        </a>

        <a href="https://numerimondes.com/bug-report" target="_blank" class="fi-dropdown-list-item fi-ac-grouped-action">
            <x-filament::icon icon="heroicon-m-bug-ant" class="fi-icon fi-size-md" />
            <span class="fi-dropdown-list-item-label">{{ lang('report_bug') }}</span>
        </a>
    </x-filament::dropdown.list>
</x-filament::dropdown>
