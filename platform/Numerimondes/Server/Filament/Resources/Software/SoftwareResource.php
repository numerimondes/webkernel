<?php
declare(strict_types=1);
namespace Platform\Numerimondes\Server\Filament\Resources\Software;

use Platform\Numerimondes\Server\Filament\Resources\Software\Pages\CreateSoftware;
use Platform\Numerimondes\Server\Filament\Resources\Software\Pages\EditSoftware;
use Platform\Numerimondes\Server\Filament\Resources\Software\Pages\ListSoftware;
use Platform\Numerimondes\Server\Filament\Resources\Software\Schemas\SoftwareForm;
use Platform\Numerimondes\Server\Filament\Resources\Software\Tables\SoftwareTable;
use Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers\CoreSoftwaresRelationManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Platform\Numerimondes\Server\Models\Software;

/**
 *
 * Query Builder Methods
 * @method static \Illuminate\Database\Eloquent\Builder<\latform\Numerimondes\Models\Software> query()
 * @method static \Illuminate\Database\Eloquent\Builder<\latform\Numerimondes\Models\Software> where(string|array|\Closure $column, mixed $operator = null, mixed $value = null)
 * @method static \latform\Numerimondes\Models\Software|null find(int|string $id, array $columns = "[\'*\']")
 * @method static \latform\Numerimondes\Models\Software findOrFail(int|string $id, array $columns = "[\'*\']")
 * @method static \latform\Numerimondes\Models\Software create(array $attributes = '[]')
 * @method static \latform\Numerimondes\Models\Software firstOrCreate(array $attributes, array $values = '[]')
 * @method static \latform\Numerimondes\Models\Software updateOrCreate(array $attributes, array $values = '[]')
 * @method static \Illuminate\Database\Eloquent\Collection<int, \latform\Numerimondes\Models\Software> all(array $columns = "[\'*\']")
 * @method static \latform\Numerimondes\Models\Software|null first()
 * @method static \latform\Numerimondes\Models\Software firstOrFail()
 */
class SoftwareResource extends Resource
{
  protected static ?string $model = Software::class;

  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

  protected static ?string $recordTitleAttribute = 'name';

  /**
   * Conditional tenant scoping
   *
   * Only scope to tenant on 'numerimondes' panel
   *
   * @return Builder<Software>
   */
  public static function getEloquentQuery(): Builder
  {
    $query = parent::getEloquentQuery();

    $panelManager = Filament::getFacadeRoot();

    if (!method_exists($panelManager, 'getCurrentPanel')) {
      return $query;
    }

    $panel = $panelManager->getCurrentPanel();

    if (!$panel) {
      return $query;
    }

    if ($panel->getId() === 'numerimondes') {
      if (!method_exists($panelManager, 'getTenant')) {
        return $query;
      }

      $tenant = $panelManager->getTenant();

      if ($tenant && method_exists($tenant, 'getKey')) {
        $query->where('organization_id', $tenant->getKey());
      }
    }

    return $query;
  }

  public static function form(Schema $schema): Schema
  {
    return SoftwareForm::configure($schema);
  }

  public static function table(Table $table): Table
  {
    return SoftwareTable::configure($table);
  }

  public static function getRelations(): array
  {
    return [CoreSoftwaresRelationManager::class];
  }

  public static function getPages(): array
  {
    return [
      'index' => ListSoftware::route('/'),
      'create' => CreateSoftware::route('/create'),
      'edit' => EditSoftware::route('/{record}/edit'),
    ];
  }
}
