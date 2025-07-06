<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Webkernel\Core\Providers\Filament\SystemPanelProvider::class,
    Webkernel\Core\Providers\WebKernelConfigServiceProvider::class,
    Webkernel\Core\Providers\WebkernelBladeServiceProvider::class,
    Webkernel\Core\Providers\WebkernelCommandServiceProvider::class,
    Webkernel\Core\Providers\WebkernelMigrationServiceProvider::class,
    Webkernel\Core\Providers\WebkernelRenderHooksServiceProvider::class,
    Webkernel\Core\Providers\WebkernelRouteServiceProvider::class,
    Webkernel\Core\Providers\WebkernelServiceProvider::class,
];
