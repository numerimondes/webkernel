<?php
namespace Webkernel\Filament\Clusters\Settings\Resources;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Pages\ListRenderHookSettings;
use BladeUI\Icons\Components\Icon;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\File;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Process\Process;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Webkernel\Models\RenderHookSetting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Webkernel\Filament\Clusters\Settings;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\View\Exceptions\CompilationException;
use Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource\Pages;
use Webkernel\Filament\Clusters\Settings\Resources\RenderHookSettingResource\RelationManagers;

class RenderHookSettingResource extends Resource
{
    protected static ?string $model = RenderHookSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-ellipsis-horizontal-circle';
    protected static ?string $cluster = Settings::class;

    public static function getNavigationLabel(): string
    {
        return lang('Custom Navigation Label');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('hook_key')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                TextInput::make('where_placed')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
                TextInput::make('view_path')
                    ->required()
                    ->disabled()
                    ->maxLength(255),
            ]);
    }public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            IconColumn::make('icon')
                ->label('')
                ->width('1%')
                ->icon(fn($record) => $record->icon)
                ->size(IconColumn\IconColumnSize::Medium),

            TextColumn::make('hook_key')
                ->label(lang('action'))
                ->formatStateUsing(function ($state, $record) {
                    $title = lang($record->hook_key); // Traduction de la clé
                    $desc = lang($record->translation_desc_key); // Traduction de la description
                    return "{$title}<br>{$desc}";
                })
                ->html()
                ->wrap()
                ->color(fn($record) => self::originalViewExists($record) ? null : 'gray'),

            ToggleColumn::make('enabled')
                ->label(lang('enabled'))
                ->disabled(fn($record) => !self::originalViewExists($record))
                ->afterStateUpdated(function (Component $livewire) {
                    $livewire->js("setTimeout(() => { window.dispatchEvent(new CustomEvent('triggerSmoothReload')); }, 150);");
                }),
        ])
        ->filters([])
        ->actions([
            self::getCustomizeViewAction(),
            self::getEditCustomViewAction(),
            self::getDeleteCustomViewAction(),

            EditAction::make()
                ->iconButton()
                ->label(lang('edit'))
                ->modal()
                ->hidden(fn($record) => self::originalViewExists($record)),
        ])
        ->bulkActions([]);
}


    protected static function getCustomizeViewAction(): Action
    {
        return Action::make('customize_view')
            ->label('Customize View')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->visible(fn($record) => self::originalViewExists($record) && !File::exists(self::getFullViewPath($record)))
            ->requiresConfirmation()
            ->modalHeading('Customize View')
            ->modalDescription('This will create a customized copy of the original view file that you can modify.')
            ->modalSubmitActionLabel('Customize')
            ->action(function ($record, Component $livewire) {
                $copied = self::copyViewToCustomPath(
                    self::getViewPathFromHookKey($record->hook_key)
                );
                $notification = Notification::make();
                if ($copied) {
                    $notification->title('View Copied Successfully')
                        ->success();
                } else {
                    $notification->title('View Already Exists or Copy Failed')
                        ->warning();
                }
                $notification->send();
                $livewire->js("setTimeout(() => { window.dispatchEvent(new CustomEvent('triggerSmoothReload')); }, 150);");
            });
    }

    protected static function getEditCustomViewAction(): Action
    {
        return Action::make('edit_custom_view')
            ->label('Edit View')
            ->icon('heroicon-m-code-bracket')
            ->color('secondary')
            ->visible(fn ($record) => self::originalViewExists($record) && File::exists(self::getFullViewPath($record)))
            ->form(fn ($record) => [
                Forms\Components\Textarea::make('view_contents')
                    ->label('Blade Content')
                    ->default(function () use ($record) {
                        $fullPath = self::getFullViewPath($record);
                        return File::exists($fullPath) ? File::get($fullPath) : '';
                    })
                    ->rows(20)
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set, $get, $component) {
                        $isValid = self::validateBladeSyntax($state);
                        $message = $isValid ? 'Valid Syntax' : 'Syntax Error';
                        $color = $isValid ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)';
                        $component->hint(new HtmlString(
                            "<span style='color: {$color}; font-weight: 500;'>{$message}</span>"
                        ));
                    })
                    ->helperText('Note: Ensure that the Blade syntax is correct before saving. The checks only validate that your code will not crash Laravel. If your code happens to crash the app, please let us know how it occurred.'),
            ])
            ->action(function (array $data, $record, Component $livewire) {
                if (!self::validateBladeSyntax($data['view_contents'])) {
                    Notification::make()
                        ->title('Syntax Error')
                        ->body('Invalid Blade/PHP syntax detected. Changes not saved.')
                        ->danger()
                        ->send();
                    return;
                }
                $path = self::getFullViewPath($record);
                $backupPath = $path . '.bak';
                try {
                    if (File::exists($path)) {
                        File::put($backupPath, File::get($path));
                    }
                    File::put($path, $data['view_contents']);
                    Notification::make()
                        ->title('View Saved Successfully')
                        ->success()
                        ->send();
                    $livewire->js("
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('triggerSmoothReload'));
                        }, 150);
                    ");
                } catch (\Exception $e) {
                    Log::error('View save failed: ' . $e->getMessage());
                    if (File::exists($backupPath)) {
                        File::put($path, File::get($backupPath));
                        File::delete($backupPath);
                    } else {
                        self::revertToOriginalView(self::getViewPathFromHookKey($record->hook_key));
                    }
                    Notification::make()
                        ->title('Save Failed')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    protected static function getDeleteCustomViewAction(): Action
    {
        return Action::make('delete_custom_view')
            ->label('Delete View')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->visible(function ($record) {
                $customPath = self::getFullViewPath($record);
                return self::originalViewExists($record) && File::exists($customPath);
            })
            ->requiresConfirmation()
            ->modalHeading('Delete Custom View')
            ->modalIcon('heroicon-o-trash')
            ->modalDescription(function ($record) {
                $customPath = self::getFullViewPath($record);
                $originalPath = self::getOriginalViewPath($record->hook_key);
                if (File::exists($originalPath) && File::get($customPath) === File::get($originalPath)) {
                    return 'This custom view matches the original. Remove it?';
                }
                return 'This will delete your customized view and restore default behavior.';
            })
            ->modalSubmitActionLabel('Confirm Deletion')
            ->action(function ($record, Component $livewire) {
                try {
                    File::delete(self::getFullViewPath($record));
                    Notification::make()
                        ->title('View Deleted Successfully')
                        ->success()
                        ->send();
                    $livewire->js("setTimeout(() => { window.dispatchEvent(new CustomEvent('triggerSmoothReload')); }, 150);");
                } catch (\Exception $e) {
                    Log::error('View deletion failed: ' . $e->getMessage());
                    Notification::make()
                        ->title('Deletion Failed')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public static function getViewPathFromHookKey(string $key): string
    {
        $hookSettings = RenderHookSetting::all();
        $viewPaths = [];
        foreach ($hookSettings as $hookSetting) {
            $viewPaths[$hookSetting->hook_key] = $hookSetting->view_path;
        }
        return $viewPaths[$key] ?? '';
    }

    private static function getFullViewPath($record): string
    {
        $viewPath = self::getViewPathFromHookKey($record->hook_key);
        return empty($viewPath) ? '' : resource_path('views/'.str_replace('.', '/', $viewPath).'.blade.php');
    }

    private static function getOriginalViewPath(string $hookKey): string
    {
        $viewPath = self::getViewPathFromHookKey($hookKey);
        return empty($viewPath) ? '' : base_path("packages/webkernel/src/resources/views/".str_replace('.', '/', $viewPath).'.blade.php');
    }

    public static function originalViewExists($record): bool
    {
        $originalPath = self::getOriginalViewPath($record->hook_key);
        return !empty($originalPath) && File::exists($originalPath);
    }

    public static function copyViewToCustomPath(string $viewPath): bool
    {
        if (empty($viewPath)) {
            Log::warning('Empty view path in copy operation');
            return false;
        }
        $relativePath = str_replace('.', '/', $viewPath).'.blade.php';
        $source = base_path("packages/webkernel/src/resources/views/{$relativePath}");
        $destination = resource_path("views/{$relativePath}");
        try {
            if (File::exists($source) && !File::exists($destination)) {
                File::ensureDirectoryExists(dirname($destination));
                return File::copy($source, $destination);
            }
            return false;
        } catch (\Exception $e) {
            Log::error('View copy failed: '.$e->getMessage());
            return false;
        }
    }

    public static function validateBladeSyntax(?string $content): bool
    {
        if (empty(trim($content))) return false;
        try {
            if (is_null($content)) {
                return false;
            }
            $compiled = Blade::compileString($content);
            if (preg_match('/<\?php/', $compiled)) {
                $process = new Process([PHP_BINARY, '-l']);
                $process->setInput($compiled);
                $process->run();
                if (!$process->isSuccessful()) {
                    Log::error('PHP syntax error: ' . $process->getErrorOutput());
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Blade compilation failed: ' . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            Log::error('Syntax validation error: ' . $e->getMessage());
            return false;
        }
    }

    public static function revertToOriginalView(string $viewPath): bool
    {
        if (empty($viewPath)) return false;
        $relativePath = str_replace('.', '/', $viewPath).'.blade.php';
        $source = base_path("packages/webkernel/src/resources/views/{$relativePath}");
        $destination = resource_path("views/{$relativePath}");
        try {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));
                File::put($destination, File::get($source));
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('View revert failed: '.$e->getMessage());
            return false;
        }
    }


    public static function getRelations(): array
    {
        return [
            // Relation managers
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRenderHookSettings::route('/'),
            'create' => Pages\CreateRenderHookSetting::route('/create'),
            'view' => Pages\ViewRenderHookSetting::route('/{record}'),
            'edit' => Pages\EditRenderHookSetting::route('/{record}/edit'),
        ];
    }
}
