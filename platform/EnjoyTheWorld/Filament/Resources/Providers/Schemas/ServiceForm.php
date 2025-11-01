<?php

declare(strict_types=1);

namespace Platform\EnjoyTheWorld\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\{Section, Tabs, Grid};
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;
use Platform\EnjoyTheWorld\Models\ServiceType;

class ServiceForm
{
  /**
   * Configure service form schema
   *
   * @param Schema $schema
   * @return Schema
   */
  public static function configure(Schema $schema): Schema
  {
    return $schema->schema(self::getSchema());
  }

  /**
   * Get form schema components
   *
   * @return array<int, \Filament\Forms\Components\Component>
   */
  public static function getSchema(): array
  {
    return [
      Section::make(__('Basic Information'))->schema([
        Grid::make(2)->schema([
          Select::make('service_type_id')
            ->label(__('Service Type'))
            ->options(self::getServiceTypeOptions())
            ->searchable()
            ->preload()
            ->required()
            ->reactive()
            ->helperText(__('Select the category of service'))
            ->columnSpanFull(),

          TextInput::make('price')
            ->label(__('Price (EUR)'))
            ->numeric()
            ->required()
            ->step(0.01)
            ->prefix('EUR')
            ->minValue(0)
            ->helperText(__('Service price in euros'))
            ->columnSpan(1),

          TextInput::make('duration')
            ->label(__('Duration'))
            ->required()
            ->maxLength(50)
            ->placeholder(__('e.g., 2h, 3 days'))
            ->helperText(__('Expected duration of the service'))
            ->columnSpan(1),

          TextInput::make('location')
            ->label(__('Location'))
            ->required()
            ->maxLength(255)
            ->helperText(__('Where the service is provided'))
            ->columnSpanFull(),

          Toggle::make('is_active')
            ->label(__('Active'))
            ->default(true)
            ->inline(false)
            ->helperText(__('Make service visible to customers'))
            ->columnSpan(1),

          Toggle::make('is_featured')
            ->label(__('Featured'))
            ->default(false)
            ->inline(false)
            ->helperText(__('Highlight this service'))
            ->columnSpan(1),
        ]),
      ]),

      Tabs::make(__('Content'))->tabs(self::getTranslationTabs())->columnSpanFull(),

      Section::make(__('Media Gallery'))
        ->schema([
          Repeater::make('media')
            ->relationship('media')
            ->schema([
              Grid::make(3)->schema([
                Select::make('type')
                  ->label(__('Media Type'))
                  ->options([
                    'image' => __('Image'),
                    'video' => __('Video'),
                  ])
                  ->required()
                  ->reactive()
                  ->columnSpan(1),

                FileUpload::make('url')
                  ->label(__('File'))
                  ->required()
                  ->directory('services/media')
                  ->maxSize(10240)
                  //->acceptedFileTypes(fn(Get $get): array => $get('type') === 'image' ? ['image/*'] : ['video/*'])
                  ->imageEditor()
                  ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                  ->columnSpan(2),

                TextInput::make('caption')->label(__('Caption'))->maxLength(255)->columnSpanFull(),

                TextInput::make('order')->label(__('Display Order'))->numeric()->default(0)->minValue(0)->columnSpan(1),
              ]),
            ])
            ->defaultItems(0)
            ->collapsible()
            ->itemLabel(
              fn(array $state): ?string => isset($state['type'])
                ? ucfirst($state['type']) . ' #' . ($state['order'] ?? 0)
                : null,
            )
            ->reorderable()
            ->reorderableWithButtons()
            ->addActionLabel(__('Add Media'))
            ->collapsed(),
        ])
        ->collapsible(),
    ];
  }

  /**
   * Get translation tabs
   *
   * @return array<int, \Filament\Forms\Components\Tabs\Tab>
   */
  protected static function getTranslationTabs(): array
  {
    $languages = [
      'en' => ['label' => __('English'), 'icon' => 'heroicon-o-flag'],
      'fr' => ['label' => __('French'), 'icon' => 'heroicon-o-flag'],
      'es' => ['label' => __('Spanish'), 'icon' => 'heroicon-o-flag'],
    ];

    $tabs = [];

    foreach ($languages as $code => $config) {
      $tabs[] = Tabs\Tab::make($config['label'])
        ->icon($config['icon'])
        ->schema([
          TextInput::make("translation_{$code}_title")
            ->label(__('Title'))
            ->maxLength(255)
            ->required($code === 'en')
            ->afterStateHydrated(function (TextInput $component, $state, $record) use ($code): void {
              if ($record && method_exists($record, 'translation')) {
                $translation = $record->translation($code);
                $component->state($translation?->title);
              }
            })
            ->dehydrated(false),

          Textarea::make("translation_{$code}_description")
            ->label(__('Description'))
            ->rows(6)
            ->maxLength(65535)
            ->afterStateHydrated(function (Textarea $component, $state, $record) use ($code): void {
              if ($record && method_exists($record, 'translation')) {
                $translation = $record->translation($code);
                $component->state($translation?->description);
              }
            })
            ->dehydrated(false),
        ])
        ->columns(1);
    }

    return $tabs;
  }

  /**
   * Get service type options
   *
   * @return array<int, string>
   */
  protected static function getServiceTypeOptions(): array
  {
    return ServiceType::query()
      ->active()
      ->with('translations')
      ->get()
      ->mapWithKeys(function (ServiceType $type): array {
        $name =
          $type->translations->where('language_code', app()->getLocale())->first()?->name ??
          ($type->translations->first()?->name ?? __('Unnamed Service Type'));

        return [$type->id => $name];
      })
      ->toArray();
  }
}
