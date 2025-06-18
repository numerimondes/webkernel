<?php

namespace Webkernel\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Webkernel\Models\User;
use Livewire\Attributes\Layout;
use DateTimeZone;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    use InteractsWithForms;

    protected static ?string $model = User::class;

    protected static bool $shouldRegisterNavigation = false;

    public function form(Form $form): Form
    {
        return $form->schema([

            Section::make(__('Need Help?'))
                ->schema([
                    View::make('webkernel::components.webkernel.rolebased.common.profile-header')
                ])
                ->collapsible()
                ->collapsed(false),

            Grid::make(1)
                ->schema([
                    Tabs::make('Profile')
                        ->tabs([
                            // Tab for Personal Information
                            Tab::make(__('Personal Information'))
                                ->schema([
                                    $this->getNameFormComponent(),
                                    $this->getEmailFormComponent()->disabled(),
                                    $this->getPasswordFormComponent(),
                                    $this->getPasswordConfirmationFormComponent(),
                                    // Ajout du champ Timezone
                                    Select::make('timezone')
                                        ->label(__('Timezone'))
                                        ->options($this->getTimezoneOptions())
                                        ->default(auth()->user()?->timezone)
                                        ->required(),
                                ]),

                            // Tab for Contact Information
                            Tab::make(__('Contact Information'))
                                ->schema([
                                    TextInput::make('mobile')
                                        ->label(__('Mobile'))
                                        ->nullable()
                                        ->tel()
                                        ->maxLength(15)
                                        ->helperText(__('Optional: Enter your mobile number. Max length 15 characters.')),

                                    TextInput::make('whatsapp')
                                        ->label(__('WhatsApp'))
                                        ->nullable()
                                        ->tel()
                                        ->maxLength(15)
                                        ->helperText(__('Optional: Enter your WhatsApp number. Max length 15 characters.')),
                                ]),

                            // Tab for Settings
                            Tab::make(__('Settings'))
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label(__('Active'))
                                        ->default(true)
                                        ->helperText(__('If inactive, the user will not be able to access their profile or related actions.')),

                                    Toggle::make('forceChangePassword')
                                        ->label(__('Force Password Change'))
                                        ->default(false)
                                        ->helperText(__('If enabled, the user will be forced to change their password upon next login.')),

                                    Toggle::make('force_password_override')
                                        ->label(__('Manually set a password'))
                                        ->reactive()
                                        ->helperText(__('Enable this to manually set the password for this user.')),

                                    Toggle::make('is_banned')
                                        ->label(__('Banned'))
                                        ->default(false)
                                        ->helperText(__('If enabled, the user will be banned and unable to login.')),
                                ]),

                            // Tab for Marketing Consent
                            Tab::make(__('Marketing Consent'))
                                ->schema([
                                    Toggle::make('marketing_callable')
                                        ->label(__('Consent to receive phone calls'))
                                        ->default(true),

                                    Toggle::make('marketing_whatsappable')
                                        ->label(__('Consent to receive WhatsApp messages'))
                                        ->default(true),

                                    Toggle::make('marketing_smsable')
                                        ->label(__('Consent to receive SMS messages'))
                                        ->default(true),
                                ]),

                            // Tab for User Info and Creation Details
                            Tab::make(__('Meta Data'))
                                ->schema([
                                    TextInput::make('created_by')
                                        ->label(__('Created By'))
                                        ->disabled()
                                        ->default(auth()->id()),

                                    TextInput::make('belongs_to')
                                        ->label(__('Belongs To'))
                                        ->nullable()
                                        ->numeric(),

                                    TextInput::make('email_verified_at')
                                        ->label(__('Email Verified At'))
                                        ->disabled()
                                        ->helperText(__('The date and time when the email was verified.')),
                                ]),
                        ])
                ]),

        ])->extraAttributes(['class' => 'md']); // Applique des classes Tailwind pour la largeur
    }

    public function getTimezoneOptions()
    {
        $timezones = DateTimeZone::listIdentifiers();

        // Retourne les fuseaux horaires sous forme d'un tableau clé/valeur
        return collect($timezones)->mapWithKeys(function ($timezone) {
            return [$timezone => $timezone];
        });
    }

    /**
     * The layout that the page should use.
     */
    protected static string $layout = 'filament-panels::components.layout.index';

    /**
     * Get the label for the page.
     */
    public static function getLabel(): string
    {
        return 'My Profile';
    }

    /**
     * Get the title for the page.
     */
    public function getTitle(): string
    {
        return '';
    }

    public static function getRelations(): array
    {
        return [
            // Add relations here if necessary
        ];
    }
}
