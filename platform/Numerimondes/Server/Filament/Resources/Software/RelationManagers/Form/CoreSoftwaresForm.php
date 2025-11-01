<?php
declare(strict_types=1);

namespace Platform\Numerimondes\Server\Filament\Resources\Software\RelationManagers\Form;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Platform\Numerimondes\Server\Models\SoftwareCore;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Webkernel\Aptitudes\Security\ValidateSourceCodeZip\Filament\SourceCodeUploadZipOnForm;

class CoreSoftwaresForm
{
  public static function getSchema(string $softwareName): array
  {
    return [
      Section::make('Core Module Details')
        ->description('Configure your core software module')
        ->secondary()
        ->schema([
          Grid::make(2)->schema([
            TextInput::make('name')
              ->label('Module Name')
              ->placeholder('e.g., Horizon, Classic, Mint')
              ->required()
              ->maxLength(255)
              ->helperText("Part of: {$softwareName}"),
            TextInput::make('version')
              ->label('Version')
              ->placeholder('e.g., 1.0.0')
              ->required()
              ->maxLength(255)
              ->regex('/^\d+\.\d+\.\d+$/')
              ->helperText('Format: major.minor.patch'),
          ]),
          Grid::make(1)->schema([
            TextInput::make('namespace')
              ->label('Namespace')
              ->placeholder('e.g., Modules\\CoreModule')
              ->required()
              ->maxLength(255)
              ->helperText('PHP namespace for this core module'),
          ]),
          Section::make('Deployment Information')
            ->collapsible()
            ->collapsed(false)
            ->schema([
              Grid::make(2)->schema([
                TextInput::make('install_path')
                  ->label('Installation Path')
                  ->placeholder('/path/to/install')
                  ->maxLength(255)
                  ->helperText('Optional: Override default installation path'),
                SourceCodeUploadZipOnForm::make('zip_path')
                  ->label('Source Code Archive')
                  ->placeholder('Upload module source code (ZIP)')
                  ->helperText('Automatic validation with checksum generation')
                  ->required()
                  ->directory("software/{$softwareName}/code")
                  ->getField(), // AJOUT DE ->getField()
              ]),
            ]),
        ])
        ->columnSpan('full'),
    ];
  }
}
