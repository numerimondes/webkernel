<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProvidersTable
{
  /**
   * Configure providers table
   *
   * @param Table $table
   * @return Table
   */
  public static function configure(Table $table): Table
  {
    return $table
      ->columns(self::getColumns())
      ->filters(self::getFilters())
      ->actions(self::getActions())
      ->bulkActions(self::getBulkActions())
      ->defaultSort('created_at', 'desc');
  }

  /**
   * Get table columns
   *
   * @return array<int, \Filament\Tables\Columns\Column>
   */
  protected static function getColumns(): array
  {
    return [
      TextColumn::make('user.name')->label(__('User'))->sortable()->searchable()->toggleable(),

      TextColumn::make('company_name')->label(__('Company'))->sortable()->searchable()->wrap()->weight('bold'),

      TextColumn::make('phone')
        ->label(__('Phone'))
        ->searchable()
        ->toggleable()
        ->copyable()
        ->copyMessage(__('Phone copied'))
        ->copyMessageDuration(1500),

      TextColumn::make('website')
        ->label(__('Website'))
        ->url(fn($record): ?string => $record->website, shouldOpenInNewTab: true)
        ->wrap()
        ->toggleable()
        ->limit(30),

      IconColumn::make('is_active')
        ->label(__('Active'))
        ->boolean()
        ->trueIcon('heroicon-o-check-circle')
        ->falseIcon('heroicon-o-x-circle')
        ->trueColor('success')
        ->falseColor('danger')
        ->sortable(),

      TextColumn::make('services_count')
        ->label(__('Services'))
        ->counts('services')
        ->sortable()
        ->alignCenter()
        ->badge()
        ->color('primary'),

      TextColumn::make('created_at')
        ->label(__('Created'))
        ->dateTime()
        ->sortable()
        ->toggleable(isToggledHiddenByDefault: true),

      TextColumn::make('updated_at')
        ->label(__('Updated'))
        ->dateTime()
        ->sortable()
        ->toggleable(isToggledHiddenByDefault: true),
    ];
  }

  /**
   * Get table filters
   *
   * @return array<int, \Filament\Tables\Filters\BaseFilter>
   */
  protected static function getFilters(): array
  {
    return [
      SelectFilter::make('is_active')
        ->label(__('Status'))
        ->options([
          '1' => __('Active'),
          '0' => __('Inactive'),
        ])
        ->placeholder(__('All')),

      TrashedFilter::make()->label(__('Deleted'))->placeholder(__('Without deleted')),
    ];
  }

  /**
   * Get table actions
   *
   * @return array<int, \Filament\Tables\Actions\Action>
   */
  protected static function getActions(): array
  {
    return [EditAction::make()->label(__('Edit'))];
  }

  /**
   * Get bulk actions
   *
   * @return array<int, \Filament\Tables\Actions\BulkAction>
   */
  protected static function getBulkActions(): array
  {
    return [
      BulkActionGroup::make([
        DeleteBulkAction::make()->label(__('Delete selected')),

        ForceDeleteBulkAction::make()->label(__('Force delete selected')),

        RestoreBulkAction::make()->label(__('Restore selected')),
      ]),
    ];
  }
}
