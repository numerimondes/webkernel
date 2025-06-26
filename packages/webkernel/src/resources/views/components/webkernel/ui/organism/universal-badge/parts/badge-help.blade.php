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
            <x-filament::icon icon="heroicon-m-question-mark-circle" class="help-icon"
                style="width: 18px; height: 18px; max-width: 100%; max-height: 100%;" />
        </button>
    </x-slot>
    <x-filament::dropdown.header>
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-question-mark-circle" class="text-gray-500 dark:text-gray-400"
                style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;" />
            Help & Support
        </div>
    </x-filament::dropdown.header>
    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item href="https://numerimondes.com/docs" tag="a" target="_blank">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-m-book-open" class="text-gray-500 dark:text-gray-400"
                    style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;" />
                <span>Documentation</span>
            </div>
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item href="https://numerimondes.com/support" tag="a" target="_blank">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-m-chat-bubble-left-right" class="text-gray-500 dark:text-gray-400"
                    style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;" />
                <span>Contact Support</span>
            </div>
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item href="https://numerimondes.com/faq" tag="a" target="_blank">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-m-information-circle" class="text-gray-500 dark:text-gray-400"
                    style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;" />
                <span>{{ lang('faq') }}</span>
            </div>
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item href="https://numerimondes.com/bug-report" tag="a" target="_blank">
            <div class="flex items-center gap-3">
                <x-filament::icon icon="heroicon-m-bug-ant" class="text-gray-500 dark:text-gray-400"
                    style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;" />
                <span>{{ lang('report_bug') }}</span>
            </div>
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
