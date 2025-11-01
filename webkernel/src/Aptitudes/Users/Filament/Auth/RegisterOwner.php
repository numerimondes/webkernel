<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\Users\Filament\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\Events\Registered;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;
use LogicException;

/**
 * @property-read Action $loginAction
 * @property-read Schema $form
 */
class RegisterOwner extends SimplePage
{
    use CanUseDatabaseTransactions;
    use WithRateLimiting;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    /**
     * @var class-string<Model>
     */
    protected string $userModel;

    public function getTitle(): string|Htmlable
    {
        return 'Superadmin Registration';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Create Superadmin Account';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (! filament()->hasLogin()) {
            return null;
        }

        return new HtmlString(lang('filament-panels::auth/pages/register.actions.login.before').' '.$this->loginAction->toHtml());
    }

    public function getSlug(): string
    {
        return 'register-'.\Illuminate\Support\Str::uuid()->toString();
    }

    public function mount(): void
    {
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(lang('filament-panels::auth/pages/register.form.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label(lang('filament-panels::auth/pages/register.form.email.label'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique($this->getUserModel()),
                TextInput::make('password')
                    ->label(lang('filament-panels::auth/pages/register.form.password.label'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->rule(Password::default())
                    ->showAllValidationMessages()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->validationAttribute(lang('filament-panels::auth/pages/register.form.password.validation_attribute')),
                $this->getSuperadminNoticeComponent(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE),
                $this->getFormContentComponent(),
                RenderHook::make(PanelsRenderHook::AUTH_REGISTER_FORM_AFTER),

            ]);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        try {
            $user = $this->wrapInDatabaseTransaction(function (): Model {
                $this->callHook('beforeValidate');

                $data = $this->form->getState();

                $this->callHook('afterValidate');

                $data = $this->mutateFormDataBeforeRegister($data);

                $this->callHook('beforeRegister');

                $user = $this->handleRegistration($data);

                $this->form->model($user)->saveRelationships();

                $this->callHook('afterRegister');

                return $user;
            });

            event(new Registered($user));

            $this->sendEmailVerificationNotification($user);

            Filament::auth()->login($user);
            session()->regenerate();

            return app(RegistrationResponse::class);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Registration Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label(lang('filament-panels::auth/pages/register.actions.login.label'))
            ->url(filament()->getLoginUrl());
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(lang('filament-panels::auth/pages/register.form.actions.register.label'))
            ->icon('heroicon-o-user-group')
            ->submit('register');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
        ];
    }

    protected function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('register')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->key('form-actions'),
            ]);
    }

    protected function getSuperadminNoticeComponent(): Component
    {
        return \Filament\Forms\Components\Placeholder::make('superadmin_notice')
            ->label('Superadmin Registration')
            ->content('This registration is for superadmin access only. Your email will be validated with numerimondes.com before account creation.')
            ->columnSpanFull();
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        if (! $this->validateSuperadminAuthorization($data['email'])) {
            throw new \Exception('Superadmin authorization failed. Please contact numerimondes.com for access.');
        }

        $user = $this->getUserModel()::create($data);

        $user->assignRole('super-admin');

        Log::info('Superadmin registered successfully', [
            'email' => $data['email'],
            'name' => $data['name'],
            'user_id' => $user->id,
        ]);

        return $user;
    }

    /**
     * Validate superadmin authorization with numerimondes.com server
     */
    protected function validateSuperadminAuthorization(string $email): bool
    {
        try {
            $response = Http::timeout(10)
                ->post('https://numerimondes.com/api/validate-superadmin', [
                    'email' => $email,
                    'domain' => request()->getHost(),
                    'timestamp' => now()->timestamp,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['authorized'] ?? false;
            }

            Log::warning('Failed to validate superadmin authorization', [
                'email' => $email,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Superadmin authorization validation failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function sendEmailVerificationNotification(Model $user): void
    {
        if (! $user instanceof MustVerifyEmail) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new LogicException("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = app(VerifyEmail::class);
        $notification->url = Filament::getVerifyEmailUrl($user);

        $user->notify($notification);
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(lang('filament-panels::auth/pages/register.notifications.throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', lang('filament-panels::auth/pages/register.notifications.throttled') ?: []) ? lang('filament-panels::auth/pages/register.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        return $data;
    }

    /**
     * @return class-string<Model>
     */
    protected function getUserModel(): string
    {
        if (isset($this->userModel)) {
            return $this->userModel;
        }

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        /** @var EloquentUserProvider $provider */
        $provider = $authGuard->getProvider();

        return $this->userModel = $provider->getModel();
    }
}
