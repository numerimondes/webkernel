<?php

namespace Webkernel\Core\Filament\Resources;

use Exception;
use Throwable;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Webkernel\Core\Models\RenderHookSetting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Webkernel\Core\Filament\Clusters\Settings;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\Component as FormComponent;

class RenderHookSettingResource extends Resource
{
    protected static ?string $model = RenderHookSetting::class;

   // public static function getNavigationGroup(): ?string
   // {
   //     return lang('system_menu_core_settings');
   // }
   // public static function getNavigationParentItem(): ?string
   // {
   //     return 'Settings';
   // }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-ellipsis-horizontal-circle';
    }

   // protected static ?string $cluster = Settings::class;

    public static function getNavigationLabel(): string
    {
        return lang('Custom Navigation Label');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

      public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('hook_key')
                    ->required()
                    ->maxLength(255),
                TextInput::make('where_placed')
                    ->required()
                    ->maxLength(255),
                TextInput::make('view_path')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('icon')
                    ->label('')
                    ->width('1%')
                    ->icon(fn($record) => $record->icon)
                    ->size('md'),
                TextColumn::make('hook_key')
                    ->label(lang('action_to_perform'))
                    ->formatStateUsing(function ($state, $record) {
                        $title = lang($record->hook_key);
                        $desc = lang($record->translation_desc_key);
                        return "{$title}<br>{$desc}";
                    })
                    ->html()
                    ->wrap()
                    ->color(fn($record) => self::originalViewExists($record) ? null : 'gray'),
               // SelectColumn::make('where_placed')
               //     ->label(lang('toggle_visibility'))
               //     ->options([
               //         'draft' => 'Draft',
               //         'reviewing' => 'Reviewing',
               //         'published' => 'Published',
               //     ]),
                ToggleColumn::make('enabled')
                    ->label(lang('toggle_visibility'))
                    ->disabled(fn($record) => !self::originalViewExists($record))
                    ->afterStateUpdated(function ($livewire) {
                        $livewire->js('window.location.reload()');
                    })
            ])
            ->filters([])
            ->actions([
               // self::getCustomizeViewAction(),
                //self::getEditCustomViewAction(),
                //self::getDeleteCustomViewAction(),
                EditAction::make()
                    ->iconButton()
                    ->label(lang('edit'))
                    ->modalWidth('lg')
                    ->hidden(fn($record) => self::originalViewExists($record)),
            ])
            ->bulkActions([]);
    }

    protected static function getCustomizeViewAction(): Action
    {
        return Action::make('customize_view')
            ->label(lang('Customize This View'))
            ->icon('heroicon-o-pencil-square')
            ->color('primary')
            ->visible(function ($record) {
                $visible = self::originalViewExists($record) && !File::exists(self::getFullViewPath($record));
                Log::info('Customize view action visibility for hook_key: ' . ($record->hook_key ?? 'null') . ' - Visible: ' . ($visible ? 'Yes' : 'No'));
                return $visible;
            })
            ->requiresConfirmation()
            ->modalHeading(lang('Customize View'))
            ->modalDescription(lang('This will create a customized copy of the original view file.'))
            ->modalSubmitActionLabel(lang('Customize'))
            ->modalCancelActionLabel(lang('Cancel'))
            ->modalWidth('lg')
            ->action(function ($record, $livewire) {
                Log::info('Customize view action triggered for hook_key: ' . ($record->hook_key ?? 'null'));
                try {
                    $viewPath = self::getViewPathFromHookKey($record->hook_key);
                    if (empty($viewPath)) {
                        Log::error('No view path found for hook_key: ' . ($record->hook_key ?? 'null'));
                        Notification::make()
                            ->title(lang('Action Failed'))
                            ->body(lang('No view path defined for this hook.'))
                            ->danger()
                            ->send();
                        return;
                    }
                    Log::info('View path retrieved: ' . $viewPath);

                    $copied = self::copyViewToCustomPath($viewPath);
                    Log::info('Copy operation result: ' . ($copied ? 'Success' : 'Failed'));

                    Notification::make()
                        ->title($copied ? lang('View Copied Successfully') : lang('View Copy Failed'))
                        ->body($copied ? lang('The view has been copied to your custom path.') : lang('Could not copy the view. Check logs for details.'))
                        ->status($copied ? 'success' : 'danger')
                        ->send();

                    if ($copied) {
                        $livewire->dispatch('refresh');
                    }
                } catch (Exception $e) {
                    Log::error('Customize view action failed: ' . $e->getMessage(), ['exception' => $e]);
                    Notification::make()
                        ->title(lang('Action Failed'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    protected static function getEditCustomViewAction(): Action
    {
        return Action::make('edit_custom_view')
            ->label(lang('Edit View'))
            ->icon('heroicon-o-code-bracket')
            ->color('secondary')
            ->visible(fn($record) => self::originalViewExists($record) && File::exists(self::getFullViewPath($record)))
            ->form([
                Textarea::make('view_contents')
                    ->label(lang('Blade Content'))
                    ->default(function ($record) {
                        $fullPath = self::getFullViewPath($record);
                        return File::exists($fullPath) ? File::get($fullPath) : '';
                    })
                    ->rows(20)
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set, FormComponent $component) {
                        $isValid = self::validateBladeSyntax($state);
                        $message = $isValid ? lang('Valid Syntax') : lang('Syntax Error');
                        $color = $isValid ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)';
                        $component->hint(new HtmlString("<span style='color: {$color}; font-weight: 500;'>{$message}</span>"));
                    })
                    ->helperText(lang('Note: Ensure that the Blade syntax is correct before saving.')),
            ])
            ->action(function (array $data, $record, $livewire) {
                Log::info('Attempting to save custom view for hook_key: ' . ($record->hook_key ?? 'null'));
                if (!self::validateBladeSyntax($data['view_contents'])) {
                    Notification::make()
                        ->title(lang('Syntax Error'))
                        -> mullisaniwanathis->body(lang('Invalid Blade/PHP syntax detected. Changes not saved.'))
                        ->danger()
                        ->send();
                    return;
                }
                $path = self::getFullViewPath($record);
                $backupPath = $path . '.bak';
                try {
                    if (!is_writable(dirname($path))) {
                        throw new Exception('Directory is not writable: ' . dirname($path));
                    }
                    if (File::exists($path)) {
                        File::put($backupPath, File::get($path));
                    }
                    File::put($path, $data['view_contents']);
                    Notification::make()
                        ->title(lang('View Saved Successfully'))
                        ->success()
                        ->send();
                    $livewire->dispatch('refresh');
                } catch (Exception $e) {
                    Log::error('View save failed: ' . $e->getMessage(), ['exception' => $e]);
                    if (File::exists($backupPath)) {
                        File::put($path, File::get($backupPath));
                        File::delete($backupPath);
                    } else {
                        self::revertToOriginalView(self::getViewPathFromHookKey($record->hook_key));
                    }
                    Notification::make()
                        ->title(lang('Save Failed'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    protected static function getDeleteCustomViewAction(): Action
    {
        return Action::make('delete_custom_view')
            ->label(lang('Delete View'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->visible(fn($record) => self::originalViewExists($record) && File::exists(self::getFullViewPath($record)))
            ->requiresConfirmation()
            ->modalHeading(lang('Delete Custom View'))
            ->modalIcon('heroicon-o-trash')
            ->modalDescription(function ($record) {
                $customPath = self::getFullViewPath($record);
                $originalPath = self::getOriginalViewPath($record->hook_key);
                if (File::exists($originalPath) && File::get($customPath) === File::get($originalPath)) {
                    return lang('This custom view matches the original. Remove it?');
                }
                return lang('This will delete your customized view and restore default behavior.');
            })
            ->modalSubmitActionLabel(lang('Confirm Deletion'))
            ->modalCancelActionLabel(lang('Cancel'))
            ->modalWidth('lg')
            ->action(function ($record, $livewire) {
                Log::info('Attempting to delete custom view for hook_key: ' . ($record->hook_key ?? 'null'));
                try {
                    File::delete(self::getFullViewPath($record));
                    Notification::make()
                        ->title(lang('View Deleted Successfully'))
                        ->success()
                        ->send();
                    $livewire->dispatch('refresh');
                } catch (Exception $e) {
                    Log::error('View deletion failed: ' . $e->getMessage(), ['exception' => $e]);
                    Notification::make()
                        ->title(lang('Deletion Failed'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public static function getViewPathFromHookKey(string $key): string
    {
        $hookSetting = RenderHookSetting::where('hook_key', $key)->first();
        $viewPath = $hookSetting?->view_path ?? '';
        Log::info('View path for hook_key ' . $key . ': ' . $viewPath);
        return $viewPath;
    }

    private static function getFullViewPath($record): string
    {
        $viewPath = self::getViewPathFromHookKey($record->hook_key);
        if (empty($viewPath)) {
            Log::warning('Empty view path for hook_key: ' . ($record->hook_key ?? 'null'));
            return '';
        }
        $path = resource_path('views/' . str_replace('.', '/', $viewPath) . '.blade.php');
        Log::info('Resolved full view path: ' . $path);
        return $path;
    }

    private static function getOriginalViewPath(string $hookKey): string
    {
        $viewPath = self::getViewPathFromHookKey($hookKey);
        if (empty($viewPath)) {
            Log::warning('Empty original view path for hook_key: ' . $hookKey);
            return '';
        }
        $path = base_path('packages/webkernel/src/Core/Resources/Views/' . str_replace('.', '/', $viewPath) . '.blade.php');
        Log::info('Resolved original view path: ' . $path);
        return $path;
    }

    public static function originalViewExists($record): bool
    {
        $originalPath = self::getOriginalViewPath($record->hook_key);
        $exists = !empty($originalPath) && File::exists($originalPath);
        Log::info('Original view exists for hook_key ' . ($record->hook_key ?? 'null') . ': ' . ($exists ? 'Yes' : 'No'));
        return $exists;
    }

    public static function copyViewToCustomPath(string $viewPath): bool
    {
        Log::info('Starting copyViewToCustomPath for view path: ' . $viewPath);
        if (empty($viewPath)) {
            Log::warning('Empty view path provided');
            return false;
        }

        $relativePath = str_replace('.', '/', $viewPath) . '.blade.php';
        $source = base_path('packages/webkernel/src/Core/Resources/Views/' . $relativePath);
        $destination = resource_path('views/' . $relativePath);

        Log::info('Source path: ' . $source);
        Log::info('Destination path: ' . $destination);

        try {
            if (!File::exists($source)) {
                Log::error('Source view file does not exist: ' . $source);
                return false;
            }

            if (File::exists($destination)) {
                Log::warning('Destination view file already exists: ' . $destination);
                return false;
            }

            $destinationDir = dirname($destination);
            if (!File::exists($destinationDir)) {
                Log::info('Creating destination directory: ' . $destinationDir);
                File::makeDirectory($destinationDir, 0755, true);
            }

            if (!is_writable($destinationDir)) {
                Log::error('Destination directory is not writable: ' . $destinationDir);
                return false;
            }

            $result = File::copy($source, $destination);
            Log::info('Copy operation ' . ($result ? 'succeeded' : 'failed') . ': ' . $source . ' to ' . $destination);

            if ($result && File::exists($destination)) {
                chmod($destination, 0644);
                Log::info('File permissions set to 0644 for: ' . $destination);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Copy view failed: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public static function validateBladeSyntax(?string $content): bool
    {
        if (empty(trim($content))) {
            Log::warning('Empty Blade content provided');
            return false;
        }
        try {
            Blade::compileString($content);
            Log::info('Blade syntax validation passed');
            return true;
        } catch (Throwable $e) {
            Log::error('Blade syntax validation failed: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public static function revertToOriginalView(string $viewPath): bool
    {
        Log::info('Starting revertToOriginalView for view path: ' . $viewPath);
        if (empty($viewPath)) {
            Log::warning('Empty view path provided');
            return false;
        }

        $relativePath = str_replace('.', '/', $viewPath) . '.blade.php';
        $source = base_path('packages/webkernel/src/Core/Resources/Views/' . $relativePath);
        $destination = resource_path('views/' . $relativePath);

        try {
            if (!File::exists($source)) {
                Log::error('Original view file does not exist: ' . $source);
                return false;
            }

            $destinationDir = dirname($destination);
            if (!is_writable($destinationDir)) {
                Log::error('Destination directory is not writable: ' . $destinationDir);
                return false;
            }

            File::put($destination, File::get($source));
            chmod($destination, 0644);
            Log::info('View reverted successfully: ' . $destination);
            return true;
        } catch (Exception $e) {
            Log::error('View revert failed: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Webkernel\Core\Filament\Resources\RenderHookSettingResource\Pages\ListRenderHookSettings::route('/'),
            'create' => \Webkernel\Core\Filament\Resources\RenderHookSettingResource\Pages\CreateRenderHookSetting::route('/create'),
          //  'view' => \Webkernel\Core\Filament\Resources\RenderHookSettingResource\Pages\ViewRenderHookSetting::route('/{record}'),
          //  'edit' => \Webkernel\Core\Filament\Resources\RenderHookSettingResource\Pages\EditRenderHookSetting::route('/{record}/edit'),
        ];
    }
}
