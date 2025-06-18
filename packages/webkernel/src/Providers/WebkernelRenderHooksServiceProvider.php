<?php
namespace Webkernel\Providers;

use DB;
use Exception;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema;
use Webkernel\Models\RenderHookSetting;

class WebkernelRenderHooksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPanelsRenderHooks();
        $this->forceSidebarCollapsible();
    }

    protected function isRenderHookEnabled(string $key): bool
    {
        try {
            DB::connection()->getPdo();
            if (!Schema::hasTable('render_hook_settings')) {
                return true;
            }
            return (bool) optional(RenderHookSetting::where('hook_key', $key)->first())->enabled ?? true;
        } catch (Exception $e) {
            return true;
        }
    }

    protected function forceSidebarCollapsible(): void
    {
        try {
            $currentPanel = Filament::getCurrentPanel();
            if ($currentPanel) {
                $this->forceSidebarSettings($currentPanel);
            }
        } catch (Exception $e) {
            // Si aucun panel n'est disponible, on ignore silencieusement
            // Cela peut arriver pendant les commandes artisan ou les migrations
        }
    }

    protected function forceSidebarSettings($currentPanel): void
    {
        $currentPanel->sidebarCollapsibleOnDesktop(true);
        $currentPanel->sidebarFullyCollapsibleOnDesktop(true);
    }

    protected function registerPanelsRenderHooks(): void
    {
        if (!app()->runningInConsole()) {
            app()->singleton('page_start_time', fn() => microtime(true));
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn() => safe_render_hook_view('webkernel::components.webkernel.assets.head-css')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn() => safe_render_hook_view('webkernel::components.webkernel.assets.languages-fonts')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn() => safe_render_hook_view('webkernel::components.webkernel.ui.molecules.badge-and-universal-help')
        );

        if ($this->isRenderHookEnabled('current_user_datetime')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn() => safe_render_hook_view('webkernel::components.webkernel.ui.atoms.currentuserdatetime')
            );
        }

        if ($this->isRenderHookEnabled('language_selector')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn() => safe_render_hook_view('webkernel::components.webkernel.ui.molecules.language-selector')
            );
        }

        if ($this->isRenderHookEnabled('search_hide')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn() => safe_render_hook_view('webkernel::components.webkernel.ui.atoms.search-hide')
            );
        }

        /*
         if ($this->isRenderHookEnabled('footer_partial')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::FOOTER,
                fn () => customizable_render_hook_view('webkernel::components.webkernel.ui.atoms.footer')
            );
        }

        if ($this->isRenderHookEnabled('tenant_menu_after')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::TENANT_MENU_AFTER,
                fn () => customizable_render_hook_view('components.userpanels')
            );
        }
        */

        switch (true) {
            case true:
                FilamentView::registerRenderHook(
                    PanelsRenderHook::BODY_END,
                    fn() => <<<HTML
                        <div style="
                            position: fixed;
                            bottom: 5px;
                            right: 10px;
                            z-index: 39;
                            background-color: #2563eb;
                            color: white;
                            padding: 12px 16px;
                            border-radius: 9999px;
                            font-size: 14px;
                            font-family: sans-serif;
                            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                            cursor: pointer;
                        ">
                            💬 Chat
                        </div>
                    HTML
                );
                break;
            default:
                break;
        }
    }
}
