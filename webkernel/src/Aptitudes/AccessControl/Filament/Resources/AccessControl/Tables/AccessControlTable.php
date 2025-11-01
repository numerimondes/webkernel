<?php declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AccessControlTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')->label('Group Name')->searchable()->sortable()->weight('bold'),

        TextColumn::make('slug')->label('Slug')->searchable()->sortable()->copyable()->icon('heroicon-o-clipboard'),

        TextColumn::make('priority')->label('Priority')->sortable()->alignCenter()->badge()->color(
          fn(int $state): string => match (true) {
            $state >= 900 => 'danger',
            $state >= 500 => 'warning',
            $state >= 100 => 'info',
            default => 'gray',
          },
        ),

        IconColumn::make('is_system')->label('System')->boolean()->alignCenter(),

        IconColumn::make('is_active')->label('Active')->boolean()->alignCenter(),

        TextColumn::make('users_count')->label('Users')->counts('users')->alignCenter()->badge()->color('success'),

        TextColumn::make('permissions_count')
          ->label('Permissions')
          ->counts('permissions')
          ->alignCenter()
          ->badge()
          ->color('info'),

        TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),

        TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Filter::make('system_groups')
          ->label('System Groups Only')
          ->query(fn(Builder $query): Builder => $query->where('is_system', true)),

        Filter::make('active_groups')
          ->label('Active Groups Only')
          ->query(fn(Builder $query): Builder => $query->where('is_active', true)),

        Filter::make('has_users')
          ->label('Has Assigned Users')
          ->query(fn(Builder $query): Builder => $query->has('users')),

        Filter::make('high_priority')
          ->label('High Priority (≥500)')
          ->query(fn(Builder $query): Builder => $query->where('priority', '>=', 500)),
      ])
      ->recordActions([
        EditAction::make()->disabled(fn(Model $record): bool => $record->slug === 'superadmin'),

        Action::make('duplicate')
          ->label('Duplicate')
          ->icon('heroicon-o-document-duplicate')
          ->requiresConfirmation()
          ->action(function (Model $record): void {
            $newGroup = $record->replicate();
            $newGroup->name = $record->name . ' (Copy)';
            $newGroup->slug = $record->slug . '-copy-' . uniqid();
            $newGroup->is_system = false;
            $newGroup->save();

            $permissions = $record->permissions()->pluck('permissions.id');
            $newGroup->permissions()->attach($permissions);
          })
          ->successNotificationTitle('Permission group duplicated'),
      ])
      ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
      ->defaultSort('priority', 'desc')
      ->poll('60s');
  }
}
