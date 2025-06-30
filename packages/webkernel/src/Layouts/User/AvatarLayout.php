<?php

namespace Webkernel\Layouts\User;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
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
use Webkernel\Filament\Resources\UserResource\Pages\EditUser;
use Webkernel\Filament\Resources\UserResource\Pages\ListUsers;
use Webkernel\Filament\Resources\UserResource\Pages\CreateUser;

class AvatarLayout
{
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
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
