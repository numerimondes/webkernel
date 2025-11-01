<?php

namespace Webkernel\Aptitudes\Base\Providers;

use Filament\Navigation\NavigationItem;

class ComingNavigation
{
  /**
   * @return NavigationItem[]
   */
  public static function navigationItems(): array
  {
    $systemItems = [
      ['url' => 'after', 'label' => 'Activity Logs', 'icon' => 'heroicon-o-clipboard-document-list'],
      ['url' => 'after', 'label' => 'Backup', 'icon' => 'heroicon-o-circle-stack'],
      ['url' => 'after', 'label' => 'Cronjob', 'icon' => 'heroicon-o-clock'],
      ['url' => 'after', 'label' => 'Cache Management', 'icon' => 'heroicon-o-bolt'],
      ['url' => 'after', 'label' => 'Cleanup System', 'icon' => 'heroicon-o-trash'],
      ['url' => 'after', 'label' => 'Licence Management', 'icon' => 'heroicon-o-information-circle'],
      ['url' => 'after', 'label' => 'System Information', 'icon' => 'heroicon-o-information-circle'],
    ];

    $navigationItems = [];

    // D'abord les autres groupes (si tu en as)
    // $navigationItems[] = NavigationItem::make(...)->group('Other Group');

    // Ensuite le groupe "System Management"
    foreach ($systemItems as $item) {
      $navigationItems[] = NavigationItem::make($item['label'])
        ->icon($item['icon'])
        ->url($item['url'])
        ->group('System Management');
    }

    return $navigationItems;
  }
}
