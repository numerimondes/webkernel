<?php

namespace Webkernel\Layouts\User;

use Webkernel\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PopupLayout extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $recordTitleAttribute = 'name';
    public static function getNavigationLabel(): string
    {
        return __('filament-panels::layout.actions.open_user_menu.label');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Tabs::make('User Tabs')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make(lang('User Information'))
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->debounce(delay: 500)
                                                    ->afterStateUpdated(function ($set, $state) {
                                                        $set('username', strtolower(str_replace(' ', '', $state)));
                                                    }),

                                                Forms\Components\TextInput::make('username')
                                                    ->required()
                                                    ->unique()
                                                    ->disabled()
                                                    ->dehydrated(),

                                                Forms\Components\TextInput::make('email')
                                                    ->email()
                                                    ->required()
                                                    ->unique(),

                                                Forms\Components\TextInput::make('mobile')
                                                    ->nullable()
                                                    ->unique(),

                                                Forms\Components\TextInput::make('whatsapp')
                                                    ->nullable()
                                                    ->unique(),

                                            ])
                                            ->columns(3),

                                        Forms\Components\Tabs\Tab::make(lang('User Status'))
                                            ->schema([
                                                Forms\Components\Toggle::make('is_active')
                                                    ->default(true)
                                                    ->label(lang('Active')),

                                                Forms\Components\Toggle::make('force_password_override')
                                                    ->label(lang('Manually set a password'))
                                                    ->reactive(),

                                                Forms\Components\TextInput::make('password')
                                                    ->label(lang('New password'))
                                                    ->password()
                                                    ->revealable(true)
                                                    ->required(fn($get) => $get('force_password_override'))
                                                    ->visible(fn($get) => $get('force_password_override')),

                                                Forms\Components\Toggle::make('is_banned')
                                                    ->default(false)
                                                    ->label(lang('Banned')),

                                                Forms\Components\Toggle::make('forceChangePassword')
                                                    ->default(true)
                                                    ->label(lang('Force Password Change'))
                                                    ->helperText(lang('Cette action forcera le changement de mot de passe de l\'utilisateur lors de la prochaine connexion')),
                                            ])
                                            ->columns(3),

                                        Forms\Components\Tabs\Tab::make(lang('Marketing & Subscription Settings'))
                                            ->schema([
                                                Forms\Components\Toggle::make('marketing_callable')
                                                    ->default(true)
                                                    ->label(lang('Consent to receive phone calls')),

                                                Forms\Components\Toggle::make('marketing_whatsappable')
                                                    ->default(true)
                                                    ->label(lang('Consent to receive WhatsApp messages')),

                                                Forms\Components\Toggle::make('marketing_smsable')
                                                    ->default(true)
                                                    ->label(lang('Consent to receive SMS messages')),

                                            ])
                                            ->columns(2),
                                    ])
                                    ->contained(true)
                            ])
                            ->columnSpan(['sm' => 2]),

                        // Meta Data Section
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(lang('Meta Data'))
                                    ->schema([
                                        Forms\Components\TextInput::make('belongs_to')
                                            ->required()
                                            ->numeric()
                                            ->default(1),

                                        Forms\Components\Placeholder::make('created_at')
                                            ->label(lang('Created At'))
                                            ->content(fn(?User $record) => $record?->created_at?->diffForHumans()),

                                        Forms\Components\Placeholder::make('updated_at')
                                            ->label(lang('Updated At'))
                                            ->content(fn(?User $record) => $record?->updated_at?->diffForHumans()),

                                        Forms\Components\Placeholder::make('email_verified_at')
                                            ->label(lang('Email Verified At'))
                                            ->content(fn(?User $record) => $record?->email_verified_at?->diffForHumans()),

                                        Forms\Components\Placeholder::make('created_by')
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
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('mobile')->searchable(),
                Tables\Columns\TextColumn::make('whatsapp')->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_banned')->boolean(),
                Tables\Columns\TextColumn::make('created_by')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('belongs_to')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),

        ];
    }
}
