<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Platform\Numerimondes\Server\Models\Organization;

class RegisterOrganization extends RegisterTenant
{
  /**
   * Get label
   *
   * @return string
   */
  public static function getLabel(): string
  {
    return 'Create Organization';
  }

  /**
   * Form schema
   *
   * @param Schema $schema
   * @return Schema
   */
  public function form(Schema $schema): Schema
  {
    return $schema->components([
      TextInput::make('name')->required()->maxLength(255)->label('Organization Name'),

      TextInput::make('slug')
        ->required()
        ->unique(Organization::class, 'slug')
        ->alphaDash()
        ->maxLength(255)
        ->label('URL Slug'),
    ]);
  }

  /**
   * Handle registration
   *
   * @param array<string, mixed> $data
   * @return Organization
   */
  protected function handleRegistration(array $data): Organization
  {
    $organization = Organization::query()->create($data);

    $organization->users()->attach(auth()->id(), [
      'role' => 'admin',
      'permissions' => json_encode(['*']),
    ]);

    return $organization;
  }
}
