<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages\CreateClient;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages\EditClient;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages\ListClients;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Schemas\ClientForm;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Tables\ClientsTable;
use Numerimondes\Modules\ReamMar\Core\Models\Client;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 1 ? 'warning' : 'primary';
    }
}
