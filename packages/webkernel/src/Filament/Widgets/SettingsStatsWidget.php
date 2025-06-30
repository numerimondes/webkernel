<?php

namespace Webkernel\Filament\Widgets;

use Webkernel\Models\PlatformSetting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class SettingsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSettings = PlatformSetting::count();
        $publicSettings = PlatformSetting::where('is_public', true)->count();
        $categoriesCount = PlatformSetting::distinct('category')->count();
        $lastModified = PlatformSetting::latest('updated_at')->first()?->updated_at;

        return [
            Stat::make('Total des paramètres', $totalSettings)
                ->description('Nombre total de paramètres')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('primary'),

            Stat::make('Paramètres publics', $publicSettings)
                ->description('Accessibles publiquement')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Catégories', $categoriesCount)
                ->description('Nombre de catégories')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Dernière modification', $lastModified ? $lastModified->diffForHumans() : 'Jamais')
                ->description('Dernière mise à jour')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
