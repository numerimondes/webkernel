<?php

namespace Webkernel\Filament\Resources;

use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Illuminate\Support\Facades\Cache;
use Webkernel\Models\PlatformSetting;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Columns\TextInputColumn;
use Webkernel\Filament\Widgets\SettingsStatsWidget;
use Webkernel\Filament\Resources\PlatformSettingResource\Pages;

class PlatformSettingResource extends Resource
{
    protected static ?string $model = PlatformSetting::class;

    // Fixed: Use string instead of BackedEnum for v4 beta compatibility
    protected static ?string $navigationLabel = 'Configuration';
    protected static ?string $pluralModelLabel = 'Platform Configuration';

public static function getNavigationIcon(): string | BackedEnum | Htmlable | null

{

return 'heroicon-o-cog-6-tooth';

}

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                Tabs::make('Settings')
                    ->tabs([
                        // Tab System avec attribut sensitive
                        Tab::make('system')
                            ->label(lang('platform_setting_category_system'))
                            ->icon('heroicon-o-shield-exclamation')
                            ->badge(fn () => PlatformSetting::where('category', 'system')->count())
                            ->badgeColor('danger')
                            ->extraAttributes(['sensitive_sys_settings' => true])
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('system_warning')
                                            ->content('⚠️ ' . lang('platform_setting_system_warning'))
                                            ->extraAttributes(['class' => 'bg-red-50 border border-red-200 rounded-lg p-4 text-red-800']),

                                        ...static::getSettingsForCategory('system')
                                    ])
                                    ->columnSpan('full')
                            ]),

                        Tabs\Tab::make('general')
                            ->label(lang('platform_setting_category_general'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->badge(fn () => PlatformSetting::where('category', 'general')->count())
                            ->badgeColor('success')
                            ->schema([
                                Section::make()
                                    ->schema(static::getSettingsForCategory('general'))
                                    ->columnSpan('full')
                            ]),

                        Tabs\Tab::make('branding')
                            ->label(lang('platform_setting_category_branding'))
                            ->icon('heroicon-o-paint-brush')
                            ->badge(fn () => PlatformSetting::where('category', 'branding')->count())
                            ->badgeColor('warning')
                            ->schema([
                                Section::make()
                                    ->schema(static::getSettingsForCategory('branding'))
                                    ->columnSpan('full')
                            ]),

                        Tabs\Tab::make('theme')
                            ->label(lang('platform_setting_category_theme'))
                            ->icon('heroicon-o-swatch')
                            ->badge(fn () => PlatformSetting::where('category', 'theme')->count())
                            ->badgeColor('info')
                            ->schema([
                                Section::make()
                                    ->schema(static::getSettingsForCategory('theme'))
                                    ->columnSpan('full')
                            ]),

                        Tabs\Tab::make('layout')
                            ->label(lang('platform_setting_category_layout'))
                            ->icon('heroicon-o-squares-2x2')
                            ->badge(fn () => PlatformSetting::where('category', 'layout')->count())
                            ->badgeColor('primary')
                            ->schema([
                                Section::make()
                                    ->schema(static::getSettingsForCategory('layout'))
                                    ->columnSpan('full')
                            ]),

                        Tabs\Tab::make('pwa')
                            ->label(lang('platform_setting_category_pwa'))
                            ->icon('heroicon-o-device-phone-mobile')
                            ->badge(fn () => PlatformSetting::where('category', 'pwa')->count())
                            ->badgeColor('secondary')
                            ->schema([
                                Section::make()
                                    ->schema(static::getSettingsForCategory('pwa'))
                                    ->columnSpan('full')
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->activeTab(request()->get('tab', 'general'))
            ])
            ->aside([
                Section::make('Statistics')
                    ->schema([
                        Forms\Components\Placeholder::make('stats_widget')
                            ->content(fn () => view('webkernel::widgets.settings-stats'))
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Quick Actions')
                    ->schema([
                        Actions::make([
                            Forms\Components\Actions\Action::make('clear_cache')
                                ->label('Clear Cache')
                                ->icon('heroicon-m-arrow-path')
                                ->color('warning')
                                ->action(function () {
                                    Cache::flush();
                                    \Filament\Notifications\Notification::make()
                                        ->title('Cache cleared successfully')
                                        ->success()
                                        ->send();
                                }),

                            Forms\Components\Actions\Action::make('export_settings')
                                ->label('Export Settings')
                                ->icon('heroicon-m-arrow-down-tray')
                                ->color('info')
                                ->action(function () {
                                    // Logic d'export
                                    \Filament\Notifications\Notification::make()
                                        ->title('Settings exported')
                                        ->success()
                                        ->send();
                                }),
                        ])
                    ])
                    ->collapsible(),

                Section::make('Recent Changes')
                    ->schema([
                        Forms\Components\Placeholder::make('recent_changes')
                            ->content(function () {
                                $recentSettings = PlatformSetting::latest('updated_at')->limit(5)->get();
                                $content = '<div class="space-y-2">';
                                foreach ($recentSettings as $setting) {
                                    $content .= '<div class="flex items-center space-x-2 text-sm">';
                                    $content .= '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>';
                                    $content .= '<span class="font-medium">' . lang($setting->name_lang_key) . '</span>';
                                    $content .= '</div>';
                                }
                                $content .= '</div>';
                                return $content;
                            })
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    protected static function getSettingsForCategory(string $category): array
    {
        $settings = PlatformSetting::where('category', $category)->get();
        $components = [];

        foreach ($settings as $setting) {
            $component = static::createFieldComponent($setting);
            if ($component) {
                $components[] = $component;
            }
        }

        return $components;
    }

    protected static function createFieldComponent($setting)
    {
        $baseComponent = match ($setting->type) {
            'string' => TextInput::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->maxLength($setting->metadata['max_length'] ?? 255)
                ->default($setting->getTypedValue())
                ->helperText(lang($setting->description_lang_key ?? ''))
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state) use ($setting) {
                    static::updateSetting($setting, $state);
                }),

            'text' => Textarea::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->rows(3)
                ->default($setting->getTypedValue())
                ->helperText(lang($setting->description_lang_key ?? ''))
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state) use ($setting) {
                    static::updateSetting($setting, $state);
                }),

            'image' => FileUpload::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->image()
                ->directory('platform-settings')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                ->maxSize(($setting->metadata['max_size'] ?? 2) * 1024)
                ->default($setting->getTypedValue())
                ->helperText(function () use ($setting) {
                    $helper = lang($setting->description_lang_key ?? '');
                    if (isset($setting->metadata['max_size'])) {
                        $helper .= " (Max: {$setting->metadata['max_size']}MB)";
                    }
                    return $helper;
                })
                ->afterStateUpdated(function ($state) use ($setting) {
                    static::updateSetting($setting, $state);
                }),

            'number' => Slider::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->range(
                    minValue: $setting->metadata['min'] ?? 0,
                    maxValue: $setting->metadata['max'] ?? 100
                )
                ->default((int) $setting->getTypedValue())
                ->step($setting->metadata['step'] ?? 1)
                ->helperText(function () use ($setting) {
                    $helper = lang($setting->description_lang_key ?? '');
                    if (isset($setting->metadata['min'], $setting->metadata['max'])) {
                        $helper .= " (Min: {$setting->metadata['min']}, Max: {$setting->metadata['max']})";
                    }
                    return $helper;
                })
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state) use ($setting) {
                    static::updateSetting($setting, $state);
                }),

            'boolean' => Toggle::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->default((bool) $setting->getTypedValue())
                ->helperText(lang($setting->description_lang_key ?? ''))
                ->live()
                ->afterStateUpdated(function ($state) use ($setting) {
                    static::updateSetting($setting, $state);
                }),

            'color' => ColorPicker::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->default($setting->getTypedValue())
                ->helperText(lang($setting->description_lang_key ?? ''))
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state) use ($setting) {
                    static::updateSetting($setting, $state);
                }),

            'select' => Select::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->options(function () use ($setting) {
                    if (isset($setting->metadata['options'])) {
                        $options = is_string($setting->metadata['options'])
                            ? json_decode($setting->metadata['options'], true)
                            : $setting->metadata['options'];
                        return is_array($options) ? array_combine($options, $options) : [];
                    }
                    return [];
                })
                ->default($setting->getTypedValue())
                ->helperText(lang($setting->description_lang_key ?? ''))
                ->live()
                ->afterStateUpdated(function ($state) use ($setting) {
                    static::updateSetting($setting, $state);
                }),

            'json' => Textarea::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->rows(6)
                ->default(is_array($setting->getTypedValue()) ? json_encode($setting->getTypedValue(), JSON_PRETTY_PRINT) : $setting->getTypedValue())
                ->helperText(lang($setting->description_lang_key ?? '') . ' (JSON required)')
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state) use ($setting) {
                    $decoded = json_decode($state, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        static::updateSetting($setting, $state);
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Invalid JSON')
                            ->body('Please check your JSON syntax')
                            ->danger()
                            ->send();
                    }
                }),

            default => TextInput::make("setting_{$setting->id}")
                ->label(lang($setting->name_lang_key))
                ->default($setting->getTypedValue())
                ->helperText('Unknown type')
        };

        return $baseComponent
            ->id("setting_{$setting->id}")
            ->extraAttributes([
                'data-setting-id' => $setting->id,
                'data-setting-reference' => $setting->settings_reference,
            ]);
    }

    protected static function updateSetting($setting, $value): void
    {
        try {
            Cache::forget('platform_settings_query');
            $setting->setTypedValue($value);
            $setting->save();

            \Filament\Notifications\Notification::make()
                ->title('Setting updated')
                ->body(lang($setting->name_lang_key) . ' has been updated')
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Update failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label(lang('platform_setting_category'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'system' => 'danger',
                        'general' => 'success',
                        'branding' => 'warning',
                        'theme' => 'info',
                        'layout' => 'primary',
                        'pwa' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => lang("platform_setting_category_{$state}"))
                    ->sortable(),

                TextColumn::make('name_lang_key')
                    ->label(lang('platform_setting_name'))
                    ->formatStateUsing(fn (string $state, $record): string =>
                        '<div class="space-y-1">' .
                        '<div class="font-medium">' . lang($state) . '</div>' .
                        '<div class="text-xs text-gray-500">' . lang($record->description_lang_key ?? '') . '</div>' .
                        '</div>'
                    )
                    ->html()
                    ->searchable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('type')
                    ->label(lang('platform_setting_type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string', 'text' => 'success',
                        'number' => 'info',
                        'boolean' => 'warning',
                        'color' => 'danger',
                        'json' => 'secondary',
                        'image' => 'primary',
                        'select' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('value')
                    ->label(lang('platform_setting_value'))
                    ->formatStateUsing(function ($state, $record) {
                        return match ($record->type) {
                            'boolean' => $record->getTypedValue() ? '✅ True' : '❌ False',
                            'image' => $state ? '<img src="' . asset($state) . '" class="h-8 w-8 object-cover rounded" alt="Image">' : 'No image',
                            'color' => $state ? '<div class="flex items-center gap-2"><div class="w-4 h-4 rounded border" style="background-color: ' . $state . '"></div>' . $state . '</div>' : 'No color',
                            'json' => '<code class="text-xs">' . \Illuminate\Support\Str::limit($state, 30) . '</code>',
                            default => \Illuminate\Support\Str::limit($state, 50) ?: 'Empty'
                        };
                    })
                    ->html()
                    ->tooltip(fn ($record) => $record->value)
                    ->copyable()
                    ->copyMessage('Value copied!'),

                IconColumn::make('is_public')
                    ->label(lang('platform_setting_public'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(lang('platform_setting_category'))
                    ->options([
                        'system' => lang('platform_setting_category_system'),
                        'general' => lang('platform_setting_category_general'),
                        'branding' => lang('platform_setting_category_branding'),
                        'theme' => lang('platform_setting_category_theme'),
                        'layout' => lang('platform_setting_category_layout'),
                        'pwa' => lang('platform_setting_category_pwa'),
                    ])
                    ->searchable(),

                SelectFilter::make('type')
                    ->label(lang('platform_setting_type'))
                    ->options([
                        'string' => 'String',
                        'text' => 'Text',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'color' => 'Color',
                        'json' => 'JSON',
                        'image' => 'Image',
                        'select' => 'Select',
                    ])
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label(lang('platform_setting_public'))
                    ->boolean()
                    ->trueLabel('Public only')
                    ->falseLabel('Private only')
                    ->native(false),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->persistFiltersInSession()
            ->groups([
                Tables\Grouping\Group::make('category')
                    ->label(lang('platform_setting_category'))
                    ->collapsible(),
            ])
            ->recordUrl(function ($record) {
                return static::getUrl('edit', [
                    'record' => $record->id,
                    'tab' => $record->category
                ]);
            })
            ->recordAction(null) // Disable default action
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('clear_cache')
                        ->label('Clear Cache')
                        ->icon('heroicon-m-arrow-path')
                        ->color('warning')
                        ->action(function () {
                            Cache::flush();
                            \Filament\Notifications\Notification::make()
                                ->title('Cache cleared successfully')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('category')
            ->striped()
            ->paginated([100, 200, 400, 'all'])
            ->defaultPaginationPageOption(100);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformSettings::route('/'),
            'edit' => Pages\EditPlatformSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('platform_settings_count', 300, fn () => static::getModel()::count());
    }
}
