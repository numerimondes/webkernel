<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Filament\Theming;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference;

class ThemeTab
{
    public static function get(): array
    {
        $user = auth()->user();
        $preferences = UserPreference::getOrCreateForUser($user);
        $themeData = UserPreference::getThemeData();

        // Formater les options avec les noms propres
        $formattedOptions = [];
        foreach ($themeData['options'] as $key => $value) {
            $formattedOptions[$key] = UserPreference::formatThemeName($value);
        }

        return [
            Select::make('theme')
                ->label('Theme')
                ->options($formattedOptions)
                ->default($themeData['current'])
                ->afterStateHydrated(function ($component, $state) use ($themeData) {
                    if (empty($state)) {
                        $component->state($themeData['current']);
                    }
                })
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state) {
                    if ($state !== null) {
                        UserPreference::saveTheme($state);
                        Notification::make()
                            ->title('Theme updated')
                            ->body('The theme has been changed successfully. Reload the page to see the changes.')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
