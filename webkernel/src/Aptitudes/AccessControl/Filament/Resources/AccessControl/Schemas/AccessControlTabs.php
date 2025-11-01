<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas;

use Filament\Schemas\Components\Tabs;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Tabs\{
  CustomPrivileges,
  Pages,
  Panels,
  Resources,
  Widgets,
  Wildcards,
};

/**
 * Permission Matrix Manager
 *
 * Handles all 6 tabs: Discovered Privileges (Resources, Panels, Pages, Widgets, Custom), and Wildcard Patterns
 *
 * @package Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas
 */
class AccessControlTabs
{
  /**
   * Constructs the permissions tabs with comprehensive permission management
   *
   * @return Tabs The complete permission matrix tabs
   */
  public static function getTabs(): Tabs
  {
    return Tabs::make('Permissions')
      ->tabs([
        Resources::getTab(),
        Panels::getTab(),
        Pages::getTab(),
        Widgets::getTab(),
        CustomPrivileges::getTab(),
        Wildcards::getTab(),
      ])
      ->contained(false)
      ->columnSpanFull();
  }
}
