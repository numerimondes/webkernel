<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Platform\EnjoyTheWorld\Filament\Resources\Providers\Schemas\ServiceForm;
use Platform\EnjoyTheWorld\Models\Service;
use Platform\EnjoyTheWorld\Filament\Resources\Services\Pages\{CreateService, EditService};

class ServicesRelationManager extends RelationManager
{
  protected static string $relationship = 'services';

  protected static ?string $recordTitleAttribute = 'id';

  /**
   * Get relation manager title
   *
   * @return string
   */
  public static function getTitle(Model $ownerRecord, string $pageClass): string
  {
    return __('Services');
  }

  /**
   * Configure form schema
   *
   * @param Schema $schema
   * @return Schema
   */
  public function form(Schema $schema): Schema
  {
    return ServiceForm::configure($schema);
  }

  /**
   * Configure table
   *
   * @param Table $table
   * @return Table
   */
  public function table(Table $table): Table
  {
    return $table
      ->recordTitle(fn(Model $record): string => $this->getRecordTitle($record))
      ->columns($this->getColumns())
      ->filters($this->getFilters())
      ->headerActions($this->getHeaderActions())
      ->actions($this->getTableActions())
      ->bulkActions($this->getBulkActions())
      ->defaultSort('created_at', 'desc');
  }

  /**
   * Get record title
   *
   * @param Model $record
   * @return string
   */
  protected function getRecordTitle(Model $record): string
  {
    if (!$record instanceof Service) {
      return "Service #{$record->id}";
    }

    $translation = $record->translation(app()->getLocale()) ?? $record->translations->first();

    return $translation?->title ?? "Service #{$record->id}";
  }

  /**
   * Get table columns
   *
   * @return array<int, \Filament\Tables\Columns\Column>
   */
  protected function getColumns(): array
  {
    return [
      Tables\Columns\TextColumn::make('serviceType.translations.name')
        ->label(__('Type'))
        ->sortable()
        ->searchable()
        ->getStateUsing(function (Service $record): ?string {
          $translation = $record->serviceType?->translations->where('language_code', app()->getLocale())->first();

          return $translation?->name ?? $record->serviceType?->translations->first()?->name;
        })
        ->badge()
        ->color('info'),

      Tables\Columns\TextColumn::make('translations.title')
        ->label(__('Title'))
        ->getStateUsing(fn(Service $record): ?string => $record->translation(app()->getLocale())?->title)
        ->limit(50)
        ->searchable()
        ->wrap(),

      Tables\Columns\TextColumn::make('price')->label(__('Price'))->money('EUR')->sortable()->alignEnd(),

      Tables\Columns\TextColumn::make('duration')->label(__('Duration'))->searchable()->badge()->color('gray'),

      Tables\Columns\TextColumn::make('location')->label(__('Location'))->searchable()->wrap()->limit(30)->toggleable(),

      Tables\Columns\IconColumn::make('is_active')
        ->label(__('Active'))
        ->boolean()
        ->trueColor('success')
        ->falseColor('danger')
        ->sortable(),

      Tables\Columns\IconColumn::make('is_featured')
        ->label(__('Featured'))
        ->boolean()
        ->trueColor('warning')
        ->falseColor('gray')
        ->sortable(),

      Tables\Columns\TextColumn::make('media_count')
        ->label(__('Media'))
        ->counts('media')
        ->sortable()
        ->alignCenter()
        ->badge()
        ->color('primary'),
    ];
  }

  /**
   * Get table filters
   *
   * @return array<int, \Filament\Tables\Filters\BaseFilter>
   */
  protected function getFilters(): array
  {
    return [
      Tables\Filters\TernaryFilter::make('is_active')
        ->label(__('Active'))
        ->placeholder(__('All'))
        ->trueLabel(__('Active only'))
        ->falseLabel(__('Inactive only')),

      Tables\Filters\TernaryFilter::make('is_featured')
        ->label(__('Featured'))
        ->placeholder(__('All'))
        ->trueLabel(__('Featured only'))
        ->falseLabel(__('Not featured')),
    ];
  }

  /**
   * Get header actions
   *
   * @return array<int, \Filament\Actions\Action>
   */
  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make()->label(__('Create Service'))->url(
        fn(): string => \Platform\EnjoyTheWorld\Filament\Resources\Services\ServiceResource::getUrl('create', [
          'ownerRecord' => $this->getOwnerRecord(),
        ]),
      ),

      AssociateAction::make()
        ->label(__('Associate Service'))
        ->preloadRecordSelect()
        ->recordSelectOptionsQuery(fn(Builder $query): Builder => $query->whereNull('provider_id'))
        ->recordSelectSearchColumns(['id', 'location'])
        ->recordTitleAttribute('id'),
    ];
  }

  /**
   * Get table actions
   *
   * @return array<int, \Filament\Tables\Actions\Action>
   */
  protected function getTableActions(): array
  {
    return [
      EditAction::make('edit')->label(__('Edit'))->icon('heroicon-o-pencil')->url(
        fn(Service $record): string => \Platform\EnjoyTheWorld\Filament\Resources\Services\ServiceResource::getUrl(
          'edit',
          [
            'record' => $record,
            'ownerRecord' => $this->getOwnerRecord(),
          ],
        ),
      ),

      DissociateAction::make()->label(__('Dissociate')),

      DeleteAction::make()->label(__('Delete')),
    ];
  }

  /**
   * Get relation manager pages
   *
   * @return array<string, string>
   */
  public static function getPages(): array
  {
    return [
      'create' => CreateService::route('/create'),
      'edit' => EditService::route('/{record}/edit'),
    ];
  }

  /**
   * Get bulk actions
   *
   * @return array<int, \Filament\Tables\Actions\BulkAction>
   */
  protected function getBulkActions(): array
  {
    return [BulkActionGroup::make([DissociateBulkAction::make()->label(__('Dissociate selected'))])];
  }
}
