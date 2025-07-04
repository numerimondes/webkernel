<?php

/***********************************************************
 |               Usage in a  Resource
 |**********************************************************
 |  uses packages/webkernel/src/Traits/AutoDiscoverable.php
 |   
 |   class ClientResource extends Resource
 |   {
 |       use AutoDiscoverable;
 |   
 |       public static function put_in_panel(): array
 |       {
 |           return [system_panel_id()];
 |       }
 |       
 |       Or multiple panels:
 |       public static function put_in_panel(): array
 |       {
 |           return ['system', 'admin'];
 |       }
 |   }
 | 
 */

if (!function_exists('system_panel_id')) {
    function system_panel_id(): string
    {
        return 'system';
    }
}

if (!function_exists('put_in_panel')) {
    function put_in_panel($panels): void
    {
        $targetPanels = is_array($panels) ? $panels : [$panels];
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callingClass = $backtrace[1]['class'] ?? null;
        
        if ($callingClass) {
            app()->booted(function () use ($callingClass, $targetPanels) {
                foreach ($targetPanels as $panelId) {
                    if (\Filament\Facades\Filament::hasPanel($panelId)) {
                        \Filament\Facades\Filament::getPanel($panelId)->resource($callingClass);
                    }
                }
            });
        }
    }
}
