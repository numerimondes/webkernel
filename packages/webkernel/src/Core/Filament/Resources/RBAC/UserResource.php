<?php
namespace Webkernel\Core\Filament\Resources\RBAC;

use BackedEnum;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Webkernel\Core\Filament\Resources\RBAC\UserResource\Pages;
use Webkernel\Core\Filament\Resources\RBAC\UserResource\Pages\EditUser;
use Webkernel\Core\Filament\Resources\RBAC\UserResource\Pages\ViewUser;
use Webkernel\Core\Filament\Resources\RBAC\UserResource\Pages\ListUsers;
use Webkernel\Core\Filament\Resources\RBAC\UserResource\Pages\CreateUser;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Webkernel\Core\Models\RBAC\UserPanels;
use Webkernel\Core\Services\PanelsInfoCollector;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

      public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // PARTIE 1 - Informations principales avec avatar
                Section::make('Profil utilisateur')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // COL 1 - Avatar
                                FileUpload::make('avatar')
                                    ->label('Photo de profil')
                                    ->image()
                                    ->avatar()
                                    ->disk('public')
                                    ->directory('avatars')
                                    ->visibility('public')
                                    ->columnSpan(1),

                                // COL 2 - Nom complet et titre
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nom complet')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('title')
                                            ->label('Titre ou Profession')
                                            ->maxLength(255),
                                    ])
                                    ->columnSpan(1),

                                // COL 3 - Informations de contact
                                Grid::make(1)
                                    ->schema([
                                        DatePicker::make('date_of_birth')
                                            ->label('Date de naissance'),

                                        TextInput::make('phone')
                                            ->label('Téléphone (Principal)')
                                            ->tel()
                                            ->maxLength(255),

                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ]),

                // PARTIE 2 - Onglets d'informations
                Tabs::make('Informations')
                    ->tabs([
                        Tab::make('Informations utilisateur')
                            ->schema([
                                TextInput::make('username')
                                    ->label('Nom d\'utilisateur')
                                    ->maxLength(255),

                                Textarea::make('bio')
                                    ->label('Biographie')
                                    ->rows(3),

                                TextInput::make('location')
                                    ->label('Localisation')
                                    ->maxLength(255),

                                Select::make('gender')
                                    ->label('Genre')
                                    ->options([
                                        'male' => 'Masculin',
                                        'female' => 'Féminin',
                                        'other' => 'Autre',
                                        'prefer_not_to_say' => 'Préfère ne pas le dire',
                                    ]),
                            ]),

                        Tab::make('Informations de compte')
                            ->schema([
                                Select::make('status')
                                    ->label('Statut du compte')
                                    ->options([
                                        'active' => 'Actif',
                                        'inactive' => 'Inactif',
                                        'suspended' => 'Suspendu',
                                        'pending' => 'En attente',
                                    ])
                                    ->default('active'),

                                DateTimePicker::make('last_login_at')
                                    ->label('Dernière connexion')
                                    ->disabled(),

                                TextInput::make('login_count')
                                    ->label('Nombre de connexions')
                                    ->numeric()
                                    ->disabled(),

                                Toggle::make('email_verified')
                                    ->label('Email vérifié'),

                                Toggle::make('two_factor_enabled')
                                    ->label('Authentification à deux facteurs'),
                            ]),

                        Tab::make('Notes')
                            ->schema([
                                Textarea::make('admin_notes')
                                    ->label('Notes administratives')
                                    ->rows(4)
                                    ->columnSpanFull(),

                                Textarea::make('public_notes')
                                    ->label('Notes publiques')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                // PARTIE 3 - Onglets de services et pièces jointes
                Tabs::make('Services et Documents')
                    ->tabs([
                        Tab::make('Services associés')
                            ->schema([
                                CheckboxList::make('services')
                                    ->label('Services actifs')
                                    ->options([
                                        'support' => 'Support technique',
                                        'premium' => 'Compte premium',
                                        'newsletter' => 'Newsletter',
                                        'notifications' => 'Notifications push',
                                        'api_access' => 'Accès API',
                                    ])
                                    ->columns(2),

                                DatePicker::make('subscription_start')
                                    ->label('Début d\'abonnement'),

                                DatePicker::make('subscription_end')
                                    ->label('Fin d\'abonnement'),
                            ]),

                        Tab::make('Pièces jointes')
                            ->schema([
                                FileUpload::make('documents')
                                    ->label('Documents')
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('user-documents')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'])
                                    ->maxSize(10240), // 10MB

                                FileUpload::make('certificates')
                                    ->label('Certificats')
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('user-certificates')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->maxSize(5120), // 5MB
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(50),

                TextColumn::make('name')
                    ->label('Nom complet')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->toggleable(),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'suspended',
                        'secondary' => 'inactive',
                    ]),

                TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'suspended' => 'Suspendu',
                        'pending' => 'En attente',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                static::getUserModulesAction(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-users';
    }

public static function getModelLabel(): string
{
    return lang('user');
}

public static function getPluralModelLabel(): string
{
    return lang('users');
}
protected static ?string $recordTitleAttribute = 'name';


    public static function getNavigationGroup(): ?string
{
    return lang('system_menu_all_users_management');
}
   


    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return cache()->remember('user_count', 30, function () {
            return static::getModel()::count();
        });
    }

    public static function getNavigationBadgePollingInterval(): ?string
    {
        return '30s';
    }

    public static function getAvailableModulesTree(): array
    {
        try {
            $allModules = PanelsInfoCollector::getAllPanelsInfo();
            $modulesTree = [];
            
            foreach ($allModules as $namespace => $namespaceData) {
                $modulesTree[$namespace] = [
                    'label' => $namespace,
                    'children' => []
                ];
                
                foreach ($namespaceData['modules'] as $moduleName => $moduleData) {
                    $modulesTree[$namespace]['children'][$moduleName] = [
                        'label' => $moduleName,
                        'children' => []
                    ];
                    
                    // Panels au niveau module
                    if (isset($moduleData['panels'])) {
                        foreach ($moduleData['panels'] as $panel) {
                            $panelId = $panel['id'];
                            $panelName = $panel['description'] ?? $panel['id'];
                            $isRestricted = $panel['restricted'] ?? false;
                            $status = $isRestricted ? '🔒' : '🌐';
                            
                            $modulesTree[$namespace]['children'][$moduleName]['children'][$panelId] = [
                                'label' => $panelName . ' ' . $status,
                                'value' => $panelId,
                                'restricted' => $isRestricted
                            ];
                        }
                    }
                    
                    // Panels dans les sous-modules
                    if (isset($moduleData['submodules'])) {
                        foreach ($moduleData['submodules'] as $submoduleName => $submoduleData) {
                            $modulesTree[$namespace]['children'][$moduleName]['children'][$submoduleName] = [
                                'label' => $submoduleName,
                                'children' => []
                            ];
                            
                            if (isset($submoduleData['panels'])) {
                                foreach ($submoduleData['panels'] as $panel) {
                                    $panelId = $panel['id'];
                                    $panelName = $panel['description'] ?? $panel['id'];
                                    $isRestricted = $panel['restricted'] ?? false;
                                    $status = $isRestricted ? '🔒' : '🌐';
                                    
                                    $modulesTree[$namespace]['children'][$moduleName]['children'][$submoduleName]['children'][$panelId] = [
                                        'label' => $panelName . ' ' . $status,
                                        'value' => $panelId,
                                        'restricted' => $isRestricted
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            
            return $modulesTree;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une structure basique
            return [
                'Webkernel' => [
                    'label' => 'Webkernel',
                    'children' => [
                        'Core' => [
                            'label' => 'Core',
                            'children' => [
                                'system' => [
                                    'label' => 'System Module 🔒',
                                    'value' => 'system',
                                    'restricted' => true
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
    }

    public static function getUserModulesAction(): Action
    {
        return Action::make('manage_modules')
            ->label('Modules')
            ->icon('heroicon-o-rectangle-stack')
            ->modalHeading('Gérer les modules de l\'utilisateur')
            ->modalDescription('Sélectionnez les modules auxquels cet utilisateur a accès.')
            ->form([
                Select::make('modules')
                    ->label('Modules disponibles')
                    ->options(static::getModulesOptions())
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->default(function (User $record) {
                        return static::getUserModules($record);
                    })
                    ->helperText('Sélectionnez les modules auxquels cet utilisateur aura accès. Les modules publics (🌐) sont accessibles par défaut.')
                    ->columnSpanFull(),
            ])
            ->action(function (User $record, array $data): void {
                try {
                    // Convertir la liste des modules en format JSON
                    $modulesArray = [];
                    if (isset($data['modules']) && is_array($data['modules'])) {
                        foreach ($data['modules'] as $moduleId) {
                            $modulesArray[$moduleId] = 'access';
                        }
                    }
                    
                    // Sauvegarder ou mettre à jour les modules utilisateur
                    UserPanels::updateOrCreate(
                        ['user_id' => $record->id],
                        ['panels' => $modulesArray]
                    );
                } catch (\Exception $e) {
                    // Gérer l'erreur silencieusement
                }
            })
            ->modalSubmitActionLabel('Sauvegarder')
            ->modalCancelActionLabel('Annuler');
    }

    public static function getModulesOptions(): array
    {
        $modulesTree = static::getAvailableModulesTree();
        $options = [];
        
        foreach ($modulesTree as $namespace => $namespaceData) {
            foreach ($namespaceData['children'] as $moduleName => $moduleData) {
                // Parcourir récursivement tous les enfants
                self::extractPanelOptions($moduleData['children'], $options);
            }
        }
        
        return $options;
    }

    private static function extractPanelOptions(array $children, array &$options): void
    {
        foreach ($children as $key => $child) {
            if (isset($child['value'])) {
                // C'est un panneau
                $options[$child['value']] = $child['label'];
            } elseif (isset($child['children'])) {
                // C'est un sous-module, continuer récursivement
                self::extractPanelOptions($child['children'], $options);
            }
        }
    }

    public static function getUserModules(User $user): array
    {
        $userPanels = UserPanels::where('user_id', $user->id)->first();
        if (!$userPanels || !$userPanels->panels) {
            return [];
        }
        
        // Retourner seulement les clés (IDs des modules) pour le multiselect
        return array_keys($userPanels->panels);
    }

    public static function getPlatformOwnersAction(): Action
    {
        return Action::make('manage_platform_owners')
            ->label('Super Admins')
            ->icon('heroicon-o-shield-check')
            ->modalHeading('Gérer les super-admins')
            ->modalDescription('Gérer les utilisateurs qui ont accès à tous les modules.')
            ->form([
                \Filament\Forms\Components\Repeater::make('platform_owners')
                    ->label('Super Admins')
                    ->schema([
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Utilisateur')
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        
                        \Filament\Forms\Components\Toggle::make('is_eternal_owner')
                            ->label('Super Admin')
                            ->helperText('Accès à tous les modules')
                            ->default(true),
                        
                        \Filament\Forms\Components\DateTimePicker::make('when')
                            ->label('Date de début')
                            ->hidden(fn ($get) => $get('is_eternal_owner')),
                        
                        \Filament\Forms\Components\DateTimePicker::make('until')
                            ->label('Date de fin')
                            ->hidden(fn ($get) => $get('is_eternal_owner')),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Ajouter un super admin')
                    ->reorderable(false)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                try {
                    // Supprimer tous les platform owners existants
                    \Webkernel\Core\Models\RBAC\PlatformOwner::truncate();
                    
                    // Ajouter les nouveaux
                    if (isset($data['platform_owners'])) {
                        foreach ($data['platform_owners'] as $owner) {
                            if (isset($owner['user_id'])) {
                                \Webkernel\Core\Models\RBAC\PlatformOwner::create([
                                    'user_id' => $owner['user_id'],
                                    'panel_id' => 'all', // Pour super admin
                                    'is_eternal_owner' => $owner['is_eternal_owner'] ?? true,
                                    'when' => $owner['when'] ?? null,
                                    'until' => $owner['until'] ?? null,
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Gérer l'erreur silencieusement
                }
            })
            ->modalSubmitActionLabel('Sauvegarder')
            ->modalCancelActionLabel('Annuler');
    }
}
