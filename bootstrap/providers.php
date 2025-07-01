<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Webkernel\Providers\Filament\SystemPanelProvider::class,
    Webkernel\Providers\WebKernelConfigServiceProvider::class,
    Webkernel\Providers\WebkernelBladeServiceProvider::class,
    Webkernel\Providers\WebkernelCommandServiceProvider::class,
    Webkernel\Providers\WebkernelMigrationServiceProvider::class,
    Webkernel\Providers\WebkernelRenderHooksServiceProvider::class,
    Webkernel\Providers\WebkernelRouteServiceProvider::class,
    Webkernel\Providers\WebkernelServiceProvider::class,
];
