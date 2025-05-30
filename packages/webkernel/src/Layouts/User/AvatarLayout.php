<?php

namespace Webkernel\Layouts\User;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class AvatarLayout
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // PARTIE 1 - Informations principales avec avatar
                Forms\Components\Section::make('Profil utilisateur')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                // COL 1 - Avatar
                                Forms\Components\FileUpload::make('avatar')
                                    ->label('Photo de profil')
                                    ->image()
                                    ->avatar()
                                    ->disk('public')
                                    ->directory('avatars')
                                    ->visibility('public')
                                    ->columnSpan(1),

                                // COL 2 - Nom complet et titre
                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nom complet')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('title')
                                            ->label('Titre ou Profession')
                                            ->maxLength(255),
                                    ])
                                    ->columnSpan(1),

                                // COL 3 - Informations de contact
                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Forms\Components\DatePicker::make('date_of_birth')
                                            ->label('Date de naissance'),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('Téléphone (Principal)')
                                            ->tel()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ]),

                // PARTIE 2 - Onglets d'informations
                Forms\Components\Tabs::make('Informations')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations utilisateur')
                            ->schema([
                                Forms\Components\TextInput::make('username')
                                    ->label('Nom d\'utilisateur')
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('bio')
                                    ->label('Biographie')
                                    ->rows(3),

                                Forms\Components\TextInput::make('location')
                                    ->label('Localisation')
                                    ->maxLength(255),

                                Forms\Components\Select::make('gender')
                                    ->label('Genre')
                                    ->options([
                                        'male' => 'Masculin',
                                        'female' => 'Féminin',
                                        'other' => 'Autre',
                                        'prefer_not_to_say' => 'Préfère ne pas le dire',
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Informations de compte')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Statut du compte')
                                    ->options([
                                        'active' => 'Actif',
                                        'inactive' => 'Inactif',
                                        'suspended' => 'Suspendu',
                                        'pending' => 'En attente',
                                    ])
                                    ->default('active'),

                                Forms\Components\DateTimePicker::make('last_login_at')
                                    ->label('Dernière connexion')
                                    ->disabled(),

                                Forms\Components\TextInput::make('login_count')
                                    ->label('Nombre de connexions')
                                    ->numeric()
                                    ->disabled(),

                                Forms\Components\Toggle::make('email_verified')
                                    ->label('Email vérifié'),

                                Forms\Components\Toggle::make('two_factor_enabled')
                                    ->label('Authentification à deux facteurs'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Notes')
                            ->schema([
                                Forms\Components\Textarea::make('admin_notes')
                                    ->label('Notes administratives')
                                    ->rows(4)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('public_notes')
                                    ->label('Notes publiques')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                // PARTIE 3 - Onglets de services et pièces jointes
                Forms\Components\Tabs::make('Services et Documents')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Services associés')
                            ->schema([
                                Forms\Components\CheckboxList::make('services')
                                    ->label('Services actifs')
                                    ->options([
                                        'support' => 'Support technique',
                                        'premium' => 'Compte premium',
                                        'newsletter' => 'Newsletter',
                                        'notifications' => 'Notifications push',
                                        'api_access' => 'Accès API',
                                    ])
                                    ->columns(2),

                                Forms\Components\DatePicker::make('subscription_start')
                                    ->label('Début d\'abonnement'),

                                Forms\Components\DatePicker::make('subscription_end')
                                    ->label('Fin d\'abonnement'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Pièces jointes')
                            ->schema([
                                Forms\Components\FileUpload::make('documents')
                                    ->label('Documents')
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('user-documents')
                                    ->acceptedFileTypes(['pdf', 'doc', 'docx', 'txt'])
                                    ->maxSize(10240), // 10MB

                                Forms\Components\FileUpload::make('certificates')
                                    ->label('Certificats')
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('user-certificates')
                                    ->acceptedFileTypes(['pdf', 'jpg', 'png'])
                                    ->maxSize(5120), // 5MB
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(50),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom complet')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Téléphone')
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'suspended',
                        'secondary' => 'inactive',
                    ]),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'suspended' => 'Suspendu',
                        'pending' => 'En attente',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Webkernel\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \Webkernel\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \Webkernel\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
