<?php
// app/Traits/AutoDiscoverable.php
namespace Webkernel\Core\Traits;
use Filament\Facades\Filament;

trait AutoDiscoverable
{
    /**
     * @return void
     */
    public static function bootAutoDiscoverable(): void
    {
        static::registerForAutoDiscovery();
    }
    /**
     * @return void
     */

    protected static function registerForAutoDiscovery(): void
    {
        if (!function_exists("put_in_panel")) {
            return;
        }

        $targetPanels = put_in_panel(static::class);

        if (!empty($targetPanels)) {
            app()->booted(function () use ($targetPanels) {
                foreach ($targetPanels as $panelId) {
                    $panel = Filament::getPanel($panelId);

                    if (!$panel) {
                        continue;
                    }

                    $panel->resources[] = static::class;
                }
            });
        }
    }
}
