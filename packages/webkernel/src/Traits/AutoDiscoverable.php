<?php
// app/Traits/AutoDiscoverable.php
namespace Webkernel\Traits;

trait AutoDiscoverable
{
    public static function bootAutoDiscoverable()
    {
        static::registerForAutoDiscovery();
    }

    protected static function registerForAutoDiscovery()
    {
        if (method_exists(static::class, 'put_in_panel')) {
            $targetPanels = static::put_in_panel();
            
            if (!empty($targetPanels)) {
                app()->booted(function () use ($targetPanels) {
                    foreach ($targetPanels as $panelId) {
                        if (\Filament\Facades\Filament::hasPanel($panelId)) {
                            \Filament\Facades\Filament::getPanel($panelId)->resource(static::class);
                        }
                    }
                });
            }
        }
    }
}