<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Webkernel\Aptitudes\AccessControl\Models\Permission;
use Webkernel\Aptitudes\AccessControl\Models\PermissionGroup;

class Stats extends StatsOverviewWidget
{
  protected function getStats(): array
  {
    $totalGroups = PermissionGroup::count();
    $totalPermissions = Permission::count();
    $totalUsersWithGroups = DB::table('users_priv_permission_group_user')->distinct('user_id')->count('user_id');
    $superadminCount = PermissionGroup::where('slug', 'superadmin')->first()?->users()->count() ?? 0;

    return [
      Stat::make('Permission Groups', $totalGroups)
        ->description('Total permission groups')
        ->icon('heroicon-o-user-group')
        ->color('success'),

      Stat::make('Permissions', $totalPermissions)
        ->description('Total system permissions')
        ->icon('heroicon-o-key')
        ->color('info'),

      Stat::make('Users with Access', $totalUsersWithGroups)
        ->description('Users with permission groups')
        ->icon('heroicon-o-users')
        ->color('warning'),

      Stat::make('Super Admins', $superadminCount)
        ->description('Users with full access')
        ->icon('heroicon-o-shield-check')
        ->color('danger'),
    ];
  }
}
