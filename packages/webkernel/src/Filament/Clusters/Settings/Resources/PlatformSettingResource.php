<?php

namespace Webkernel\Filament\Clusters\Settings\Resources;

use Webkernel\Models\PlatformSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Webkernel\Filament\Clusters\Settings\Resources\PlatformSettingResource\Pages;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\SelectFilter;
use Webkernel\Filament\Clusters\Settings;
use Illuminate\Support\Facades\Auth;

class PlatformSettingResource extends Resource
{
    protected static ?string $model = PlatformSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $cluster = Settings::class;

    public static function getNavigationLabel(): string
    {
        return __('settings.platform_settings');
    }

    public static function getModelLabel(): string
    {
        return __('settings.platform_setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('settings.platform_settings');
    }

    public static function form(Form $form): Form
    {
        return $form->schema(function () use ($form) {
            $record = $form->getRecord();
            if (!$record instanceof PlatformSetting) {
                return [];
            }

            return [
                Section::make(__($record->name_lang_key))
                    ->description(__($record->description_lang_key))
                    ->schema([
                        self::createFieldForSetting($record),
                    ])
                    ->columns(1),
            ];
        });
    }

    protected static function createFieldForSetting(PlatformSetting $setting)
    {
        $baseField = match ($setting->type) {
            'boolean' => Forms\Components\Toggle::make($setting->settings_reference)
                ->default($setting->getTypedValue())
                ->live(onBlur: true),

            'color' => Forms\Components\ColorPicker::make($setting->settings_reference)
                ->default($setting->value)
                ->live(onBlur: true),

            'number' => Forms\Components\TextInput::make($setting->settings_reference)
                ->numeric()
                ->default($setting->value)
                ->live(onBlur: true)
                ->minValue($setting->metadata['min'] ?? null)
                ->maxValue($setting->metadata['max'] ?? null)
                ->suffix($setting->metadata['unit'] ?? ''),

            'select' => Forms\Components\Select::make($setting->settings_reference)
                ->options(array_combine(
                    $setting->metadata['options'] ?? [],
                    array_map(fn($option) => __("settings.option_{$option}"), $setting->metadata['options'] ?? [])
                ))
                ->default($setting->value)
                ->live(onBlur: true),

            'text' => Forms\Components\Textarea::make($setting->settings_reference)
                ->default($setting->value)
                ->rows(3)
                ->live(onBlur: true),

            'image' => Forms\Components\FileUpload::make($setting->settings_reference)
                ->image()
                ->default($setting->value)
                ->live(onBlur: true),

            'json' => Forms\Components\Textarea::make($setting->settings_reference)
                ->default(json_encode($setting->getTypedValue()))
                ->rows(5)
                ->live(onBlur: true)
                ->formatStateUsing(fn ($state) => json_encode(json_decode($state, true), JSON_PRETTY_PRINT))
                ->reactive(),

            default => Forms\Components\TextInput::make($setting->settings_reference)
                ->default($setting->value)
                ->live(onBlur: true),
        };

        return $baseField
            ->label(__($setting->name_lang_key))
            ->helperText(__($setting->description_lang_key))
            ->rules($setting->validation_rules ?? [])
            ->afterStateUpdated(function ($state, $component) use ($setting) {
                // Update setting in real-time
                $tenantId = Auth::user()?->tenant_id ?? 1;
                $result = PlatformSetting::set($setting->settings_reference, $state, $tenantId);

                // Show notification
                Notification::make()
                    ->title(__('settings.updated_successfully'))
                    ->success()
                    ->send();

                // Dispatch browser event for live preview
                $component->dispatch('setting-updated', [
                    'reference' => $setting->settings_reference,
                    'value' => $state,
                    'type' => $setting->type,
                    'tenant_id' => $tenantId,
                ]);

                // Trigger reload for settings requiring cache clear
                if ($setting->requires_cache_clear) {
                    $component->dispatch('triggerSmoothReload');
                }
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('settings_reference')
                    ->label(__('settings.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_lang_key')
                    ->label(__('settings.name'))
                    ->formatStateUsing(fn ($state) => __($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label(__('settings.value'))
                    ->formatStateUsing(function ($state, PlatformSetting $record) {
                        return match ($record->type) {
                            'boolean' => $record->getTypedValue() ? __('settings.yes') : __('settings.no'),
                            'json' => json_encode($record->getTypedValue(), JSON_PRETTY_PRINT),
                            default => $state,
                        };
                    })
                    ->searchable(),
                TextColumn::make('category')
                    ->label(__('settings.category'))
                    ->formatStateUsing(fn ($state) => __("settings.category_{$state}"))
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('settings.type'))
                    ->sortable(),
                BooleanColumn::make('is_public')
                    ->label(__('settings.is_public'))
                    ->sortable(),
                BooleanColumn::make('requires_cache_clear')
                    ->label(__('settings.requires_cache_clear'))
                    ->sortable(),
                TextColumn::make('tenant_id')
                    ->label(__('settings.tenant_id'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label(__('settings.category'))
                    ->options([
                        'general' => __('settings.category_general'),
                        'branding' => __('settings.category_branding'),
                        'theme' => __('settings.category_theme'),
                        'layout' => __('settings.category_layout'),
                        'pwa' => __('settings.category_pwa'),
                        'system' => __('settings.category_system'),
                    ]),
                SelectFilter::make('is_public')
                    ->label(__('settings.is_public'))
                    ->options([
                        '1' => __('settings.yes'),
                        '0' => __('settings.no'),
                    ]),
                SelectFilter::make('tenant_id')
                    ->label(__('settings.tenant_id'))
                    ->options(function () {
                        return PlatformSetting::distinct()
                            ->pluck('tenant_id')
                            ->mapWithKeys(fn ($tenantId) => [$tenantId => $tenantId])
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn ($record) => __('settings.edit_setting', ['name' => __($record->name_lang_key)]))
                    ->modalWidth('lg')
                    ->form(function (Form $form) {
                        $record = $form->getRecord();
                        if (!$record instanceof PlatformSetting) {
                            return [];
                        }
                        return [
                            Section::make(__($record->name_lang_key))
                                ->description(__($record->description_lang_key))
                                ->schema([
                                    self::createFieldForSetting($record),
                                ])
                                ->columns(1),
                        ];
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('settings.updated_successfully'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('settings.deleted_successfully'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('settings.bulk_deleted_successfully'))
                    ),
            ])
            ->defaultSort('category')
            ->query(function () {
                $query = PlatformSetting::query();
                if ($tenantId = Auth::user()?->tenant_id) {
                    $query->where('tenant_id', $tenantId);
                }
                return $query;
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlatformSettings::route('/'),
        ];
    }
}
