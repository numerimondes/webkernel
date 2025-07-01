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
                            if (empty($state)) {
                                return;
                            }

                            $cleaned = Str::slug($state, '_');
                            if ($state !== $cleaned) {
                                Notification::make()
                                    ->title(lang('form_translation_key_cleaned_notification_title'))
                                    ->body(lang('form_translation_key_cleaned_notification_body', ['cleaned' => $cleaned]))
                                    ->warning()
                                    ->send();
                                $set('lang_ref', $cleaned);
                            }

                            // Vérifier si la clé existe déjà
                            $existingTranslation = LanguageTranslation::where('lang_ref', $cleaned)->first();
                            if ($existingTranslation) {
                                Notification::make()
                                    ->title(lang('form_translation_key_exists_notification_title'))
                                    ->body("Key '{$cleaned}' already exists")
                                    ->warning()
                                    ->send();

                                // Charger toutes les traductions pour cette clé
                                $translationsQuery = LanguageTranslation::where('lang_ref', $cleaned)->get();
                                foreach ($translationsQuery as $translation) {
                                    $language = Language::find($translation->lang);
                                    if ($language) {
                                        $set("translations.{$language->code}", $translation->translation);
                                    }
                                }
                                $set('is_edit_mode', true);
                            } else {
                                // Réinitialiser le mode édition si c'est une nouvelle clé
                                $set('is_edit_mode', false);
                                // Vider les traductions existantes
                                $languages = Language::all();
                                foreach ($languages as $lang) {
                                    $set("translations.{$lang->code}", '');
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
                        // Créer le contenu du label avec un SVG correctement aligné
                        $flagPath = base_path("packages/webkernel/src/public/assets/flags/language/{$lang->code}.svg");
                        $flagSvg = '';

                        if (file_exists($flagPath)) {
                            $svgContent = file_get_contents($flagPath);
                            // Ajouter des classes CSS pour l'alignement et la taille
                            $flagSvg = preg_replace(
                                '/<svg/',
                                '<svg class="inline-block w-4 h-4 mr-2 align-text-bottom"',
                                $svgContent
                            );
                        }

                        $labelHtml = '<div class="flex items-center">' .
                                    $flagSvg .
                                    '<span>' . lang('repeater_title_translation') . ' ' . $lang->label . " ({$lang->code})" . '</span>' .
                                    '</div>';

                        return Textarea::make("translations.{$lang->code}")
                            ->label(new HtmlString($labelHtml))
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                // Vérifier qu'au moins une traduction est présente
                                $hasTranslation = false;
                                $languages = Language::all();
                                foreach ($languages as $lang) {
                                    if (!empty($get("translations.{$lang->code}"))) {
                                        $hasTranslation = true;
                                        break;
                                    }
                                }
                                $set('has_translations', $hasTranslation);
                            });
                    })->toArray()
                )
                ->columns(1),

            // Champs cachés pour la gestion des états
            Hidden::make('language_mapping')
                ->default(json_encode($languageOptions)),

            Hidden::make('is_edit_mode')
                ->default(false),

            Hidden::make('has_translations')
                ->default(false),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        // Récupérer toutes les langues pour les filtres
        $languages = Language::all();
        $languagesOptions = $languages->pluck('label', 'id')->toArray();

        // Récupérer toutes les applications disponibles
        $appOptions = LanguageTranslation::select('app')
            ->distinct()
            ->pluck('app', 'app')
            ->toArray();

        // Récupérer tous les thèmes disponibles
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
                        // Valider qu'au moins une traduction est fournie
                        $hasTranslation = false;
                        if (isset($data['translations']) && is_array($data['translations'])) {
                            foreach ($data['translations'] as $translation) {
                                if (!empty(trim($translation))) {
                                    $hasTranslation = true;
                                    break;
                                }
                            }
                        }

                        if (!$hasTranslation) {
                            Notification::make()
                                ->title('Validation Error')
                                ->body('At least one translation must be provided.')
                                ->danger()
                                ->send();

                            throw new \Exception('At least one translation must be provided.');
                        }

                        return $data;
                    })
                    ->using(function (array $data, string $model): \Illuminate\Database\Eloquent\Model {
                        return DB::transaction(function () use ($data, $model) {
                            $isEditMode = $data['is_edit_mode'] ?? false;
                            $langRef = $data['lang_ref'];
                            $app = $data['app'];
                            $theme = $data['theme'];
                            $translations = $data['translations'] ?? [];

                            // Vérifier si la clé existe déjà
                            $keyExists = LanguageTranslation::where('lang_ref', $langRef)
                                ->where('app', $app)
                                ->where('theme', $theme)
                                ->exists();

                            if ($keyExists && !$isEditMode) {
                                throw new \Exception("The key '$langRef' already exists for this app/theme combination.");
                            }

                            $createdTranslations = [];
                            $mainTranslation = null;

                            // Traiter chaque traduction
                            foreach ($translations as $langCode => $text) {
                                // Ignorer les traductions vides
                                if (empty(trim($text))) {
                                    continue;
                                }

                                // Récupérer l'ID de la langue à partir du code
                                $language = Language::where('code', $langCode)->first();
                                if (!$language) {
                                    Log::warning("Language not found for code: {$langCode}");
                                    continue;
                                }

                                try {
                                    if ($isEditMode) {
                                        $translation = LanguageTranslation::updateOrCreate(
                                            [
                                                'lang_ref' => $langRef,
                                                'lang' => $language->id,
                                                'app' => $app,
                                                'theme' => $theme,
                                            ],
                                            ['translation' => $text]
                                        );
                                    } else {
                                        $translation = LanguageTranslation::create([
                                            'lang_ref' => $langRef,
                                            'lang' => $language->id,
                                            'translation' => $text,
                                            'app' => $app,
                                            'theme' => $theme,
                                        ]);
                                    }

                                    $createdTranslations[] = $translation;
                                    if (!$mainTranslation) {
                                        $mainTranslation = $translation;
                                    }

                                } catch (QueryException $e) {
                                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                                        // Récupérer la traduction existante
                                        $existingTranslation = LanguageTranslation::where('lang_ref', $langRef)
                                            ->where('lang', $language->id)
                                            ->where('app', $app)
                                            ->where('theme', $theme)
                                            ->first();

                                        if ($existingTranslation) {
                                            $createdTranslations[] = $existingTranslation;
                                            if (!$mainTranslation) {
                                                $mainTranslation = $existingTranslation;
                                            }
                                        }
                                    } else {
                                        throw $e;
                                    }
                                }
                            }

                            // Notification de succès
                            $action = $isEditMode ? 'updated' : 'created';
                            $translationCount = count($createdTranslations);

                            Notification::make()
                                ->title('Translation ' . $action)
                                ->body("The translation key '{$langRef}' has been {$action} with {$translationCount} language(s).")
                                ->success()
                                ->send();

                            // Retourner la traduction principale ou créer un fallback
                            return $mainTranslation ?? LanguageTranslation::create([
                                'lang_ref' => $langRef,
                                'app' => $app,
                                'theme' => $theme,
                                'lang' => Language::first()->id ?? 1,
                                'translation' => array_values($translations)[0] ?? ''
                            ]);
                        });
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, LanguageTranslation $record): array {
                        $langRef = $record->lang_ref;

                        // Récupérer toutes les traductions pour cette clé
                        $translations = LanguageTranslation::where('lang_ref', $langRef)
                            ->where('app', $record->app)
                            ->where('theme', $record->theme)
                            ->get();

                        // Mapper les traductions aux codes de langue
                        $translationsData = [];
                        foreach ($translations as $translation) {
                            $language = Language::find($translation->lang);
                            if ($language) {
                                $translationsData[$language->code] = $translation->translation;
                            }
                        }

                        $data['translations'] = $translationsData;
                        $data['is_edit_mode'] = true;
                        $data['lang_ref'] = $langRef;
                        $data['app'] = $record->app;
                        $data['theme'] = $record->theme;

                        return $data;
                    })
                    ->using(function (LanguageTranslation $record, array $data) {
                        return DB::transaction(function () use ($record, $data) {
                            $langRef = $record->lang_ref;
                            $translationData = $data['translations'] ?? [];

                            foreach ($translationData as $langCode => $text) {
                                $language = Language::where('code', $langCode)->first();
                                if (!$language) {
                                    continue;
                                }

                                if (empty(trim($text))) {
                                    // Supprimer la traduction si elle est vide
                                    LanguageTranslation::where('lang_ref', $langRef)
                                        ->where('lang', $language->id)
                                        ->where('app', $data['app'])
                                        ->where('theme', $data['theme'])
                                        ->delete();
                                } else {
                                    // Mettre à jour ou créer la traduction
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
                            }

                            Notification::make()
                                ->title('Translation updated')
                                ->body("The translation for '{$langRef}' has been updated successfully.")
                                ->success()
                                ->send();

                            return $record;
                        });
                    }),

                DeleteAction::make()
                    ->using(function (LanguageTranslation $record) {
                        return DB::transaction(function () use ($record) {
                            // Supprimer toutes les traductions pour cette clé
                            $deletedCount = LanguageTranslation::where('lang_ref', $record->lang_ref)
                                ->where('app', $record->app)
                                ->where('theme', $record->theme)
                                ->delete();

                            Notification::make()
                                ->title('Translation deleted')
                                ->body("Translation key '{$record->lang_ref}' and all its {$deletedCount} language variants have been deleted.")
                                ->success()
                                ->send();

                            return true;
                        });
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
