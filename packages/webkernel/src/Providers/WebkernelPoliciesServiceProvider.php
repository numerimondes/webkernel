<?php

namespace Webkernel\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WebkernelPoliciesServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $policyPath = base_path('packages/webkernel/src/Policies');
        $files = File::allFiles($policyPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $policyClass = 'Webkernel\\Policies\\' . Str::studly($file->getBasename('.php'));
                $modelClass = 'Webkernel\\Models\\' . Str::studly($file->getBasename('.php'));

                if (class_exists($modelClass)) {
                    Gate::policy($modelClass, $policyClass);
                }
            }
        }
    }
}
