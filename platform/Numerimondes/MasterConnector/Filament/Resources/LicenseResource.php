<?php

declare(strict_types=1);

namespace Platform\Numerimondes\MasterConnector\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Platform\Numerimondes\MasterConnector\Models\License;
use Platform\Numerimondes\MasterConnector\Services\LicenseManager;

class LicenseResource extends Resource
{
  protected static ?string $model = License::class;
  protected static ?string $navigationIcon = 'heroicon-o-key';
  protected static ?string $navigationLabel = 'Licenses';

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\Section::make('License Information')->schema([
        Forms\Components\TextInput::make('domain')
          ->required()
          ->maxLength(255)
          ->placeholder('example.com')
          ->helperText('The domain where this license will be used (exact match required).'),

        Forms\Components\Select::make('status')
          ->options([
            'active' => 'Active',
            'expired' => 'Expired',
            'revoked' => 'Revoked',
          ])
          ->default('active')
          ->required(),

        Forms\Components\DateTimePicker::make('expires_at')
          ->label('Expiration Date')
          ->nullable()
          ->helperText('Leave empty for perpetual license.'),

        Forms\Components\Select::make('organization_id')
          ->relationship('organization', 'name')
          ->searchable()
          ->nullable()
          ->helperText('Optional: Assign to an organization.'),
      ]),

      Forms\Components\Section::make('Authorized Modules')->schema([
        Forms\Components\CheckboxList::make('modules')
          ->relationship('modules', 'name')
          ->searchable()
          ->columns(2)
          ->helperText('Select modules this license can access.'),
      ]),

      Forms\Components\Section::make('Metadata')
        ->schema([
          Forms\Components\KeyValue::make('metadata')
            ->label('Additional Metadata')
            ->helperText('Optional key-value pairs (e.g., client name, plan type).'),
        ])
        ->collapsible(),
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),

        Tables\Columns\TextColumn::make('domain')->searchable()->sortable()->copyable(),

        Tables\Columns\BadgeColumn::make('status')->colors([
          'success' => 'active',
          'warning' => 'expired',
          'danger' => 'revoked',
        ]),

        Tables\Columns\TextColumn::make('modules_count')->counts('modules')->label('Modules'),

        Tables\Columns\TextColumn::make('expires_at')
          ->label('Expires')
          ->dateTime()
          ->sortable()
          ->placeholder('Perpetual'),

        Tables\Columns\TextColumn::make('last_validated_at')
          ->label('Last Validated')
          ->dateTime()
          ->sortable()
          ->since()
          ->placeholder('Never'),

        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('status')->options([
          'active' => 'Active',
          'expired' => 'Expired',
          'revoked' => 'Revoked',
        ]),

        Tables\Filters\Filter::make('expires_soon')
          ->query(fn($query) => $query->whereBetween('expires_at', [now(), now()->addDays(30)]))
          ->label('Expires in 30 days'),
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),

        Tables\Actions\Action::make('revoke')
          ->icon('heroicon-o-x-circle')
          ->color('danger')
          ->requiresConfirmation()
          ->action(function (License $record) {
            app(LicenseManager::class)->revokeLicense($record, 'Manually revoked via admin panel');
          })
          ->visible(fn(License $record) => $record->status === 'active'),

        Tables\Actions\Action::make('extend')
          ->icon('heroicon-o-calendar')
          ->form([
            Forms\Components\DateTimePicker::make('new_expires_at')
              ->label('New Expiration Date')
              ->required()
              ->minDate(now()),
          ])
          ->action(function (License $record, array $data) {
            app(LicenseManager::class)->extendLicense($record, new \DateTime($data['new_expires_at']));
          }),

        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
  }

  public static function getRelations(): array
  {
    return [
        // Add relation managers if needed (e.g., ModulesRelationManager)
      ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListLicenses::route('/'),
      'create' => Pages\CreateLicense::route('/create'),
      'view' => Pages\ViewLicense::route('/{record}'),
      'edit' => Pages\EditLicense::route('/{record}/edit'),
    ];
  }

  public static function getNavigationBadge(): ?string
  {
    return static::getModel()::active()->count();
  }
}

// Page Classes (create in Filament/Resources/LicenseResource/Pages/)

namespace Platform\Numerimondes\MasterConnector\Filament\Resources\LicenseResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Platform\Numerimondes\MasterConnector\Services\LicenseManager;

class CreateLicense extends CreateRecord
{
  protected static string $resource = \Platform\Numerimondes\MasterConnector\Filament\Resources\LicenseResource::class;

  protected function afterCreate(): void
  {
    // Generate token and show modal
    $manager = app(LicenseManager::class);
    $result = $manager->createLicense(
      domain: $this->record->domain,
      moduleIds: $this->record->modules->pluck('id')->toArray(),
      expiresAt: $this->record->expires_at,
      organizationId: $this->record->organization_id,
      metadata: $this->record->metadata ?? [],
    );

    // Display token (one-time only)
    Notification::make()
      ->title('License Created Successfully')
      ->body(
        new \Illuminate\Support\HtmlString(
          '<strong>IMPORTANT: Copy this token now. It will not be shown again.</strong><br>' .
            '<code style="user-select: all; background: #f3f4f6; padding: 8px; display: block; margin-top: 8px;">' .
            $result['token'] .
            '</code>',
        ),
      )
      ->success()
      ->persistent()
      ->send();
  }
}

class ListLicenses extends \Filament\Resources\Pages\ListRecords
{
  protected static string $resource = \Platform\Numerimondes\MasterConnector\Filament\Resources\LicenseResource::class;

  protected function getHeaderActions(): array
  {
    return [\Filament\Actions\CreateAction::make()];
  }
}

class ViewLicense extends \Filament\Resources\Pages\ViewRecord
{
  protected static string $resource = \Platform\Numerimondes\MasterConnector\Filament\Resources\LicenseResource::class;
}

class EditLicense extends \Filament\Resources\Pages\EditRecord
{
  protected static string $resource = \Platform\Numerimondes\MasterConnector\Filament\Resources\LicenseResource::class;
}
