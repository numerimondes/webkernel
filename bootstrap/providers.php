<?php

return [
    App\Providers\AppServiceProvider::class,

    Webkernel\Providers\WebkernelServiceProvider::class,

    Webkernel\Providers\Filament\SystemPanelProvider::class,

    Webkernel\Providers\WebkernelRenderHooksServiceProvider::class,

    Webkernel\Providers\WebkernelBladeServiceProvider::class,

    Webkernel\Providers\WebkernelCommandServiceProvider::class,

    Webkernel\Providers\WebkernelHelperServiceProvider::class,

    Webkernel\Providers\WebkernelMigrationServiceProvider::class,

    Webkernel\Providers\WebkernelRouteServiceProvider::class,

    Webkernel\Providers\WebkernelViewServiceProvider::class,

    Webkernel\Providers\WebkernelWebhookServiceProvider::class,
];
