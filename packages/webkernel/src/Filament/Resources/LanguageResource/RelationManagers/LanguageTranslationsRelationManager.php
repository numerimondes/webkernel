<?php

namespace Webkernel\Filament\Resources\LanguageResource\RelationManagers;
use Filament\Forms;

use Filament\Tables;
use Webkernel\Models\Language;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Webkernel\Models\LanguageTranslation;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Notifications\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;

class LanguageTranslationsRelationManager extends RelationManager
{
    protected static string $relationship = 'languageTranslations';

    public function isReadOnly(): bool
    {
        return false; // To be editable in view embedded mode
    }

    public function form(Form $form): Form
    {
        $languages = Language::all();

        return $form->schema([
            Forms\Components\Grid::make(columns: 3)
                ->schema([
                    Forms\Components\TextInput::make('lang_ref')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('app', request('app')))
                        ->label('Clé de traduction')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, $livewire) {
                            // Clean the key using slug
                            $cleaned = Str::slug($state, '_');
                            if ($state !== $cleaned) {
                                Notification::make()
                                    ->title('Clé nettoyée')
                                    ->body("Clé transformée en : $cleaned")
                                    ->warning()
                                    ->send();
                                $set('lang_ref', $cleaned);
                            }

                            // Check if this key already exists
                            if (!empty($cleaned)) {
                                $existingTranslation = LanguageTranslation::where('lang_ref', $cleaned)
                                    ->where('app', $livewire->data['app'] ?? 'core')
                                    ->where('theme', $livewire->data['theme'] ?? 'none')
                                    ->first();

                                if ($existingTranslation) {
                                    // If key exists, notify the user
                                    Notification::make()
                                        ->title('Clé existante')
                                        ->body("La clé '$cleaned' existe déjà. Les traductions existantes ont été chargées.")
                                        ->info()
                                        ->send();

                                    // Fetch all translations for this key
                                    $translations = LanguageTranslation::where('lang_ref', $cleaned)
                                        ->where('app', $livewire->data['app'] ?? 'core')
                                        ->where('theme', $livewire->data['theme'] ?? 'none')
                                        ->get()
                                        ->keyBy('lang');

                                    // Set form values from existing translations
                                    $translationsData = [];
                                    foreach ($translations as $translation) {
                                        $translationsData[0][$translation->lang] = $translation->translation;
                                    }

                                    $set('translations', $translationsData);
                                    $set('is_edit_mode', true);
                                }
                            }
                        }),
                    Forms\Components\Select::make('app')
                        ->label(lang('col_translation_app'))
                        ->searchable()
                        ->options([
                            lang('numerimondes') => [
                                'core' => lang('core'),  // Traduction dynamique selon la langue de l'utilisateur
                                'notification' => lang('notification'),  // Idem pour notification
                            ],
                            lang_i('no_attribution') => [
                                'none' => lang('no_app'),  // Traduction dynamique pour "no_app"
                            ],
                        ])
                        ->createOptionForm([
                            Forms\Components\TextInput::make('app'),
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('theme')
                        ->default('none')
                        ->required(),
                    Forms\Components\Hidden::make('is_edit_mode')
                        ->default(false),
                ]),

            Forms\Components\Repeater::make('translations')
                ->label(lang(''))
                ->schema(
                    $languages->map(
                        fn($lang) =>
                        Forms\Components\Textarea::make($lang->id)
                            ->label(new HtmlString(lang('repeater_title_translation') . ' <img src="' . asset('assets/flags/language/' . $lang->code . '.svg') . '" class="h-4 w-4 inline-block" alt="Flag" style="margin-right:8px; margin-left:8px;"> ' . $lang->label . ' (' . $lang->code . ')'))
                            ->maxLength(1000)
                            ->columnSpanFull()
                    )->toArray()
                )
                ->columns(1)
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lang_ref')
            ->columns([
                Tables\Columns\TextColumn::make('lang_ref')
                    ->label('Clé')
                    ->searchable(),
                Tables\Columns\TextColumn::make('language.label')
                    ->label('Langue'),
                Tables\Columns\TextColumn::make('translation')
                    ->label('Traduction')
                    ->limit(50),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data) {
                        return DB::transaction(function () use ($data) {
                            // Check if we're in edit mode (existing key)
                            $isEditMode = $data['is_edit_mode'] ?? false;
                            $langRef = $data['lang_ref'];
                            $app = $data['app'];
                            $theme = $data['theme'];

                            // Check if this key already exists
                            $keyExists = LanguageTranslation::where('lang_ref', $langRef)
                                ->where('app', $app)
                                ->where('theme', $theme)
                                ->exists();

                            // If key exists but not in edit mode, notify user and abort
                            if ($keyExists && !$isEditMode) {
                                Notification::make()
                                    ->title('Clé déjà existante')
                                    ->body("La clé '$langRef' existe déjà. Utilisez le mode édition pour la modifier.")
                                    ->warning()
                                    ->send();

                                // Instead of throwing exception, which may disrupt UI,
                                // we can return one of the existing translations
                                return LanguageTranslation::where('lang_ref', $langRef)
                                    ->where('app', $app)
                                    ->where('theme', $theme)
                                    ->first();
                            }

                            $translations = [];
                            $texts = $data['translations'][0] ?? [];
                            $createdTranslation = null;  // Pour récupérer la traduction créée

                            // Handle both create and update scenarios
                            foreach ($texts as $langId => $text) {
                                try {
                                    if ($isEditMode || $keyExists) {
                                        // Update existing translation
                                        $createdTranslation = LanguageTranslation::updateOrCreate(
                                            [
                                                'lang_ref' => $langRef,
                                                'lang' => $langId,
                                                'app' => $app,
                                                'theme' => $theme,
                                            ],
                                            ['translation' => $text]
                                        );
                                    } else {
                                        // Create new translation
                                        $createdTranslation = LanguageTranslation::create([
                                            'lang_ref' => $langRef,
                                            'lang' => $langId,
                                            'translation' => $text,
                                            'app' => $app,
                                            'theme' => $theme,
                                        ]);
                                    }

                                    $translations[] = $createdTranslation;
                                } catch (QueryException $e) {
                                    // Handle duplicate entry error
                                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                                        Notification::make()
                                            ->title('Erreur de duplication')
                                            ->body("Une traduction pour la clé '$langRef' et la langue '$langId' existe déjà.")
                                            ->danger()
                                            ->send();

                                        // Get the existing translation instead
                                        $existingTranslation = LanguageTranslation::where('lang_ref', $langRef)
                                            ->where('lang', $langId)
                                            ->where('app', $app)
                                            ->where('theme', $theme)
                                            ->first();

                                        if ($existingTranslation) {
                                            $translations[] = $existingTranslation;
                                        }
                                    } else {
                                        // Re-throw other query exceptions
                                        throw $e;
                                    }
                                }
                            }

                            // Calcul du délai restant avant que la suppression soit interdite
                            if ($createdTranslation && !$isEditMode) {
                                $createdAt = $createdTranslation->created_at instanceof Carbon
                                    ? $createdTranslation->created_at
                                    : Carbon::parse($createdTranslation->created_at);

                                $delay = $createdAt->diffInSeconds(now()) + 60; // Délai de 1 minute avant suppression interdite

                                // Afficher une notification à l'utilisateur
                                Notification::make()
                                    ->title('Suppression de la clé de traduction')
                                    ->body("Si vous envisagez de supprimer la clé de traduction `{$data['lang_ref']}` il vous reste {$delay} secondes pour le faire.")
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

                            // Show success notification
                            $action = $isEditMode ? 'modifiée' : 'créée';
                            Notification::make()
                                ->title('Traduction ' . $action)
                                ->body("La traduction pour '{$langRef}' a été {$action} avec succès.")
                                ->success()
                                ->send();

                            return $translations[0] ?? null;
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, LanguageTranslation $record): array {
                        // Récupérer la clé lang_ref, soit à partir de $data, soit à partir de $record
                        $langRef = $data['lang_ref'] ?? $record->lang_ref;

                        // Vérifier si lang_ref est défini
                        if (!$langRef) {
                            throw new \Exception('La clé "lang_ref" est manquante.');
                        }

                        // Récupère toutes les traductions liées à cette clé
                        $translations = LanguageTranslation::where('lang_ref', $langRef)
                            ->where('app', $record->app)
                            ->where('theme', $record->theme)
                            ->get()
                            ->keyBy('lang'); // On peut accéder par ID de langue

                        // Prépare les données pour le repeater
                        $translationsData = [];
                        foreach ($translations as $translation) {
                            $translationsData[0][$translation->lang] = $translation->translation;
                        }

                        $data['translations'] = $translationsData;
                        $data['is_edit_mode'] = true;

                        return $data;
                    })
                    ->form(function (LanguageTranslation $record) {
                        // Récupérer les langues disponibles
                        $languages = Language::all();

                        return [
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('lang_ref')
                                        ->required()
                                        ->default($record->lang_ref)
                                        ->disabled(),
                                    Forms\Components\TextInput::make('app')
                                        ->default($record->app),
                                    Forms\Components\TextInput::make('theme')
                                        ->default($record->theme),
                                    Forms\Components\Hidden::make('is_edit_mode')
                                        ->default(true),
                                ]),

                            Forms\Components\Repeater::make('translations')
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->schema(
                                    $languages->map(
                                        fn($lang) =>
                                        Forms\Components\Textarea::make($lang->id)
                                            ->label("Traduction - {$lang->label} ({$lang->code})")
                                            ->required()
                                            ->debounce(500) // Ajout du debounce pour résoudre le problème de timing
                                            ->columnSpanFull()
                                    )->toArray()
                                )
                                ->columns(1)
                                ->live() // Pour s'assurer que le repeater est réactif
                        ];
                    })
                    ->using(function (LanguageTranslation $record, array $data) {
                        return DB::transaction(function () use ($record, $data) {
                            // Récupérer la clé lang_ref, soit à partir de $data, soit à partir de $record
                            $langRef = $data['lang_ref'] ?? $record->lang_ref;

                            // Vérifier si lang_ref est défini
                            if (!$langRef) {
                                throw new \Exception('La clé "lang_ref" est manquante.');
                            }

                            // Si lang_ref est valide, procéder à l'enregistrement des traductions
                            $translationData = $data['translations'][0] ?? [];

                            foreach ($translationData as $langId => $text) {
                                LanguageTranslation::updateOrCreate(
                                    [
                                        'lang_ref' => $langRef,  // Utiliser $langRef
                                        'lang' => $langId,
                                        'app' => $data['app'],
                                        'theme' => $data['theme'],
                                    ],
                                    ['translation' => $text]
                                );
                            }

                            return $record;
                        });
                    }),

                Tables\Actions\DeleteAction::make()
                    ->hidden(fn($record) => !$record->isDeletable())  // Vérifie si l'enregistrement peut être supprimé
                    ->using(function (LanguageTranslation $record) {
                        LanguageTranslation::where('lang_ref', $record->lang_ref)
                            ->where('app', $record->app)
                            ->where('theme', $record->theme)
                            ->delete();
                    })
            ]);
    }
}
