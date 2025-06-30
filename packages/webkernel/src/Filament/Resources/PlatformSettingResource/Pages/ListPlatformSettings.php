<?php

namespace Webkernel\Filament\Resources\PlatformSettingResource\Pages;

use Webkernel\Filament\Resources\PlatformSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Filament\Notifications\Notification;

class ListPlatformSettings extends ListRecords
{
    protected static string $resource = PlatformSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clear_all_cache')
                ->label('Vider tout le cache')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Vider le cache de la plateforme')
                ->modalDescription('Cette action va vider tout le cache de la plateforme. Êtes-vous sûr ?')
                ->action(function () {
                    \Illuminate\Support\Facades\Cache::flush();

                    Notification::make()
                        ->title('Cache vidé')
                        ->body('Le cache de la plateforme a été vidé avec succès.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('export_settings')
                ->label('Exporter')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $settings = \Webkernel\Models\PlatformSetting::all()->toArray();

                    $filename = 'platform_settings_' . date('Y-m-d_H-i-s') . '.json';

                    return response()->streamDownload(function () use ($settings) {
                        echo json_encode($settings, JSON_PRETTY_PRINT);
                    }, $filename, [
                        'Content-Type' => 'application/json',
                    ]);
                }),

            Actions\CreateAction::make()
                ->label('Nouveau paramètre')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Paramètres de la plateforme';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PlatformSettingResource\Widgets\SettingsStatsWidget::class,
        ];
    }
}
