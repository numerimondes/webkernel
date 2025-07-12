<?php

namespace Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Webkernel\Core\Traits\AutoDiscoverable;
use Numerimondes\Modules\ReamMar\Core\Models\Client;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages\EditClient;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages\ListClients;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Pages\CreateClient;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Schemas\ClientForm;
use Numerimondes\Modules\ReamMar\Core\Filament\Resources\Clients\Tables\ClientsTable;

class ClientResource extends Resource
{
    use AutoDiscoverable;

    protected static ?string $model = Client::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * Indique que cette ressource peut être découverte automatiquement
     */
    public static function isDiscovered(): bool
    {
        return true;
    }

    /**
     * Spécifie dans quels panels cette ressource doit apparaître
     */
    public static function put_in_panel(): array
    {
        return ['system']; 
    }

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

    /**
     * Retourne le slug pour la navigation (optionnel)
     */
    //protected static ?string $navigationGroup = 'clients';
    
    /**
     * Ordre dans la navigation (optionnel)
     */
    protected static ?int $navigationSort = 1;
}