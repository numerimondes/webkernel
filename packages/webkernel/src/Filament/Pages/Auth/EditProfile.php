<?php

namespace Webkernel\Filament\Pages\Auth;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Webkernel\Models\Language;
use Webkernel\Models\Session;
use App\Models\User;
use DateTimeZone;
use Filament\Tables\Concerns\InteractsWithTable;

class EditProfile extends BaseEditProfile implements HasForms, HasTable
{
    use InteractsWithTable;

    protected static ?string $model = User::class;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Contextual Help Section
                Section::make('need_help')
                    ->description('need_help_description')
                    ->schema([
                        Placeholder::make('help_info')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-start space-x-3">
                                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">profile_help_title</h4>
                                            <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">profile_help_content</p>
                                        </div>
                                    </div>
                                </div>
                            '))
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->icon('heroicon-o-question-mark-circle')
                    ->columnSpanFull(),

                // Personal Information Section
                Section::make('personal_information')
                    ->description('personal_information_description')
                    ->aside()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                FileUpload::make('avatar')
                                    ->label('profile_photo')
                                    ->image()
                                    ->avatar()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->directory('avatars')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(2048)
                                    ->helperText('avatar_helper')
                                    ->hint('drag_drop_hint')
                                    ->columnSpanFull(),

                                TextInput::make('name')
                                    ->label('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(['regex:/^[a-zA-ZÀ-ÿ\s\-\'\.]+$/u'])
                                    ->columnSpan(2)
                                    ->extraAttributes(['class' => 'font-medium']),

                                TextInput::make('username')
                                    ->label('username')
                                    ->unique(ignoreRecord: true)
                                    ->alphaDash()
                                    ->minLength(3)
                                    ->maxLength(50)
                                    ->rules(['regex:/^[a-zA-Z0-9_-]+$/'])
                                    ->helperText('username_helper')
                                    ->columnSpan(1)
                                    ->suffixIcon('heroicon-o-at-symbol'),
                            ]),
                    ])
                    ->icon('heroicon-o-user-circle'),

                // Email & Verification Section
                Section::make('email_verification')
                    ->description('email_verification_description')
                    ->aside()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                $this->getEmailFormComponent()
                                    ->disabled()
                                    ->suffixIcon('heroicon-o-envelope')
                                    ->columnSpan(1),

                                Placeholder::make('email_status')
                                    ->label('email_status')
                                    ->content(function () {
                                        $user = auth()->user();
                                        return $user->email_verified_at
                                            ? 'verified_on ' . $user->email_verified_at->format('M d, Y')
                                            : 'not_verified';
                                    })
                                    ->columnSpan(1),
                            ]),

                        \Filament\Forms\Components\Actions::make([
                            Action::make('change_email')
                                ->label('request_email_change')
                                ->icon('heroicon-o-envelope')
                                ->color('gray')
                                ->size(ActionSize::Small)
                                ->requiresConfirmation()
                                ->form([
                                    TextInput::make('new_email')
                                        ->label('new_email')
                                        ->email()
                                        ->required()
                                        ->unique('users', 'email', ignoreRecord: true),

                                    TextInput::make('password')
                                        ->label('current_password')
                                        ->password()
                                        ->required()
                                        ->rules(['current_password']),
                                ])
                                ->action(function (array $data) {
                                    Notification::make()
                                        ->title('email_change_requested')
                                        ->body('email_change_instructions')
                                        ->info()
                                        ->send();
                                }),

                            Action::make('resend_verification')
                                ->label('resend_verification')
                                ->icon('heroicon-o-check-circle')
                                ->color('primary')
                                ->size(ActionSize::Small)
                                ->visible(fn () => !auth()->user()->email_verified_at)
                                ->requiresConfirmation()
                                ->action(function () {
                                    auth()->user()->sendEmailVerificationNotification();
                                    Notification::make()
                                        ->title('verification_email_sent')
                                        ->success()
                                        ->send();
                                }),
                        ])->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-at-symbol'),

                // Password & Security Section
                Section::make('password_security')
                    ->description('password_security_description')
                    ->aside()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('current_password')
                                    ->label('current_password')
                                    ->password()
                                    ->revealable()
                                    ->rules(['current_password'])
                                    ->helperText('current_password_helper')
                                    ->suffixIcon('heroicon-o-lock-closed')
                                    ->dehydrated(false),

                                Grid::make(2)
                                    ->schema([
                                        $this->getPasswordFormComponent()
                                            ->revealable()
                                            ->rules([
                                                Password::min(8)
                                                    ->letters()
                                                    ->mixedCase()
                                                    ->numbers()
                                                    ->symbols()
                                                    ->uncompromised()
                                            ])
                                            ->helperText('password_requirements')
                                            ->columnSpan(1),

                                        $this->getPasswordConfirmationFormComponent()
                                            ->revealable()
                                            ->columnSpan(1),
                                    ]),
                            ]),
                    ])
                    ->icon('heroicon-o-shield-check'),

                // Personal Preferences Section
                Section::make('personal_preferences')
                    ->description('personal_preferences_description')
                    ->aside()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('timezone')
                                    ->label('timezone')
                                    ->options($this->getTimezoneOptions())
                                    ->default(auth()->user()?->timezone ?? config('app.timezone'))
                                    ->required()
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search) => $this->searchTimezones($search))
                                    ->columnSpan(1)
                                    ->suffixIcon('heroicon-o-globe-alt'),

                                Select::make('user_lang')
                                    ->label('language')
                                    ->options($this->getAvailableLanguages())
                                    ->default(app()->getLocale())
                                    ->required()
                                    ->columnSpan(1)
                                    ->suffixIcon('heroicon-o-language'),
                            ]),
                    ])
                    ->icon('heroicon-o-cog-6-tooth'),

                // Contact Information Section
                Section::make('contact_information')
                    ->description('contact_information_description')
                    ->aside()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('mobile')
                                    ->label('mobile_phone')
                                    ->tel()
                                    ->nullable()
                                    ->mask('+999 99 999 99 99')
                                    ->placeholder('+212 6 12 34 56 78')
                                    ->helperText('international_format')
                                    ->rules(['regex:/^\+?[1-9]\d{1,14}$/'])
                                    ->columnSpan(1)
                                    ->suffixIcon('heroicon-o-device-phone-mobile'),

                                TextInput::make('whatsapp')
                                    ->label('whatsapp_number')
                                    ->tel()
                                    ->nullable()
                                    ->mask('+999 99 999 99 99')
                                    ->placeholder('+212 6 12 34 56 78')
                                    ->helperText('whatsapp_helper')
                                    ->rules(['regex:/^\+?[1-9]\d{1,14}$/'])
                                    ->columnSpan(1)
                                    ->suffixIcon('heroicon-o-chat-bubble-left-right'),
                            ]),
                    ])
                    ->icon('heroicon-o-phone'),

                // Communication Preferences Section
                Section::make('communication_preferences')
                    ->description('communication_preferences_description')
                    ->aside()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Toggle::make('marketing_callable')
                                    ->label('phone_call_notifications')
                                    ->helperText('phone_call_helper')
                                    ->default(true)
                                    ->inline(false),

                                Toggle::make('marketing_whatsappable')
                                    ->label('whatsapp_messages')
                                    ->helperText('whatsapp_messages_helper')
                                    ->default(true)
                                    ->inline(false),

                                Toggle::make('marketing_smsable')
                                    ->label('sms_notifications')
                                    ->helperText('sms_helper')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ])
                    ->icon('heroicon-o-bell'),

                // GDPR & Data Portability Section
                Section::make('gdpr_data_portability')
                    ->description('gdpr_description')
                    ->aside()
                    ->schema([
                        \Filament\Forms\Components\Actions::make([
                            Action::make('export_data')
                                ->label('export_my_data')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('info')
                                ->requiresConfirmation()
                                ->action(function () {
                                    $userData = $this->exportUserData();
                                    Notification::make()
                                        ->title('data_export_initiated')
                                        ->body('data_export_email_sent')
                                        ->success()
                                        ->send();
                                }),

                            Action::make('request_data_deletion')
                                ->label('request_data_deletion')
                                ->icon('heroicon-o-trash')
                                ->color('warning')
                                ->requiresConfirmation()
                                ->form([
                                    TextInput::make('password')
                                        ->label('current_password')
                                        ->password()
                                        ->required()
                                        ->rules(['current_password']),

                                    TextInput::make('confirmation')
                                        ->label('type_delete_to_confirm')
                                        ->required()
                                        ->rules(['in:DELETE']),
                                ])
                                ->action(function (array $data) {
                                    Notification::make()
                                        ->title('deletion_request_submitted')
                                        ->body('deletion_request_processed')
                                        ->warning()
                                        ->send();
                                }),
                        ])->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-shield-exclamation'),

                // Advanced Settings Section
                Section::make('advanced_settings')
                    ->description('advanced_settings_description')
                    ->aside()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('account_active')
                                    ->helperText('account_active_helper')
                                    ->default(true)
                                    ->columnSpan(1),

                                Toggle::make('forceChangePassword')
                                    ->label('require_password_change')
                                    ->helperText('password_change_helper')
                                    ->default(false)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true)
                    ->icon('heroicon-o-wrench-screwdriver'),

                // Danger Zone Section
                Section::make('danger_zone')
                    ->description('danger_zone_description')
                    ->aside()
                    ->schema([
                        \Filament\Forms\Components\Actions::make([
                            Action::make('logout_other_sessions')
                                ->label('logout_other_sessions')
                                ->icon('heroicon-o-computer-desktop')
                                ->color('warning')
                                ->requiresConfirmation()
                                ->form([
                                    TextInput::make('password')
                                        ->label('current_password')
                                        ->password()
                                        ->required()
                                        ->rules(['current_password']),
                                ])
                                ->action(function (array $data) {
                                    $this->logoutOtherBrowserSessions($data['password']);
                                    Notification::make()
                                        ->title('other_sessions_logged_out')
                                        ->success()
                                        ->send();
                                }),

                            Action::make('deactivate_account')
                                ->label('deactivate_account')
                                ->icon('heroicon-o-no-symbol')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->form([
                                    TextInput::make('password')
                                        ->label('current_password')
                                        ->password()
                                        ->required()
                                        ->rules(['current_password']),
                                ])
                                ->action(function (array $data) {
                                    auth()->user()->update(['is_active' => false]);
                                    Auth::logout();
                                    Notification::make()
                                        ->title('account_deactivated')
                                        ->warning()
                                        ->send();
                                }),
                        ])->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-exclamation-triangle'),

                // Account Metadata Section
                Section::make('account_metadata')
                    ->description('account_metadata_description')
                    ->aside()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('account_created')
                                    ->label('account_created')
                                    ->content(fn () => auth()->user()->created_at?->format('M d, Y H:i')),

                                Placeholder::make('last_login')
                                    ->label('last_login')
                                    ->content(fn () => auth()->user()->last_login_at?->format('M d, Y H:i') ?? 'never'),

                                Placeholder::make('account_id')
                                    ->label('account_id')
                                    ->content(fn () => '#' . auth()->user()->id),

                                TextInput::make('belongs_to')
                                    ->label('organization_id')
                                    ->nullable()
                                    ->numeric()
                                    ->disabled(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true)
                    ->icon('heroicon-o-information-circle'),
            ])
            ->model(auth()->user())
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('ip_address')
                    ->label('ip_address')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user_agent')
                    ->label('browser')
                    ->formatStateUsing(fn (string $state): string => $this->parseUserAgent($state))
                    ->limit(50),

                TextColumn::make('last_activity')
                    ->label('last_activity')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                BadgeColumn::make('is_current')
                    ->label('status')
                    ->getStateUsing(fn ($record) => $record->id === session()->getId())
                    ->formatStateUsing(fn (bool $state): string => $state ? 'current_session' : 'other_session')
                    ->colors([
                        'success' => true,
                        'secondary' => false,
                    ]),

                TextColumn::make('location')
                    ->label('location')
                    ->getStateUsing(fn ($record) => $this->getLocationFromIP($record->ip_address))
                    ->placeholder('unknown_location'),
            ])
            ->filters([])
            ->actions([
                \Filament\Tables\Actions\Action::make('terminate')
                    ->label('terminate')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->id !== session()->getId())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::table('sessions')->where('id', $record->id)->delete();
                        Cache::forget('user_sessions_' . auth()->id());
                        Notification::make()
                            ->title('session_terminated')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkAction::make('terminate_selected')
                    ->label('terminate_selected')
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
                            ->title('sessions_terminated')
                            ->success()
                            ->send();
                    }),
            ])
            ->heading('active_sessions')
            ->description('active_sessions_description')
            ->emptyStateHeading('no_active_sessions')
            ->emptyStateDescription('no_sessions_description')
            ->emptyStateIcon('heroicon-o-device-phone-mobile')
            ->poll('30s');
    }

    protected function getTableQuery(): Builder
    {
        return Session::query()
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc');
    }

    protected function exportUserData(): array
    {
        $user = auth()->user();
        return [
            'profile' => $user->toArray(),
            'sessions' => Session::where('user_id', $user->id)->get()->toArray(),
            'exported_at' => now()->toISOString(),
        ];
    }

    protected function getLocationFromIP(string $ip): string
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'localhost';
        }
        return 'unknown_location';
    }

    protected function parseUserAgent(string $userAgent): string
    {
        if (Str::contains($userAgent, 'Chrome')) {
            return 'Chrome Browser';
        } elseif (Str::contains($userAgent, 'Firefox')) {
            return 'Firefox Browser';
        } elseif (Str::contains($userAgent, 'Safari')) {
            return 'Safari Browser';
        } elseif (Str::contains($userAgent, 'Edge')) {
            return 'Edge Browser';
        }
        return 'Unknown Browser';
    }

    protected function logoutOtherBrowserSessions(string $password): void
    {
        if (!Hash::check($password, auth()->user()->getAuthPassword())) {
            throw new \Exception('invalid_password');
        }

        DB::table('sessions')
            ->where('user_id', auth()->id())
            ->where('id', '!=', session()->getId())
            ->delete();

        Cache::forget('user_sessions_' . auth()->id());
    }

    public function getTimezoneOptions(): array
    {
        return Cache::remember('timezone_options', now()->addDays(7), function () {
            $timezones = DateTimeZone::listIdentifiers();
            return collect($timezones)->mapWithKeys(function ($timezone) {
                $offset = now()->setTimezone($timezone)->format('P');
                return [$timezone => "($offset) $timezone"];
            })->toArray();
        });
    }

    protected function searchTimezones(string $search): array
    {
        $timezones = DateTimeZone:: frivolity;
        return collect($timezones)
            ->filter(fn ($tz) => str_contains(strtolower($tz), strtolower($search)))
            ->mapWithKeys(function ($timezone) {
                $offset = now()->setTimezone($timezone)->format('P');
                return [$timezone => "($offset) $timezone"];
            })
            ->take(20)
            ->toArray();
    }

    protected function getAvailableLanguages(): array
    {
        return Cache::remember('available_languages', now()->addHour(), function () {
            return Language::where('is_active', true)
                ->pluck('label', 'code')
                ->toArray();
        });
    }

    public static function getLabel(): string
    {
        return 'my_profile';
    }

    public function getTitle(): string
    {
        return 'profile_settings';
    }

    public function getSubheading(): ?string
    {
        return 'profile_settings_description';
    }

    public static function getRelations(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('profile_updated')
            ->body('profile_updated_successfully');
    }

    protected function getRedirectUrl(): string
    {
        return static::getUrl();
    }

    protected function beforeSave(): void
    {
        Cache::forget('user_sessions_' . auth()->id());
        $this->record->last_profile_update_at = now();
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('user_lang')) {
            Cache::forget('available_languages');
        }
    }

}
