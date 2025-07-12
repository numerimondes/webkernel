@php
    $show_this_badge = true; // Mettez true pour afficher, false pour masquer

    // Récupérer les modules accessibles à l'utilisateur
    $accessibleModules = \Webkernel\Core\Helpers\ModuleAccessHelper::getAccessibleModules(auth()->user());
    
    // Construire la liste des liens pour le dropdown
    $dropdownLinks = [];
    
    foreach ($accessibleModules as $namespace => $namespaceData) {
        foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
            // Panels au niveau module
            if (isset($moduleData['panels']) && count($moduleData['panels']) > 0) {
                foreach ($moduleData['panels'] as $panel) {
                    $panelId = $panel['id'] ?? 'unknown';
                    $panelName = 'Module ' . ucfirst($panelId);
                    
                    $dropdownLinks[] = [
                        'href' => route('filament.' . $panelId . '.pages.dashboard'),
                        'icon' => 'heroicon-m-rectangle-stack',
                        'label' => $panelName,
                        'panel_id' => $panelId
                    ];
                }
            }
            
            // Panels dans les sous-modules
            if (isset($moduleData['submodules'])) {
                foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                    if (isset($submoduleData['panels']) && count($submoduleData['panels']) > 0) {
                        foreach ($submoduleData['panels'] as $panel) {
                            $panelId = $panel['id'] ?? 'unknown';
                            $panelName = 'Module ' . ucfirst($panelId);
                            
                            $dropdownLinks[] = [
                                'href' => route('filament.' . $panelId . '.pages.dashboard'),
                                'icon' => 'heroicon-m-rectangle-stack',
                                'label' => $panelName,
                                'panel_id' => $panelId
                            ];
                        }
                    }
                }
            }
        }
    }
    
    // Variables nécessaires depuis le parent
    $dropdownPlacement = $dropdownPlacement ?? 'top-end';
@endphp

@if(isset($show_this_badge) && $show_this_badge && count($dropdownLinks) > 0)
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
            <x-filament::icon icon="heroicon-o-chevron-up-down" class="help-icon w-4 h-4" />
        </button>
    </x-slot>

    <x-filament::dropdown.header class="font-semibold text-gray-900 dark:text-gray-100" icon="heroicon-m-rectangle-stack">
        {{ lang('available_modules') }}
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