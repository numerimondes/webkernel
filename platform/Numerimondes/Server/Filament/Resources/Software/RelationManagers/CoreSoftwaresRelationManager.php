<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers\Form\CoreSoftwaresForm;
use Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers\Table\CoreSoftwaresTable;
use Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers\Actions\CoreSoftwaresActions;

class CoreSoftwaresRelationManager extends RelationManager
{
  protected static string $relationship = 'coreSoftwares';

  /**
   * Get the form schema for creating/editing core modules.
   *
   * @return array
   */
  public function getFormSchema(): array
  {
    $softwareName = $this->ownerRecord->name ?? 'Software';
    return CoreSoftwaresForm::getSchema($softwareName);
  }

  /**
   * Configure the relation table.
   *
   * @param Table $table
   * @return Table
   */
  public function table(Table $table): Table
  {
    $emptyState = CoreSoftwaresTable::getEmptyState();

    return $table
      ->recordTitleAttribute('name')
      ->columns(CoreSoftwaresTable::getColumns())
      ->headerActions(CoreSoftwaresActions::getHeaderActions(fn() => $this->getFormSchema(), $this->ownerRecord))
      ->actions(CoreSoftwaresActions::getRowActions(fn() => $this->getFormSchema(), $this->ownerRecord))
      ->emptyStateHeading($emptyState['heading'])
      ->emptyStateDescription($emptyState['description'])
      ->emptyStateIcon($emptyState['icon'])
      ->emptyStateActions(
        CoreSoftwaresActions::getEmptyStateActions(fn() => $this->getFormSchema(), $this->ownerRecord),
      );
  }
}
