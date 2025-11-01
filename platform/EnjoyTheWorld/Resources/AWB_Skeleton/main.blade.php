
<div class="flex flex-col min-h-screen">
    @includeIf('enjoy-the-world::blocks.navigation-before')
    @includeIf('enjoy-the-world::blocks.navigation')

    <main class="flex-1 relative">
        @includeIf('website-builder::live-website.layouts.partials.flash-messages')
        <div class="animate-fade-in">

        @includeIf('enjoy-the-world::pages.01-home.01-hero')
        @includeIf('enjoy-the-world::pages.01-home.02-welcome-paradise')
        @includeIf('enjoy-the-world::pages.01-home.03-exclusivity-enjoysxm')
        @includeIf('enjoy-the-world::pages.01-home.04-map')
        @includeIf('enjoy-the-world::pages.01-home.05-featured-activities')

            @yield('content')
            @stack('content')
        </div>
    </main>
    @includeIf('enjoy-the-world::blocks.footer')

</div>
