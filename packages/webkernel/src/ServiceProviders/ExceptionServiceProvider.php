<?php

namespace Webkernel\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class ExceptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Webkernel\Core\Exceptions\Handler::class
        );
    }
} 