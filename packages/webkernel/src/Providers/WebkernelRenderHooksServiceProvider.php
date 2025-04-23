<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\FilamentManager;
use Webkernel\Observers\LanguageTranslationObserver;

class WebkernelRenderHooksServiceProvider extends ServiceProvider
{
    /**
     * Register additional services if needed.
     *
     * @return void
     */
    public function register(): void
    {
        // You may bind services or perform registrations here in the future.
    }

    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        // Register render hooks used by Filament.
        $this->registerPanelsRenderHooks();

        // Ensure the sidebar is collapsible on desktop devices.
        $this->forceSidebarCollapsible();
    }

    /**
     * Register all Filament render hooks needed for customization.
     *
     * @return void
     */
    protected function registerPanelsRenderHooks(): void
    {
        // Store page load start time for performance monitoring.
        if (!app()->runningInConsole()) {
            app()->singleton('page_start_time', fn () => microtime(true));
        }

        // Inject custom head CSS into the <head> section of the page.
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn () => view('webkernel::components.webkernel.assets.head-css')->render()
        );

        // Register observer for the LanguageTranslation model.
        //  \Webkernel\Models\LanguageTranslation::observe(LanguageTranslationObserver::class);

         FilamentView::registerRenderHook(
             PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
             fn () => view('webkernel::components.webkernel.ui.atoms.currentuserdatetime')
         );

         FilamentView::registerRenderHook(
             PanelsRenderHook::USER_MENU_BEFORE,
             fn () => view('webkernel::components.webkernel.ui.molecules.language-selector')
         );

        // Optional: Hide global search via a custom component.
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
        //     fn () => view('components.search-hide')
        // );

        // Optional: Inject custom footer partial.
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::FOOTER,
        //     fn () => view('components.partials.footer')
        // );

        // Optional: Add user panels after the tenant menu.
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::TENANT_MENU_AFTER,
        //     fn () => view('components.userpanels')
        // );
    }

    /**
     * Make sure the sidebar is collapsible on desktop devices.
     *
     * @return void
     */
    protected function forceSidebarCollapsible(): void
    {
        // Get the current Filament panel.
        $currentPanel = filament()->getCurrentPanel();

        // Apply sidebar settings if the panel exists.
        if ($currentPanel) {
            $this->forceSidebarSettings($currentPanel);
        }
    }

    /**
     * Apply sidebar settings to the given Filament panel.
     *
     * @param \Filament\Panel $currentPanel
     * @return void
     */
    protected function forceSidebarSettings($currentPanel): void
    {
        // Enable collapsible sidebar on desktop.
        $currentPanel->sidebarCollapsibleOnDesktop(true);

        // Enable fully collapsible sidebar on desktop.
        $currentPanel->sidebarFullyCollapsibleOnDesktop(true);
    }
}
