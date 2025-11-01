<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Filament\Auth;

use BackedEnum;
use DateTimeZone;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Webkernel\Core\Models\User;
use Filament\Actions\BulkAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Illuminate\Support\Facades\Cache;
use Filament\Actions\Action as Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use Webkernel\Aptitudes\Users\Models\SessionHistory;
use Filament\Forms\Components\Placeholder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\ImageEntry;
use Webkernel\Aptitudes\I18n\Models\Language;
use Webkernel\Aptitudes\Users\Models\Session;
use Webkernel\Aptitudes\Users\Models\UserExtensions\UserPreference;
use Webkernel\Aptitudes\Users\Filament\Theming\ThemeTab;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile implements HasForms, HasTable
{
    use InteractsWithTable;

    protected static ?string $model = User::class;
    protected static bool $shouldRegisterNavigation = true;

    protected string $previousUrl = '/';

    public function mount(): void
    {
        if (method_exists(get_parent_class($this), 'mount')) {
            parent::mount();
        }
        $referer = url()->previous();
        if ($referer && !str_contains($referer, request()->url())) {
            $this->previousUrl = $referer;
        }
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-user-circle';
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Retour')
                ->icon('heroicon-o-arrow-left')
                ->url($this->previousUrl),
            Action::make('save')
                ->label('Sauvegarder')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                //View::make('webkernel::components.webkernel.rolebased.common.edit-profile.banner-profile'),

                Grid::make(6)
                    ->schema([
                        // Sidebar (colonne gauche)
                        Grid::make(1)
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Profil')
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->avatar()
                                            ->circleCropper()
                                            ->directory('avatars')
                                            ->visibility('public'),
                                        TextInput::make('username')->label('Nom d\'utilisateur')->maxLength(50),
                                        TextInput::make('name')->label('Nom complet')->required()->maxLength(255),
                                        TextInput::make('country')->label('Pays')->maxLength(100),
                                        Placeholder::make('joined_at')
                                            ->label('Inscrit le')
                                            ->content(fn () => auth()->user()->created_at?->format('d/m/Y') ?? 'N/A'),
                                        Placeholder::make('last_seen_at')
                                            ->label('Dernière visite')
                                            ->content(fn () => auth()->user()->last_login_at?->diffForHumans() ?? 'jamais'),

                                        ...ThemeTab::get(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Stats & Badges')
                                    ->schema([
                                        Placeholder::make('reputation')
                                            ->label('Réputation')
                                            ->content(fn () => auth()->user()->reputation ?? 0),
                                        Placeholder::make('solutions_count')
                                            ->label('Solutions')
                                            ->content(fn () => auth()->user()->solutions_count ?? 0),
                                        Placeholder::make('posts_count')
                                            ->label('Messages')
                                            ->content(fn () => auth()->user()->posts_count ?? 0),
                                        Placeholder::make('badges')
                                            ->label('Badges')
                                            ->content(fn () =>
                                                collect(auth()->user()->badges ?? [])->isEmpty()
                                                    ? 'Aucun badge'
                                                    : collect(auth()->user()->badges)->map(fn($badge) =>
                                                        "<span class='inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full mr-1'>{$badge}</span>"
                                                    )->implode('')
                                            ),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Visiteurs récents')
                                    ->schema([
                                        Placeholder::make('recent_visitors')
                                            ->label('Visiteurs récents')
                                            ->content(fn () => 'Fonctionnalité à venir'),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('À venir (sidebar)')
                                    ->schema([
                                        Placeholder::make('coming_soon_sidebar')
                                            ->content('De nouveaux widgets arriveront ici !'),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        // Main (colonne droite)
                        Grid::make(1)
                            ->columnSpan(4)
                            ->schema([
                                Tabs::make('personal_info_tabs')
                                    ->tabs([
                                        Tab::make('Informations personnelles')
                                            ->icon('heroicon-o-user')
                                            ->schema([
                                                TextInput::make('mobile')
                                                    ->label('Téléphone mobile')
                                                    ->tel()
                                                    ->maxLength(20),
                                                TextInput::make('whatsapp')
                                                    ->label('Numéro WhatsApp')
                                                    ->tel()
                                                    ->maxLength(20),
                                                Select::make('user_lang')
                                                    ->label('Langue')
                                                    ->searchable()
                                                    ->options(static::getAvailableLanguages())
                                                    ,
                                                Select::make('timezone')
                                                    ->label('Fuseau horaire')
                                                    ->searchable()
                                                    ->options(static::getTimezoneOptions())
                                                    ,
                                                Toggle::make('is_active')
                                                    ->label('Compte actif'),
                                                Toggle::make('forceChangePassword')
                                                    ->label('Exiger le changement de mot de passe'),
                                                Toggle::make('marketing_callable')
                                                    ->label('Appels marketing'),
                                                Toggle::make('marketing_whatsappable')
                                                    ->label('WhatsApp marketing'),
                                                Toggle::make('marketing_smsable')
                                                    ->label('SMS marketing'),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                // Add infolist here using a private function
                                Section::make('Actualités')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema($this->getProfileDynamicNewsInfoList())
                                    ])
                                    ->contained(false)
                                    ->inlineLabel(false)
                                    ->columnSpanFull(),

                                Section::make('Sessions actives')
                                    ->contained(false)
                                    ->schema([
                                        $this->getSessionsTable(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Données & RGPD')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Placeholder::make('export_data')
                                                    ->label('Exporter mes données')
                                                    ->content('Recevez une copie de vos données par email.'),
                                                Action::make('export_data')
                                                    ->label('Exporter')
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->color('info')
                                                    ->action(fn () => $this->exportUserData()),
                                            ]),
                                        Grid::make(2)
                                            ->schema([
                                                Placeholder::make('request_deletion')
                                                    ->label('Demander la suppression')
                                                    ->content('Demandez la suppression de votre compte et de vos données.'),
                                                Action::make('request_deletion')
                                                    ->label('Demander')
                                                    ->icon('heroicon-o-trash')
                                                    ->color('warning')
                                                    ->requiresConfirmation()
                                                    ->action(fn () => $this->requestAccountDeletion()),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Signaler un problème')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Placeholder::make('report_issue')
                                                    ->label('Signaler un problème')
                                                    ->content('Un bug ou une suggestion ? Faites-le nous savoir.'),
                                                Action::make('report_issue')
                                                    ->label('Signaler')
                                                    ->icon('heroicon-o-flag')
                                                    ->color('primary')
                                                    ->url('https://github.com/tonrepo/issues'), // à adapter
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                    Section::make(lang('danger_zone'))
                                    ->extraAttributes(['class' => 'border-2 border-red-500 rounded-lg'])
                                    ->schema([
                                        // Désactiver le compte
                                        Grid::make(1)
                                            ->schema([
                                                Grid::make(2)
                                                    ->columns(2)
                                                    ->schema([
                                                        Placeholder::make('deactivate_account')
                                                            ->label(fn () =>
                                                                '<strong>' . lang('disable_account') . '</strong><br>' .
                                                                '<small>' . lang('disable_account_description') . '</small>'
                                                            )
                                                            ->html() // <- Permet d'interpréter le HTML dans le label
                                                            ->extraAttributes(['class' => 'whitespace-pre-wrap']),
                                                        Action::make('deactivate_account')
                                                            ->label(lang('disable'))
                                                            ->color('danger')
                                                            ->requiresConfirmation()
                                                            ->action(fn () => $this->deactivateAccount())
                                                            ->extraAttributes(['class' => 'justify-self-end']),
                                                    ]),
                                            ]),

                                        // Déconnecter les autres sessions
                                        Grid::make(1)
                                            ->schema([
                                                Grid::make(2)
                                                    ->columns(2)
                                                    ->schema([
                                                        Placeholder::make('logout_other_sessions')
                                                            ->label(fn () =>
                                                                '<strong>' . lang('logout_other_sessions') . '</strong><br>' .
                                                                '<small>' . lang('logout_other_sessions_description') . '</small>'
                                                            )
                                                            ->html()
                                                            ->extraAttributes(['class' => 'whitespace-pre-wrap']),
                                                        Action::make('logout_other_sessions')
                                                            ->label(lang('logout'))
                                                            ->color('danger')
                                                            ->requiresConfirmation()
                                                            ->action(fn () => $this->logoutOtherSessions())
                                                            ->extraAttributes(['class' => 'justify-self-end']),
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('À venir')
                                    ->schema([
                                        Placeholder::make('coming_soon')
                                            ->content('De nouvelles fonctionnalités arriveront bientôt !'),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->model(auth()->user())
            ->statePath('data')
            ->fill([
                'preferences' => $this->loadUserPreferences(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('ip_address')
                    ->label('Adresse IP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user_agent')
                    ->label('Navigateur')
                    ->formatStateUsing(fn (string $state): string => $this->parseUserAgent($state))
                    ->limit(50),

                TextColumn::make('last_activity')
                    ->label('Dernière activité')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                BadgeColumn::make('is_current')
                    ->label('Statut')
                    ->getStateUsing(fn ($record) => $record->id === session()->getId())
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Session courante' : 'Autre session')
                    ->colors([
                        'success' => true,
                        'secondary' => false,
                    ]),

                TextColumn::make('location')
                    ->label('Localisation')
                    ->getStateUsing(fn ($record) => $this->getLocationFromIP($record->ip_address))
                    ->placeholder('Localisation inconnue'),
            ])
            ->filters([])
            ->actions([
                Action::make('terminate')
                    ->label('Terminer')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->id !== session()->getId())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Archiver la session avant de la supprimer
                        try {
                            SessionHistory::create([
                                'session_id' => $record->id,
                                'user_id' => $record->user_id,
                                'ip_address' => $record->ip_address,
                                'user_agent' => $record->user_agent,
                                'last_activity' => $record->last_activity,
                                'archived_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            // Si l'archivage échoue, on continue quand même
                        }

                        $record->delete();
                        Cache::forget('user_sessions_' . auth()->id());

                        Notification::make()
                            ->title('Session terminée')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('terminate_selected')
                    ->label('Terminer les sessions sélectionnées')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $sessionIds = $records->pluck('id')->toArray();
                        $currentSessionId = session()->getId();
                        $sessionIds = array_filter($sessionIds, fn ($id) => $id !== $currentSessionId);

                        DB::table('sessions')->whereIn('id', $sessionIds)->delete();
                        Cache::forget('user_sessions_' . auth()->id());

                        Notification::make()
                            ->title('Sessions terminées')
                            ->success()
                            ->send();
                    }),
            ])
            ->heading('Sessions actives')
            ->description('Gérez vos sessions actives')
            ->emptyStateHeading('Aucune session active')
            ->emptyStateDescription('Vous n\'avez aucune session active')
            ->emptyStateIcon('heroicon-o-device-phone-mobile')
            ->poll('30s');
    }

    protected function getTableQuery(): Builder
    {
        return Session::query()
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc');
    }

    protected function exportUserData(): void
    {
        $user = auth()->user();
        $data = [
            'profile' => $user->toArray(),
            'sessions' => Session::where('user_id', $user->id)->get()->toArray(),
            'exported_at' => now()->toISOString(),
        ];

        // Ici vous pourriez envoyer les données par email ou les télécharger
        Notification::make()
            ->title('Export des données')
            ->body('Vos données ont été exportées avec succès')
            ->success()
            ->send();
    }

    protected function requestAccountDeletion(): void
    {
        // Logique pour demander la suppression du compte
        Notification::make()
            ->title('Demande de suppression')
            ->body('Votre demande de suppression a été enregistrée')
            ->warning()
            ->send();
    }

    protected function deactivateAccount(): void
    {
        $user = auth()->user();
        $user->update(['is_active' => false]);

        Notification::make()
            ->title('Compte désactivé')
            ->body('Votre compte a été désactivé')
            ->warning()
            ->send();
    }

    protected function logoutOtherSessions(): void
    {
        DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', session()->getId())
            ->delete();

        Cache::forget('user_sessions_' . auth()->id());

        Notification::make()
            ->title('Sessions déconnectées')
            ->body('Toutes les autres sessions ont été déconnectées')
            ->success()
            ->send();
    }

    protected function getLocationFromIP(string $ip): string
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Localhost';
        }
        return 'Localisation inconnue';
    }

    protected function parseUserAgent(string $userAgent): string
    {
        if (Str::contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (Str::contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (Str::contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (Str::contains($userAgent, 'Edge')) {
            return 'Edge';
        }
        return 'Navigateur inconnu';
    }

    protected function logoutOtherBrowserSessions(string $password): void
    {
        if (!Hash::check($password, auth()->user()->getAuthPassword())) {
            throw new \Exception('Mot de passe invalide');
        }

        DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', session()->getId())
            ->delete();

        Cache::forget('user_sessions_' . auth()->id());
    }

    public static function getTimezoneOptions(): array
    {
        return Cache::remember('timezone_options', now()->addDays(7), function () {
            $timezones = DateTimeZone::listIdentifiers();
            return collect($timezones)->mapWithKeys(function ($timezone) {
                try {
                    $offset = now()->setTimezone($timezone)->format('P');
                    return [$timezone => "($offset) $timezone"];
                } catch (\Exception $e) {
                    return [$timezone => $timezone];
                }
            })->toArray();
        });
    }

    public static function getAvailableLanguages(): array
    {
        return Cache::remember('available_languages', now()->addHour(), function () {
            try {
                return Language::where('is_active', true)
                    ->pluck('label', 'code')
                    ->toArray();
            } catch (\Exception $e) {
                return ['en' => 'English', 'fr' => 'Français'];
            }
        });
    }

    public static function getLabel(): string
    {
        return 'Mon profil personnel';
    }

    public function getTitle(): string
    {
        return 'Paramètres du profil';
    }

    public function getSubheading(): ?string
    {
        return 'Gérez vos informations personnelles et paramètres de compte';
    }

    public static function getRelations(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Profil mis à jour')
            ->body('Votre profil a été mis à jour avec succès');
    }

    protected function getRedirectUrl(): string
    {
        return static::getUrl();
    }

    protected function beforeSave(): void
    {
        Cache::forget('user_sessions_' . auth()->id());
        $user = auth()->user();
        $user->save();
    }

    protected function afterSave(): void
    {
        $user = auth()->user();
        if ($user->wasChanged('user_lang')) {
            Cache::forget('available_languages');
        }

        // Sauvegarder les préférences utilisateur
        $this->saveUserPreferences();
    }

    protected function saveUserPreferences(): void
    {
        $user = auth()->user();
        $preferences = UserPreference::getOrCreateForUser($user);

        // Récupérer les données du formulaire
        $formData = $this->form->getState();

        if (isset($formData['preferences'])) {
            $preferencesData = $formData['preferences'];

            // Mettre à jour les champs individuels
            if (isset($preferencesData['theme_name'])) {
                $preferences->theme_name = $preferencesData['theme_name'];
            }

            if (isset($preferencesData['user_lang'])) {
                $preferences->user_lang = $preferencesData['user_lang'];
            }

            $preferences->save();
        }
    }

    protected function loadUserPreferences(): array
    {
        $user = auth()->user();
        $preferences = UserPreference::getOrCreateForUser($user);

        return [
            'theme_name' => $preferences->theme_name ?? 'webkernel-builder-vibes',
            'user_lang' => $preferences->user_lang ?? 'en',
        ];
    }

    protected function getSessionsTable(): Table
    {
        return Table::make($this)
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('ip_address')
                    ->label('Adresse IP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user_agent')
                    ->label('Navigateur')
                    ->formatStateUsing(fn (string $state): string => $this->parseUserAgent($state))
                    ->limit(50),

                TextColumn::make('last_activity')
                    ->label('Dernière activité')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                BadgeColumn::make('is_current')
                    ->label('Statut')
                    ->getStateUsing(fn ($record) => $record->id === session()->getId())
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Session courante' : 'Autre session')
                    ->colors([
                        'success' => true,
                        'secondary' => false,
                    ]),

                TextColumn::make('location')
                    ->label('Localisation')
                    ->getStateUsing(fn ($record) => $this->getLocationFromIP($record->ip_address))
                    ->placeholder('Localisation inconnue'),
            ])
            ->filters([])
            ->actions([
                Action::make('terminate')
                    ->label('Terminer')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->id !== session()->getId())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Archiver la session avant de la supprimer
                        try {
                            SessionHistory::create([
                                'session_id' => $record->id,
                                'user_id' => $record->user_id,
                                'ip_address' => $record->ip_address,
                                'user_agent' => $record->user_agent,
                                'last_activity' => $record->last_activity,
                                'archived_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            // Si l'archivage échoue, on continue quand même
                        }

                        $record->delete();
                        Cache::forget('user_sessions_' . auth()->id());

                        Notification::make()
                            ->title('Session terminée')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('terminate_selected')
                    ->label('Terminer les sessions sélectionnées')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $sessionIds = $records->pluck('id')->toArray();
                        $currentSessionId = session()->getId();
                        $sessionIds = array_filter($sessionIds, fn ($id) => $id !== $currentSessionId);

                        DB::table('sessions')->whereIn('id', $sessionIds)->delete();
                        Cache::forget('user_sessions_' . auth()->id());

                        Notification::make()
                            ->title('Sessions terminées')
                            ->success()
                            ->send();
                    }),
            ])
            ->heading('Sessions actives')
            ->description('Gérez vos sessions actives')
            ->emptyStateHeading('Aucune session active')
            ->emptyStateDescription('Vous n\'avez aucune session active')
            ->emptyStateIcon('heroicon-o-device-phone-mobile')
            ->poll('30s');
    }
    private function getProfileDynamicNewsInfoList(): array
{
    $news = [
        'features' => [
            'categorie' => 'features',
            'label' => 'news_l_features',
            'icon' => 'heroicon-o-star',
            'short_description' => 'news.features.description',
            'color' => 'text-blue-600 bg-blue-50 dark:bg-blue-900/20',
            'items' => [
                [
                    'title' => 'Nouvelle fonctionnalité',
                    'date' => '2024-06-01',
                    'image' => 'https://images.unsplash.com/photo-1522252234503-e356532cafd5?w=400&h=200&fit=crop',
                    'link' => '#'
                ]
            ]
        ],
        'security' => [
            'categorie' => 'security',
            'label' => 'news_l_security',
            'icon' => 'heroicon-o-shield-check',
            'short_description' => 'news.security.description',
            'color' => 'text-red-600 bg-red-50 dark:bg-red-900/20',
            'items' => [
                [
                    'title' => 'Mise à jour de sécurité',
                    'date' => '2024-05-28',
                    'image' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=400&h=200&fit=crop',
                    'link' => '#'
                ]
            ]
        ],
        'maintenance' => [
            'categorie' => 'maintenance',
            'label' => 'news_l_maintenance',
            'icon' => 'heroicon-o-wrench-screwdriver',
            'short_description' => 'news.maintenance.description',
            'color' => 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20',
            'items' => [
                [
                    'title' => 'Maintenance programmée',
                    'date' => '2024-06-03',
                    'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=400&h=200&fit=crop',
                    'link' => '#'
                ]
            ]
        ],
        'events' => [
            'categorie' => 'events',
            'label' => 'news.events.label',
            'icon' => 'heroicon-o-calendar-days',
            'short_description' => 'news.events.description',
            'color' => 'text-green-600 bg-green-50 dark:bg-green-900/20',
            'items' => [
                [
                    'title' => 'Événement communautaire',
                    'date' => '2024-06-10',
                    'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400&h=200&fit=crop',
                    'link' => '#'
                ]
            ]
        ]
    ];

    $sections = [];

    foreach ($news as $key => $category) {
        $sections[] = Section::make()
            ->contained(false)
            ->inlineLabel(false)
            ->hiddenLabel()
            ->compact()
            ->schema([
                Action::make('news_category_' . $key)
                    ->label(__($category['label']))
                    ->icon($category['icon'])
                    ->badge(count($category['items']))
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'w-full p-4 ' . $category['color'] . ' hover:scale-105 transition-all duration-200'
                    ])
            ])
            ->columnSpan([
                'sm' => 2,
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
            ]);
    }

    return $sections;
}
}
