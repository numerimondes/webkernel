<?php
namespace Webkernel\Filament\Resources\LanguageResource\RelationManagers;

use Exception;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Webkernel\Models\Language;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\QueryException;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Webkernel\Models\LanguageTranslation;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\RelationManagers\RelationManager;

class LanguageTranslationsRelationManager extends RelationManager
{
    protected static string $relationship = 'languageTranslations';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        $languages = Language::all();
        $languageOptions = $languages->pluck('id', 'code')->toArray();

        return $schema->schema([
            Grid::make(columns: 3)
                ->schema([
                    TextInput::make('lang_ref')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('app', request('app')))
                        ->label(lang('form_translation_key_label'))
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set, $livewire) {
                            $cleaned = Str::slug($state, '_');
                            if ($state !== $cleaned) {
                                Notification::make()
                                    ->title(lang('form_translation_key_cleaned_notification_title'))
                                    ->body(lang('form_translation_key_cleaned_notification_body', ['cleaned' => $cleaned]))
                                    ->warning()
                                    ->send();
                                $set('lang_ref', $cleaned);
                            }

                            if (!empty($cleaned)) {
                                // Simply check if the key exists
                                $existingTranslation = LanguageTranslation::where('lang_ref', $cleaned)->first();
                                if ($existingTranslation) {
                                    Notification::make()
                                        ->title(lang('form_translation_key_exists_notification_title'))
                                        ->body("Key '{$cleaned}' already exists")
                                        ->warning()
                                        ->send();

                                    // Get all translations for this key
                                    $translationsQuery = LanguageTranslation::where('lang_ref', $cleaned)->get();

                                    // Prepare translations for the form fields
                                    foreach ($translationsQuery as $translation) {
                                        // Get language code from ID
                                        $language = Language::find($translation->lang);
                                        if ($language) {
                                            $set("translations.{$language->code}", $translation->translation);
                                        }
                                    }
                                    $set('is_edit_mode', true);
                                }
                            }
                        }),

                    Select::make('app')
                        ->label(lang('form_translation_app_label'))
                        ->searchable()
                        ->options([
                            'core' => lang('core'),
                            'renderhooks' => lang('renderhooks'),
                            'notification' => lang('notification'),
                        ])
                        ->required()
                        ->default('core'),

                    TextInput::make('theme')
                        ->default('none')
                        ->required(),
                ]),

            Fieldset::make('translations')
                ->label(lang('Translations'))
                ->schema(
                    $languages->map(function ($lang) {
                        return Textarea::make("translations.{$lang->code}")
                            ->label(new HtmlString(
                                lang('repeater_title_translation') .
                                ' <span class="inline-flex items-center align-middle mx-1">' .
                                preg_replace(
                                    '/<svg/',
                                    '<svg width="16" height="16"',
                                    file_get_contents(base_path("packages/webkernel/src/public/assets/flags/language/{$lang->code}.svg"))
                                ) .
                                '</span>' .
                                $lang->label . " ({$lang->code})"
                            ))
                            ->maxLength(1000)
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                // Ensure at least one translation is present
                                $hasTranslation = false;
                                $languages = Language::all();
                                foreach ($languages as $lang) {
                                    if (!empty($get("translations.{$lang->code}"))) {
                                        $hasTranslation = true;
                                        break;
                                    }
                                }
                                // Set a flag to track if we have translations
                                $set('has_translations', $hasTranslation);
                            })
                            ->columnSpanFull();
                    })->toArray()
                )
                ->columns(1),

            // Hidden field to store language ID mapping
            Hidden::make('language_mapping')
                ->default(json_encode($languageOptions))
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        // Get all languages for filters
        $languages = Language::all();
        $languagesOptions = $languages->pluck('label', 'id')->toArray();

        // Get all available applications
        $appOptions = LanguageTranslation::select('app')
            ->distinct()
            ->pluck('app', 'app')
            ->toArray();

        // Get all available themes
        $themeOptions = LanguageTranslation::select('theme')
            ->distinct()
            ->pluck('theme', 'theme')
            ->toArray();

        return $table
            ->recordTitleAttribute('lang_ref')
            ->columns([
                TextColumn::make('lang_ref')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('translation')
                    ->label('Translation')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('app')
                    ->label('Application')
                    ->sortable(),
                TextColumn::make('theme')
                    ->label('Theme')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Structure translations data for processing
                        if (isset($data['translations']) && !isset($data['translations'][0])) {
                            // Keep flat structure - form now uses flat structure
                            // This is compatible with the new form structure
                        }
                        return $data;
                    })
                    ->using(function (array $data, string $model): \Illuminate\Database\Eloquent\Model {
                        return DB::transaction(function () use ($data, $model) {
                            $isEditMode = $data['is_edit_mode'] ?? false;
                            $langRef = $data['lang_ref'];
                            $app = $data['app'];
                            $theme = $data['theme'];

                            // Check if key already exists
                            $keyExists = LanguageTranslation::where('lang_ref', $langRef)
                                ->where('app', $app)
                                ->where('theme', $theme)
                                ->exists();

                            if ($keyExists && !$isEditMode) {
                                Notification::make()
                                    ->title('Key already exists')
                                    ->body("The key '$langRef' already exists. Use edit mode to modify it.")
                                    ->warning()
                                    ->send();

                                // Return an existing translation to avoid null
                                $existingTranslation = LanguageTranslation::where('lang_ref', $langRef)
                                    ->where('app', $app)
                                    ->where('theme', $theme)
                                    ->first();
                                return $existingTranslation;
                            }

                            $translations = [];
                            // Get translations directly from the flat structure
                            $texts = $data['translations'] ?? [];
                            $createdTranslation = null;

                            // Debug the structure
                            Log::debug('Translation data structure: ', ['data' => $data]);

                            // Ensure we have at least one translation
                            if (empty($texts)) {
                                // Get the first available language
                                $firstLang = Language::first();
                                if ($firstLang) {
                                    $texts[$firstLang->code] = '';
                                } else {
                                    $texts['en'] = '';
                                }
                            }

                            // Process each translation
                            foreach ($texts as $langCode => $text) {
                                try {
                                    // Get language ID from code
                                    $language = Language::where('code', $langCode)->first();
                                    if (!$language) {
                                        Log::warning("Could not find language with code: {$langCode}");
                                        continue;
                                    }

                                    $langId = $language->id;

                                    if ($isEditMode || $keyExists) {
                                        $translation = LanguageTranslation::updateOrCreate(
                                            [
                                                'lang_ref' => $langRef,
                                                'lang' => $langId,
                                                'app' => $app,
                                                'theme' => $theme,
                                            ],
                                            ['translation' => $text]
                                        );
                                    } else {
                                        $translation = LanguageTranslation::create([
                                            'lang_ref' => $langRef,
                                            'lang' => $langId,
                                            'translation' => $text,
                                            'app' => $app,
                                            'theme' => $theme,
                                        ]);
                                    }

                                    $translations[] = $translation;
                                    if ($translation) {
                                        $createdTranslation = $translation;
                                    }
                                } catch (QueryException $e) {
                                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                                        Notification::make()
                                            ->title('Duplicate error')
                                            ->body("A translation for key '$langRef' and language '$langCode' already exists.")
                                            ->danger()
                                            ->send();

                                        $existingTranslation = LanguageTranslation::where('lang_ref', $langRef)
                                            ->where('lang', $langId)
                                            ->where('app', $app)
                                            ->where('theme', $theme)
                                            ->first();

                                        if ($existingTranslation) {
                                            $translations[] = $existingTranslation;
                                            $createdTranslation = $existingTranslation;
                                        }
                                    } else {
                                        throw $e;
                                    }
                                }
                            }

                            // Send notification for new translations
                            if ($createdTranslation && !$isEditMode) {
                                $createdAt = $createdTranslation->created_at instanceof Carbon
                                    ? $createdTranslation->created_at
                                    : Carbon::parse($createdTranslation->created_at);

                                $delay = $createdAt->diffInSeconds(now()) + 60;

                                Notification::make()
                                    ->title('Translation key deletion')
                                    ->body("If you plan to delete the translation key `{$data['lang_ref']}`, you have {$delay} seconds to do so.")
                                    ->icon('heroicon-o-document-text')
                                    ->color('danger')
                                    ->seconds(9)
                                    ->danger()
                                    ->send()
                                    ->actions([
                                        Action::make('view')
                                            ->button(),
                                        Action::make('undo')
                                            ->color('gray'),
                                    ]);
                            }

                            // Success notification
                            $action = $isEditMode ? 'updated' : 'created';
                            Notification::make()
                                ->title('Translation ' . $action)
                                ->body("The translation for '{$langRef}' has been {$action} successfully.")
                                ->success()
                                ->send();

                            // Return the first created translation or fallback
                            return $createdTranslation ?? $translations[0] ?? new LanguageTranslation([
                                'lang_ref' => $langRef,
                                'app' => $app,
                                'theme' => $theme,
                                'lang' => Language::first()->id ?? 1,
                                'translation' => ''
                            ]);
                        });
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, LanguageTranslation $record): array {
                        $langRef = $data['lang_ref'] ?? $record->lang_ref;
                        if (!$langRef) {
                            throw new Exception('Missing "lang_ref" key.');
                        }

                        // Get all translations for this record
                        $translations = LanguageTranslation::where('lang_ref', $langRef)
                            ->where('app', $record->app)
                            ->where('theme', $record->theme)
                            ->get();

                        // Map translations to language codes
                        $translationsData = [];
                        foreach ($translations as $translation) {
                            // Get language code from ID
                            $language = Language::find($translation->lang);
                            if ($language) {
                                $translationsData[$language->code] = $translation->translation;
                            }
                        }

                        // Use the new flat structure
                        $data['translations'] = $translationsData;
                        $data['is_edit_mode'] = true;

                        return $data;
                    })
                    ->schema(function (LanguageTranslation $record) {
                        $languages = Language::all();
                        return [
                            Grid::make(3)
                                ->schema([
                                    TextInput::make('lang_ref')
                                        ->required()
                                        ->default($record->lang_ref)
                                        ->disabled(),
                                    TextInput::make('app')
                                        ->default($record->app),
                                    TextInput::make('theme')
                                        ->default($record->theme),
                                    Hidden::make('is_edit_mode')
                                        ->default(true),
                                ]),
                            Fieldset::make('translations')
                                ->label(lang('Translations'))
                                ->schema(
                                    $languages->map(function ($lang) {
                                        return Textarea::make("translations.{$lang->code}")
                                            ->label("Translation - {$lang->label} ({$lang->code})")
                                            ->debounce(500)
                                            ->columnSpanFull();
                                    })->toArray()
                                )
                                ->columns(1)
                        ];
                    })
                    ->using(function (LanguageTranslation $record, array $data) {
                        return DB::transaction(function () use ($record, $data) {
                            $langRef = $data['lang_ref'] ?? $record->lang_ref;
                            if (!$langRef) {
                                throw new Exception('Missing "lang_ref" key.');
                            }

                            // Get translations data in flat structure
                            $translationData = $data['translations'] ?? [];

                            foreach ($translationData as $langCode => $text) {
                                // Get language ID from code
                                $language = Language::where('code', $langCode)->first();
                                if (!$language) {
                                    continue; // Skip if language not found
                                }

                                LanguageTranslation::updateOrCreate(
                                    [
                                        'lang_ref' => $langRef,
                                        'lang' => $language->id,
                                        'app' => $data['app'],
                                        'theme' => $data['theme'],
                                    ],
                                    ['translation' => $text]
                                );
                            }

                            // Success notification
                            Notification::make()
                                ->title('Translation updated')
                                ->body("The translation for '{$langRef}' has been updated successfully.")
                                ->success()
                                ->send();

                            return $record;
                        });
                    }),

                DeleteAction::make()
                    ->hidden(fn($record) => !$record->isDeletable())
                    ->using(function (LanguageTranslation $record) {
                        LanguageTranslation::where('lang_ref', $record->lang_ref)
                            ->where('app', $record->app)
                            ->where('theme', $record->theme)
                            ->delete();

                        // Return a boolean to confirm deletion
                        return true;
                    })
            ])
            ->filters([
                SelectFilter::make('app')
                    ->label('Application')
                    ->options($appOptions)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('theme')
                    ->label('Theme')
                    ->options($themeOptions)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('has_translation')
                    ->label(lang('Translation'))
                    ->placeholder(lang('All Translations'))
                    ->native(false)
                    ->options([
                        '1' => lang('With Content'),
                        '0' => lang('Without Content'),
                    ])
                    ->query(function (Builder $query, $state): Builder {
                        return match ($state) {
                            '1' => $query->whereRaw('LENGTH(TRIM(translation)) > 0'),
                            '0' => $query->whereRaw('LENGTH(TRIM(translation)) = 0 OR translation IS NULL'),
                            default => $query,
                        };
                    }),

                Filter::make('key_prefix')
                    ->label('Key prefix')
                    ->schema([
                        Select::make('prefix')
                            ->label('Common prefix')
                            ->options(function () {
                                // Get all unique prefixes (part before first "_")
                                $prefixes = LanguageTranslation::selectRaw('SUBSTRING_INDEX(lang_ref, "_", 1) as prefix')
                                    ->distinct()
                                    ->pluck('prefix')
                                    ->filter()
                                    ->mapWithKeys(fn ($item) => [$item => $item])
                                    ->toArray();
                                return $prefixes;
                            })
                            ->searchable()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['prefix'],
                            fn(Builder $query, $value): Builder => $query->where('lang_ref', 'like', "{$value}_%")
                        );
                    }),

                Filter::make('recently_modified')
                    ->label('Recently modified')
                    ->schema([
                        Select::make('period')
                            ->label('Period')
                            ->options([
                                'today' => "Today",
                                'yesterday' => 'Yesterday',
                                'last7days' => 'Last 7 days',
                                'last30days' => 'Last 30 days',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['period'], function (Builder $query, $period) {
                            return match ($period) {
                                'today' => $query->whereDate('updated_at', Carbon::today()),
                                'yesterday' => $query->whereDate('updated_at', Carbon::yesterday()),
                                'last7days' => $query->where('updated_at', '>=', Carbon::now()->subDays(7)),
                                'last30days' => $query->where('updated_at', '>=', Carbon::now()->subDays(30)),
                                default => $query
                            };
                        });
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(5)
            ->persistFiltersInSession();
    }
}
