<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class CoreSoftwaresTable
{
  public static function getColumns(): array
  {
    return [
      TextColumn::make('name')->label('Module')->sortable()->searchable()->weight('semibold')->icon('heroicon-o-cube'),

      TextColumn::make('version')->label('Version')->sortable()->badge()->color('info'),

      BadgeColumn::make('validation_status')
        ->label('Status')
        ->colors([
          'secondary' => 'pending',
          'warning' => 'processing',
          'success' => 'validated',
          'danger' => 'failed',
        ])
        ->icons([
          'heroicon-o-clock' => 'pending',
          'heroicon-o-arrow-path' => 'processing',
          'heroicon-o-check-circle' => 'validated',
          'heroicon-o-x-circle' => 'failed',
        ]),

      IconColumn::make('has_license')
        ->label('License')
        ->boolean()
        ->trueIcon('heroicon-o-check-circle')
        ->falseIcon('heroicon-o-x-circle')
        ->trueColor('success')
        ->falseColor('danger')
        ->getStateUsing(fn($record) => $record->metadata['license']['exists'] ?? false)
        ->toggleable(),

      IconColumn::make('has_composer')
        ->label('Composer')
        ->boolean()
        ->trueIcon('heroicon-o-check-circle')
        ->falseIcon('heroicon-o-x-circle')
        ->trueColor('success')
        ->falseColor('danger')
        ->getStateUsing(fn($record) => $record->metadata['composer']['valid'] ?? false)
        ->toggleable(),

      TextColumn::make('namespace')
        ->label('Namespace')
        ->sortable()
        ->searchable()
        ->limit(40)
        ->tooltip(fn($record) => $record->namespace)
        ->toggleable(isToggledHiddenByDefault: true),

      TextColumn::make('hash')
        ->label('Hash')
        ->limit(16)
        ->tooltip(fn($record) => $record->hash)
        ->toggleable(isToggledHiddenByDefault: true),

      TextColumn::make('created_at')
        ->label('Created')
        ->dateTime('d/m/Y H:i')
        ->sortable()
        ->toggleable(isToggledHiddenByDefault: true),

      TextColumn::make('validated_at')
        ->label('Validated')
        ->dateTime('d/m/Y H:i')
        ->sortable()
        ->toggleable(isToggledHiddenByDefault: true),
    ];
  }

  public static function getEmptyState(): array
  {
    return [
      'heading' => 'No core modules yet',
      'description' => 'Create your first core software module to get started',
      'icon' => 'heroicon-o-cube',
    ];
  }
}
