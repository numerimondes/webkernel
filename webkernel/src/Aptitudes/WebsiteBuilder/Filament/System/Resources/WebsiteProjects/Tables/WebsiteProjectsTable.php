<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebsiteProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->size('sm')
                    ->icon('heroicon-o-globe-alt')
                    ->iconPosition('before'),

                TextColumn::make('status_id')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->getEnumLabel('status_id'))
                    ->color(fn ($record) => $record->getEnumCssClass('status_id'))
                    ->icon(fn ($record) => $record->getEnumIcon('status_id')),

                TextColumn::make('type_id')
                    ->label('Project Type')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->getEnumLabel('type_id'))
                    ->color(fn ($record) => $record->getEnumCssClass('type_id'))
                    ->icon(fn ($record) => $record->getEnumIcon('type_id')),

                ColumnGroup::make('Site Information')
                    ->columns([
                        TextColumn::make('site_title_key')
                            ->label('Site Title Key')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->copyable()
                            ->copyMessage('Site title key copied!')
                            ->icon('heroicon-o-key'),

                        TextColumn::make('slug')
                            ->label('URL Slug')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->copyable()
                            ->copyMessage('Slug copied!')
                            ->prefix('/')
                            ->color('gray')
                            ->fontFamily('mono')
                            ->size('xs'),

                        TextColumn::make('description')
                            ->label('Description')
                            ->width('20%')
                            ->searchable()
                            ->wrap()
                            ->lineClamp(2)
                            ->tooltip(fn ($record) => $record->description)
                            ->color('gray')
                            ->size('xs'),
                    ]),

                ColumnGroup::make('Configuration')
                    ->columns([
                        TextColumn::make('version')
                            ->label('Version')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-tag')
                            ->prefix('v'),

                        IconColumn::make('is_multilingual')
                            ->label('Multilingual')
                            ->boolean()
                            ->trueIcon('heroicon-o-language')
                            ->falseIcon('heroicon-o-globe-americas')
                            ->trueColor('success')
                            ->falseColor('gray')
                            ->tooltip(fn ($record) => $record->is_multilingual ? 'Multilingual site' : 'Single language site'),

                        TextColumn::make('main_language')
                            ->label('Primary Language')
                            ->searchable()
                            ->badge()
                            ->color('violet')
                            ->icon('heroicon-o-language')
                            ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                    ]),

                ColumnGroup::make('URLs & Domains')
                    ->columns([
                        TextColumn::make('domain')
                            ->label('Domain')
                            ->searchable()
                            ->copyable()
                            ->copyMessage('Domain copied!')
                            ->url(fn ($record) => 'https://' . $record->domain)
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-link')
                            ->color('blue')
                            ->weight('medium'),

                        TextColumn::make('canonical_url')
                            ->label('Canonical URL')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true)
                            ->copyable()
                            ->copyMessage('Canonical URL copied!')
                            ->url(fn ($record) => $record->canonical_url)
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->color('blue')
                            ->fontFamily('mono')
                            ->size('xs'),
                    ]),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y \a\t H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->icon('heroicon-o-calendar-days')
                    ->color('gray')
                    ->size('xs'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y \a\t H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->size('xs'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->searchDebounce('550ms')
            ->actionsColumnLabel('Actions')
            ->deferColumnManager(false)
            ->striped()
            ->filters([
                // Add your filters here
            ])
            ->actions([
                EditAction::make()

                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->tooltip('Edit project'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to delete these projects? This action cannot be undone.'),
                ]),
            ])
            ->emptyStateHeading('No website projects found')
            ->emptyStateDescription('Get started by creating your first website project.')
            ->emptyStateIcon('heroicon-o-globe-alt');
    }
}
