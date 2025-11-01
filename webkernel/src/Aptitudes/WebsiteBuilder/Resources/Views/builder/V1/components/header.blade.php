@php
    use Webkernel\Aptitudes\WebsiteBuilder\WebsiteBuilderService;

    $service = app(WebsiteBuilderService::class);
    $projectsData = $service->listProjects(1, 100);
    $projects = $projectsData['data'];
    $currentProject = $project ?? $projects->first();

    $leftActions = [
        [
            'icon' => 'plus',
            'label' => 'Insert',
            'tooltip' => 'Insert new elements',
            'link' => '#',
            'primary' => true,
            'showText' => 'md',
        ],
        [
            'icon' => 'layout-dashboard',
            'label' => 'Layout',
            'tooltip' => 'Manage layout structure',
            'link' => '#',
            'showText' => 'md',
        ],
        [
            'icon' => 'type',
            'label' => 'Text',
            'tooltip' => 'Add text elements',
            'link' => '#',
            'showText' => 'md',
        ],
        [
            'icon' => 'pen-tool',
            'label' => 'Vector',
            'tooltip' => 'Create vector graphics',
            'link' => '#',
            'showText' => 'md',
        ],
        [
            'icon' => 'layers',
            'label' => 'CMS',
            'tooltip' => 'Content management system',
            'link' => '#',
            'showText' => 'md',
        ],
    ];

    $rightActions = [
        [
            'icon' => 'globe',
            'label' => 'Public',
            'tooltip' => 'View public site',
            'link' => '#',
            'iconOnly' => true,
        ],
        [
            'icon' => 'eye',
            'label' => 'Preview',
            'tooltip' => 'Preview changes',
            'link' =>
                $currentProject && $currentProject->slug
                    ? route('website-builder.preview', $currentProject->slug)
                    : '#',
            'iconOnly' => true,
            'target' => '_blank',
        ],
        [
            'icon' => 'external-link',
            'label' => 'Previewer',
            'tooltip' => 'Open previewer page',
            'link' =>
                $currentProject && $currentProject->slug
                    ? route('website-builder.previewer', $currentProject->slug)
                    : '#',
            'iconOnly' => true,
        ],
        [
            'icon' => 'bar-chart-3',
            'label' => 'Analytics',
            'tooltip' => 'View analytics',
            'link' => '#',
            'iconOnly' => true,
        ],
        [
            'icon' => 'play',
            'label' => 'Test',
            'tooltip' => 'Run tests',
            'link' => '#',
            'iconOnly' => true,
        ],
        [
            'icon' => 'user-plus',
            'label' => 'Invite',
            'tooltip' => 'Invite team members',
            'sound' => 'notification-sent',
            'link' => '#',
            'showText' => 'lg',
        ],
        [
            'icon' => 'upload',
            'label' => 'Publish',
            'tooltip' => 'Publish your project',
            'sound' => 'notification-sent',
            'link' => '#',
            'primary' => true,
            'showText' => 'md',
        ],
    ];
@endphp

<div class="bg-black px-3 py-1.5 relative grid grid-cols-3 items-center" x-data="{
    dropdownOpen: false,
    leftCollapsed: localStorage.getItem('wkb-leftCollapsed') === 'true' || false,
    toggleGlassmorphism() {
        this.dropdownOpen = !this.dropdownOpen;
        window.dispatchEvent(new CustomEvent('glassmorphism-toggle', { detail: { active: this.dropdownOpen } }));
    }
}"
    x-on:dropdown-toggle.window="dropdownOpen = $event.detail.open" x-init="// Listen for sidebar collapse changes
    window.addEventListener('storage', (e) => {
        if (e.key === 'wkb-leftCollapsed') {
            this.leftCollapsed = e.newValue === 'true';
        }
    });">

    <!-- Left Section -->
    <div class="flex items-center space-x-1.5 justify-start">
        <div class="wkb-logo-container" :class="{ 'collapsed': leftCollapsed }">
            <x-base::UIQuery  element="logo" module="website-builder" width="35" height="35"
                inject-style="fill: white !important; stroke: white !important;" inject-class="mr-2" />
        </div>

        @foreach ($leftActions as $action)
            <a href="{{ $action['link'] }}" onclick="playSound('{{ $action['sound'] ?? 'click' }}'); return true;"
                class="group relative inline-flex items-center {{ isset($action['primary']) && $action['primary'] ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-800 hover:bg-gray-700 text-white' }}
                      px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors duration-150">
                <x-dynamic-component :component="'lucide-' . $action['icon']" class="w-3.5 h-3.5" />
                @if (isset($action['showText']))
                    <span
                        class="hidden {{ $action['showText'] }}:ml-1.5 {{ $action['showText'] }}:inline">{{ $action['label'] }}</span>
                    <div
                        class="{{ $action['showText'] }}:hidden absolute top-full left-1/2 transform -translate-x-1/2 mt-1 bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                        {{ $action['tooltip'] }}
                        <div
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-b-2 border-transparent border-b-gray-900">
                        </div>
                    </div>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Center - Project Title -->
    <div class="flex justify-center">
        <div x-data="{ open: false }" class="relative flex justify-center" x-cloak>
            <button @click="open = !open; toggleGlassmorphism(); $dispatch('dropdown-toggle', { open: open })"
                class="flex items-center space-x-2 px-2 py-1 rounded text-sm font-medium text-white hover:bg-gray-700/50 transition-colors">
                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                <span>{{ $currentProject ? $currentProject->name : 'Untitled' }}</span>
                <x-lucide-chevron-down class="w-3 h-3 text-gray-400" x-bind:class="{ 'rotate-180': open }" />
            </button>

            <div x-show="open"
                @click.away="open = false; toggleGlassmorphism(); $dispatch('dropdown-toggle', { open: false })"
                x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="fixed left-1/2 top-16 w-80 bg-white dark:bg-black rounded-md shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
                style="transform: translateX(-50%);">

                <a href="javascript:void(0)"
                    class="flex items-center space-x-2 px-3 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                    <x-lucide-plus class="w-4 h-4" />
                    <span>New Project</span>
                </a>

                @forelse($projects as $projectItem)
                    <div
                        class="grid grid-cols-[auto_auto_1fr_auto] items-stretch w-full text-sm text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer {{ $currentProject && $currentProject->id === $projectItem->id ? 'bg-gray-100 dark:bg-gray-800' : '' }}">

                        <div class="flex items-center px-3 py-2">
                            <div
                                class="w-1.5 h-1.5 rounded-full flex-shrink-0 @if ($projectItem->status === 'active') bg-green-400 @elseif($projectItem->status === 'draft') bg-yellow-400 @else bg-gray-400 @endif">
                            </div>
                        </div>

                        <div class="flex items-center px-2 py-2">
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-green-900/80 text-green-200 rounded">
                                <x-lucide-sparkles class="w-3 h-3 mr-0.5" /> Free
                            </span>
                        </div>

                        <a href="{{ $projectItem->slug && $projectItem->id ? route('filament.system.resources.website-projects.website-builder', $projectItem->id) : '#' }}"
                            class="flex items-center min-w-0 px-2 py-2">
                            <span class="truncate" title="{{ $projectItem->name }}">{{ $projectItem->name }}</span>
                        </a>

                        <a href="{{ $projectItem->slug && $projectItem->id ? route('filament.system.resources.website-projects.website-builder', $projectItem->id) : '#' }}"
                            target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center px-4 py-2 border-l border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                            title="Open in new tab" onclick="event.stopPropagation();">
                            <x-lucide-external-link
                                class="w-4 h-4 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" />
                        </a>
                    </div>
                @empty
                    <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 text-center">No projects</div>
                @endforelse

            </div>
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center space-x-1.5 justify-end">
        <div
            class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold mr-2">
            SV
        </div>

        @foreach ($rightActions as $action)
            @if (isset($action['onclick']))
                <button onclick="{{ $action['onclick'] }}; playSound('{{ $action['sound'] ?? 'click' }}');"
                    class="group relative inline-flex items-center {{ isset($action['primary']) && $action['primary'] ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-800 hover:bg-gray-700 text-white' }}
                              px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors duration-150">
                @else
                    <a href="{{ $action['link'] }}"
                        @if (isset($action['target'])) target="{{ $action['target'] }}" @endif
                        onclick="playSound('{{ $action['sound'] ?? 'click' }}'); return true;"
                        class="group relative inline-flex items-center {{ isset($action['primary']) && $action['primary'] ? 'bg-blue-500 hover:bg-blue-600 text-white' : 'bg-gray-800 hover:bg-gray-700 text-white' }}
                          px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors duration-150">
            @endif
            <x-dynamic-component :component="'lucide-' . $action['icon']" class="w-3.5 h-3.5" />
            @if (isset($action['iconOnly']) && $action['iconOnly'])
                <div
                    class="absolute top-full left-1/2 transform -translate-x-1/2 mt-1 bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                    {{ $action['tooltip'] }}
                    <div
                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-b-2 border-transparent border-b-gray-900">
                    </div>
                </div>
            @elseif(isset($action['showText']))
                <span
                    class="hidden {{ $action['showText'] }}:ml-1.5 {{ $action['showText'] }}:inline">{{ $action['label'] }}</span>
                <div
                    class="{{ $action['showText'] }}:hidden absolute top-full left-1/2 transform -translate-x-1/2 mt-1 bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                    {{ $action['tooltip'] }}
                    <div
                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-b-2 border-transparent border-b-gray-900">
                    </div>
                </div>
            @endif
            @if (isset($action['onclick']))
                </button>
            @else
                </a>
            @endif
        @endforeach
    </div>
</div>

<script>
    // Glassmorphism overlay manager - can be called from anywhere in the app
    window.GlassmorphismManager = {
        overlay: null,

        init() {
            if (!this.overlay) {
                this.overlay = document.createElement('div');
                this.overlay.className =
                    'fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition-all duration-300 ease-out opacity-0 backdrop-blur-none pointer-events-none';
                document.body.appendChild(this.overlay);
            }

            window.addEventListener('glassmorphism-toggle', (e) => {
                this.toggle(e.detail.active);
            });
        },

        show() {
            if (this.overlay) {
                this.overlay.style.pointerEvents = 'auto';
                this.overlay.classList.remove('opacity-0', 'backdrop-blur-none');
                this.overlay.classList.add('opacity-100', 'backdrop-blur-sm');
            }
        },

        hide() {
            if (this.overlay) {
                this.overlay.style.pointerEvents = 'none';
                this.overlay.classList.remove('opacity-100', 'backdrop-blur-sm');
                this.overlay.classList.add('opacity-0', 'backdrop-blur-none');
            }
        },

        toggle(active) {
            active ? this.show() : this.hide();
        }
    };

    // Auto-initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        window.GlassmorphismManager.init();
    });

    // Preview window handler
    function openPreviewWindow(url) {
        if (url) {
            window.open(url, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        }
    }

    // Listen for Livewire events
    // Livewire references removed to prevent layout conflicts
    // document.addEventListener('livewire:load', function() {
    //     Livewire.on('openPreviewWindow', function(url) {
    //         openPreviewWindow(url);
    //     });
    // });
</script>
