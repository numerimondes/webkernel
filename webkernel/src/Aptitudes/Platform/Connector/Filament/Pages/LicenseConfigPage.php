<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\Platform\Connector\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Webkernel\Aptitudes\Platform\Connector\Services\SyncService;
use Webkernel\Aptitudes\Platform\Core\Models\LocalLicense;
use Webkernel\Aptitudes\Platform\Core\Services\EncryptionService;

class LicenseConfigPage extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-key';
  protected static string $view = 'filament.pages.license-config';

  public function mount(): void
  {
    $this->form->fill(LocalLicense::first()?->only(['domain']) ?? []);
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('token')
          ->label('License Token')
          ->password()
          ->required()
          ->maxLength(64)
          ->dehydrated(fn(?string $state) => !empty($state)),
        TextInput::make('domain')
          ->label('Domain')
          ->required()
          ->default(request()->getHost()),
      ])
      ->statePath('data');
  }

  public function save(): void
  {
    $data = $this->form->getState();
    $license = LocalLicense::firstOrCreate(['domain' => $data['domain']]);

    if (!empty($data['token'])) {
      $license->token_encrypted = new EncryptionService()->encrypt($data['token']);
      $license->status = 'pending';
      $license->save();
    }

    Notification::make()->success()->title('Saved.')->send();

    $this->redirect($this->getResource()::getUrl());
  }

  public function sync(): void
  {
    $license = LocalLicense::first();
    if (!$license) {
      Notification::make()->danger()->title('No license configured.')->send();
      return;
    }

    $result = app(SyncService::class)->sync($license);
    if ($result['success']) {
      Notification::make()->success()->title('Sync successful.')->send();
    } else {
      Notification::make()->danger()->title($result['error'])->send();
    }
  }
}
