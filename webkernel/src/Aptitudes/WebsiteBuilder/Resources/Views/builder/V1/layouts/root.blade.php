<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="wkb-root">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $this->getTitle() }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Initialize dark theme immediately to prevent flickering -->
    <script>
        // Force dark mode immediately
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    </script>

    <!-- Load WebkernelBuilder assets -->
    <x-base::UIQuery module="website-builder" scope="builder/V1" types="js,css" recursive />
    <!-- WebkernelBuilder Tailwind configuration is loaded via dynamic-asset -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- WebkernelBuilder styles are loaded via dynamic-asset component -->

    <!-- Livewire Styles -->
    @livewireStyles
</head>

<body class="wkb-root h-screen overflow-hidden" x-data="{}"
    wire:key="wkb-page-editor-{{ $project->id ?? 'default' }}-{{ $page->id ?? 'new' }}">

    <div class="wkb-layout" x-data="{
        showLoading: true,
        init() {
            // Wait for everything to be ready, then show
            setTimeout(() => {
                this.showLoading = false;
                this.$el.classList.add('ready');
            }, 100);
        }
    }" x-init="init()">
        <!-- Loading Screen -->
        <div class="wkb-loading" x-show="showLoading" x-transition:leave="transition ease-out duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="wkb-loading-spinner"></div>
        </div>
        {{-- Load WebkernelBuilder component styles --}}
        <x-base::UIQuery module="website-builder" scope="builder/V1/components" types="js,css" recursive />

        <!-- WebkernelBuilder Header -->
        <div class="wkb-header">
            @includeIf('website-builder::builder.V1.components.header')
        </div>

        <!-- Main Builder Parts -->
        @includeIf('website-builder::builder.V1.components.main-builder-parts')

        <!-- WebkernelBuilder Dynamic Island - Inside Canvas -->
        @includeIf('website-builder::builder.V1.components.dynamic-island')

        <!-- WebkernelBuilder Context Menu -->
        @includeIf('website-builder::builder.V1.components.context-menu')

        <!-- WebkernelBuilder Modals -->
        @includeIf('website-builder::builder.V1.modals.page-settings')
        @includeIf('website-builder::builder.V1.modals.block-library')
        @includeIf('website-builder::builder.V1.modals.confirm-delete')
    </div>


    <!-- Load scripts in correct order to prevent Alpine.js conflicts -->
    @livewireScripts

</body>

</html>
