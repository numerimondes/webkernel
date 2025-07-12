<?php

namespace Webkernel\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Webkernel\Core\Http\Middleware\CheckUserAccess;
use Filament\Facades\Filament;
use Illuminate\Contracts\Http\Kernel;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Après le boot de l'app, injecte le middleware dans tous les panels
        $this->app->booted(function () {
            $this->injectMiddlewareInAllPanels();
            $this->injectMiddlewareInWebGroup();
            $this->injectMiddlewareGlobally();
        });

        // Si Filament est résolu plus tard (cas rare), réinjecte
        $this->app->resolving('filament', function () {
            $this->injectMiddlewareInAllPanels();
        });
    }

    protected function injectMiddlewareInAllPanels(): void
    {
        try {
            foreach (Filament::getPanels() as $panel) {
                $current = $panel->getMiddleware();
                if (!in_array(CheckUserAccess::class, $current)) {
                    $panel->middleware(array_merge($current, [CheckUserAccess::class]));
                    \Log::info('[Webkernel] CheckUserAccess injected in panel: ' . $panel->getId());
                } else {
                    \Log::info('[Webkernel] CheckUserAccess already present in panel: ' . $panel->getId());
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('[Webkernel] MiddlewareServiceProvider: Panel injection failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function injectMiddlewareInWebGroup(): void
    {
        try {
            $kernel = $this->app->make(Kernel::class);
            $webMiddleware = $kernel->getMiddlewareGroups()['web'] ?? [];
            
            if (!in_array(CheckUserAccess::class, $webMiddleware)) {
                $kernel->appendMiddlewareToGroup('web', CheckUserAccess::class);
                \Log::info('[Webkernel] CheckUserAccess injected in web middleware group');
            } else {
                \Log::info('[Webkernel] CheckUserAccess already present in web middleware group');
            }
        } catch (\Throwable $e) {
            \Log::warning('[Webkernel] MiddlewareServiceProvider: Web group injection failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function injectMiddlewareGlobally(): void
    {
        try {
            $kernel = $this->app->make(Kernel::class);
            $globalMiddleware = $kernel->getMiddleware();
            
            if (!in_array(CheckUserAccess::class, $globalMiddleware)) {
                $kernel->pushMiddleware(CheckUserAccess::class);
                \Log::info('[Webkernel] CheckUserAccess injected globally');
            } else {
                \Log::info('[Webkernel] CheckUserAccess already present globally');
            }
        } catch (\Throwable $e) {
            \Log::warning('[Webkernel] MiddlewareServiceProvider: Global injection failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}