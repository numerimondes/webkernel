@php
    $show_this_badge = true;
    
    // Utiliser le service pour récupérer les panels accessibles
    $dropdownLinks = [];
    if (auth()->check()) {
        $accessService = new \Webkernel\Core\Services\PanelAccessService();
        $accessiblePanels = $accessService->getUserAccessiblePanels(auth()->user());
        
        foreach ($accessiblePanels as $panelId => $panel) {
            $dropdownLinks[] = [
                'href' => '/' . $panelId,
                'icon' => $panel['icon'] ?? 'heroicon-m-rectangle-stack',
                'label' => $panel['name'] ?? 'Module ' . ucfirst($panelId),
                'panel_id' => $panelId
            ];
        }
    }
    
    $dropdownPlacement = $dropdownPlacement ?? 'top-end';
@endphp

@if(isset($show_this_badge) && $show_this_badge && count($dropdownLinks) > 0)
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

    <x-filament::dropdown.header class="font-semibold text-gray-900 dark:text-gray-100" icon="heroicon-o-ellipsis-horizontal-circle">
        {{ __('Modules disponibles') }}
    </x-filament::dropdown.header>

    <x-filament::dropdown.list class="w-auto min-w-48 fi-dropdown-list">
        @foreach ($dropdownLinks as $link)
            <a href="{{ $link['href'] }}" class="fi-dropdown-list-item fi-ac-grouped-action">
                <x-filament::icon icon="{{ $link['icon'] }}" class="fi-icon fi-size-md" />
                <span class="fi-dropdown-list-item-label">{{ $link['label'] }}</span>
            </a>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
@endif