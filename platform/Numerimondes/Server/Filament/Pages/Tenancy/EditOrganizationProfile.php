<?php namespace Platform\Numerimondes\Server\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditOrganizationProfile extends EditTenantProfile
{
  public static function getLabel(): string
  {
    return 'Organization Settings';
  }

  public function form(Schema $schema): Schema
  {
    return $schema->components([
      TextInput::make('name')->required()->maxLength(255),

      TextInput::make('slug')->required()->alphaDash()->maxLength(255),

      FileUpload::make('avatar_url')->image()->disk('public')->directory('organizations'),
    ]);
  }
}
