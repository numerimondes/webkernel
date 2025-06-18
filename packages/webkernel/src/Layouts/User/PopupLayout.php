<?php

namespace Webkernel\Layouts\User;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Webkernel\Filament\Resources\UserResource\Pages\ListUsers;
use Webkernel\Filament\Resources\UserResource\Pages\CreateUser;
use Webkernel\Filament\Resources\UserResource\Pages\EditUser;
use Webkernel\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PopupLayout extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?string $recordTitleAttribute = 'name';
    public static function getNavigationLabel(): string
    {
        return __('filament-panels::layout.actions.open_user_menu.label');
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make('User Tabs')
                                    ->tabs([
                                        Tab::make(lang('User Information'))
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->debounce(delay: 500)
                                                    ->afterStateUpdated(function ($set, $state) {
                                                        $set('username', strtolower(str_replace(' ', '', $state)));
                                                    }),

                                                TextInput::make('username')
                                                    ->required()
                                                    ->unique()
                                                    ->disabled()
                                                    ->dehydrated(),

                                                TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->unique(),

                                                TextInput::make('mobile')
                                                    ->nullable()
                                                    ->unique(),

                                                TextInput::make('whatsapp')
                                                    ->nullable()
                                                    ->unique(),

                                            ])
                                            ->columns(3),

                                        Tab::make(lang('User Status'))
                                            ->schema([
                                                Toggle::make('is_active')
                                                    ->default(true)
                                                    ->label(lang('Active')),

                                                Toggle::make('force_password_override')
                                                    ->label(lang('Manually set a password'))
                                                    ->reactive(),

                                                TextInput::make('password')
                                                    ->label(lang('New password'))
                                                    ->password()
                                                    ->revealable(true)
                                                    ->required(fn($get) => $get('force_password_override'))
                                                    ->visible(fn($get) => $get('force_password_override')),

                                                Toggle::make('is_banned')
                                                    ->default(false)
                                                    ->label(lang('Banned')),

                                                Toggle::make('forceChangePassword')
                                                    ->default(true)
                                                    ->label(lang('Force Password Change'))
                                                    ->helperText(lang('Cette action forcera le changement de mot de passe de l\'utilisateur lors de la prochaine connexion')),
                                            ])
                                            ->columns(3),

                                        Tab::make(lang('Marketing & Subscription Settings'))
                                            ->schema([
                                                Toggle::make('marketing_callable')
                                                    ->default(true)
                                                    ->label(lang('Consent to receive phone calls')),

                                                Toggle::make('marketing_whatsappable')
                                                    ->default(true)
                                                    ->label(lang('Consent to receive WhatsApp messages')),

                                                Toggle::make('marketing_smsable')
                                                    ->default(true)
                                                    ->label(lang('Consent to receive SMS messages')),

                                            ])
                                            ->columns(2),
                                    ])
                                    ->contained(true)
                            ])
                            ->columnSpan(['sm' => 2]),

                        // Meta Data Section
                        Group::make()
                            ->schema([
                                Section::make(lang('Meta Data'))
                                    ->schema([
                                        TextInput::make('belongs_to')
                                            ->required()
                                            ->numeric()
                                            ->default(1),

                                        Placeholder::make('created_at')
                                            ->label(lang('Created At'))
                                            ->content(fn(?User $record) => $record?->created_at?->diffForHumans()),

                                        Placeholder::make('updated_at')
                                            ->label(lang('Updated At'))
                                            ->content(fn(?User $record) => $record?->updated_at?->diffForHumans()),

                                        Placeholder::make('email_verified_at')
                                            ->label(lang('Email Verified At'))
                                            ->content(fn(?User $record) => $record?->email_verified_at?->diffForHumans()),

                                        Placeholder::make('created_by')
                                            ->label(lang('Created By'))
                                            ->content(
                                                fn($record) =>
                                                $record ? $record->createdBy?->name : (auth()->user()->name ?? lang('Unknown'))
                                            ),
                                    ])
                                    ->columns(1),
                            ])
                            ->columnSpan(['sm' => 1]),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('mobile')->searchable(),
                TextColumn::make('whatsapp')->searchable(),
                TextColumn::make('email_verified_at')->dateTime()->sortable(),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('is_banned')->boolean(),
                TextColumn::make('created_by')->numeric()->sortable(),
                TextColumn::make('belongs_to')->numeric()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relations here if necessary
        ];
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
