<?php

namespace Webkernel\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Schema;
use Webkernel\Models\RenderHookSetting;

class WebkernelRenderHooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // You may bind services or perform registrations here in the future.
    }

    public function boot(): void
    {
        $this->registerPanelsRenderHooks();
        $this->forceSidebarCollapsible();
    }

    protected function registerPanelsRenderHooks(): void
    {
        if (!app()->runningInConsole()) {
            app()->singleton('page_start_time', fn () => microtime(true));
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn () => customizable_render_hook_view('webkernel::components.webkernel.assets.head-css')->render()
        );

        if ($this->isRenderHookEnabled('current_user_datetime')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn () => customizable_render_hook_view('webkernel::components.webkernel.ui.atoms.currentuserdatetime')
            );
        }

        if ($this->isRenderHookEnabled('language_selector')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn () => customizable_render_hook_view('webkernel::components.webkernel.ui.molecules.language-selector')
            );
        }

        if ($this->isRenderHookEnabled('search_hide')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn () => customizable_render_hook_view('webkernel::components.webkernel.ui.atoms.search-hide')
            );
        }

        // Optional hooks (commentés volontairement)
        /*
        if ($this->isRenderHookEnabled('footer_partial')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::FOOTER,
                fn () => customizable_render_hook_view('components.partials.footer')
            );
        }

        if ($this->isRenderHookEnabled('tenant_menu_after')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::TENANT_MENU_AFTER,
                fn () => customizable_render_hook_view('components.userpanels')
            );
        }
        */
    }

    protected function isRenderHookEnabled(string $key): bool
    {
        try {
            // Vérification de la connexion DB avant toute opération
            \DB::connection()->getPdo();

            if (!Schema::hasTable('render_hook_settings')) {
                return true; // Fallback si la table n'existe pas
            }

            return (bool) optional(RenderHookSetting::where('hook_key', $key)->first())->enabled ?? true;

        } catch (\Exception $e) {
            return true; // Fallback si la DB n'est pas accessible
        }
    }

    protected function forceSidebarCollapsible(): void
    {
        $currentPanel = filament()->getCurrentPanel();

        if ($currentPanel) {
            $this->forceSidebarSettings($currentPanel);
        }
    }

    protected function forceSidebarSettings($currentPanel): void
    {
        $currentPanel->sidebarCollapsibleOnDesktop(true);
        $currentPanel->sidebarFullyCollapsibleOnDesktop(true);
    }
}
