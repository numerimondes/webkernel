<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\WebsiteBuilder\Filament\System\Resources\WebsiteProjects\Schemas;

// Form Components
use Filament\Actions\Action;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
// Schema Components
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
// Infolist Components for display
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class WebsiteProjectForm
{
  /**
   * Creates a simplified form for modal creation
   */
  public static function createForm(Schema $schema): Schema
  {
    return $schema->components([
      Section::make('')
        ->schema([
          TextInput::make('name')
            ->label('Nom du projet')
            ->required()
            ->maxLength(255)
            ->placeholder('Mon super site web'),

          TextInput::make('domain')
            ->label('Domaine')
            ->required()
            ->maxLength(255)
            ->placeholder('monsite.com')
            ->unique('apt_website_projects', 'domain', ignoreRecord: true)
            ->helperText('Le domaine principal de votre site web'),

          TextInput::make('description')
            ->label('Description')
            ->maxLength(255)
            ->placeholder('Description courte de votre projet'),

          Select::make('type_id')
            ->label('Type de projet')
            ->options(
              fn() => new \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject()->getEnumOptionsWithIds('type_id'),
            )
            ->searchable()
            ->debounce(250)
            ->preload(),

          Select::make('status_id')
            ->label('Statut')
            ->options(
              fn() => new \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject()->getEnumOptionsWithIds(
                'status_id',
              ),
            )
            ->searchable()
            ->preload()
            ->debounce(250)
            ->required(),
        ])
        ->contained(false)
        ->compact()
        ->columns(2),
    ]);
  }
  /**
   * Creates a reusable HeroCard component
   */
  private static function createHeroCard(
    string $title,
    string $description,
    string $backgroundImage,
    float $overlayOpacity = 0.6,
    string $size = 'sm',
  ): View {
    return View::make('ui::components.HeroCard.index')
      ->viewData([
        'title' => $title,
        'description' => $description,
        'backgroundImage' => $backgroundImage,
        'overlayOpacity' => $overlayOpacity,
        'size' => $size,
      ])
      ->columnSpanFull();
  }

  /**
   * Available language options configuration
   */
  private static function getLanguageOptions(): array
  {
    return [
      'en' => 'English',
      'fr' => 'French',
      'es' => 'Spanish',
      'de' => 'German',
      'it' => 'Italian',
      'pt' => 'Portuguese',
      'ar' => 'Arabic',
      'zh' => 'Chinese',
      'ja' => 'Japanese',
      'ko' => 'Korean',
      'nl' => 'Dutch',
      'ru' => 'Russian',
    ];
  }

  /**
   * Available timezone options configuration
   */
  private static function getTimezoneOptions(): array
  {
    return [
      'UTC' => 'UTC (Coordinated Universal Time)',
      'Europe/London' => 'Europe/London (GMT/BST)',
      'Europe/Paris' => 'Europe/Paris (CET/CEST)',
      'Europe/Berlin' => 'Europe/Berlin (CET/CEST)',
      'America/New_York' => 'America/New_York (EST/EDT)',
      'America/Chicago' => 'America/Chicago (CST/CDT)',
      'America/Denver' => 'America/Denver (MST/MDT)',
      'America/Los_Angeles' => 'America/Los_Angeles (PST/PDT)',
      'Asia/Tokyo' => 'Asia/Tokyo (JST)',
      'Asia/Shanghai' => 'Asia/Shanghai (CST)',
      'Asia/Dubai' => 'Asia/Dubai (GST)',
      'Australia/Sydney' => 'Australia/Sydney (AEST/AEDT)',
    ];
  }

  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Group::make()
          ->schema([
            // Main Project Hero Card - Changes here will be reflected in all tabs
            self::createHeroCard(
              'Website Project Configuration',
              'Configure your website project settings, branding, and technical specifications',
              'https://images.unsplash.com/photo-1748019349196-eb2e1026fafd?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
              0.7,
              'md',
            ),

            // Main Configuration Tabs
            Tabs::make('main_configuration')
              ->columnSpanFull()
              ->tabs([
                // TAB 1 - Project Details & Identity
                Tabs\Tab::make('Project Details')
                  ->icon('heroicon-o-document-text')
                  ->schema([
                    Fieldset::make('Basic Project Information')
                      ->columns([
                        'default' => 1,
                        'md' => 2,
                      ])
                      ->schema([
                        TextInput::make('name')
                          ->label('Project Name')
                          ->required()
                          ->maxLength(255)
                          ->placeholder('My Website Project')
                          ->helperText('The display name of your website project'),

                        TextInput::make('slug')
                          ->label('URL Slug')
                          ->required()
                          ->maxLength(255)
                          ->placeholder('my-website-project')
                          ->rules(['regex:/^[a-z0-9\-]+$/'])
                          ->helperText('Used in URLs and file paths (lowercase, numbers, hyphens only)'),
                      ]),

                    Fieldset::make('Domain and Status')
                      ->columns([
                        'default' => 1,
                        'md' => 2,
                      ])
                      ->schema([
                        TextInput::make('domain')
                          ->label('Primary Domain')
                          ->required()
                          ->url()
                          ->placeholder('https://example.com')
                          ->helperText('The main domain where your website will be accessible'),

                        Select::make('status_id')
                          ->label('Project Status')
                          ->options(
                            fn() => new \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject()->getEnumOptionsWithIds(
                              'status_id',
                            ),
                          )
                          ->searchable()
                          ->preload()
                          ->required()
                          ->helperText('Current status of your website project'),
                      ]),

                    // Website Builder Action
                    Actions::make([
                      Action::make('open_builder')
                        ->label('Open Website Builder')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('primary')
                        ->url(fn($record) => $record ? "/v1/builder/{$record->slug}" : '#')
                        ->openUrlInNewTab()
                        ->visible(fn($record) => $record && $record->slug),
                    ])
                      ->alignCenter()
                      ->columnSpanFull(),
                  ]),

                Tabs\Tab::make('Project Specifications')
                  ->icon('heroicon-o-document-text')
                  ->schema([
                    Fieldset::make('Project Configuration')
                      ->columns([
                        'default' => 1,
                        'md' => 3,
                      ])
                      ->schema([
                        Select::make('type_id')
                          ->label('Project Type')
                          ->options(
                            fn() => new \Webkernel\Aptitudes\WebsiteBuilder\Models\WebsiteProject()->getEnumOptionsWithIds(
                              'type_id',
                            ),
                          )
                          ->searchable()
                          ->preload()
                          ->required(),

                        TextInput::make('version')
                          ->label('Project Version')
                          ->placeholder('1.0.0')
                          ->default('1.0.0')
                          ->helperText('Semantic versioning (major.minor.patch)'),

                        TextInput::make('site_title_key')
                          ->label('Site Title Translation Key')
                          ->maxLength(255)
                          ->placeholder('site.title')
                          ->helperText('Translation key for dynamic titles'),
                      ]),
                    MarkdownEditor::make('description')
                      ->label('Project Description & Specifications')

                      ->placeholder('')
                      ->fileAttachmentsMaxSize(10240)
                      ->columnSpanFull(),
                  ]),

                // TAB 2 - Localization & Languages (Enhanced)
                Tabs\Tab::make('Localization')
                  ->icon('heroicon-o-language')
                  ->schema([
                    Fieldset::make('Primary Language Settings')
                      ->columns([
                        'default' => 1,
                        'md' => 3,
                      ])
                      ->schema([
                        Select::make('main_language')
                          ->label('Primary Language')
                          ->options(self::getLanguageOptions())
                          ->default('en')
                          ->searchable()
                          ->required(),

                        Select::make('main_timezone')
                          ->label('Primary Timezone')
                          ->options(self::getTimezoneOptions())
                          ->default('UTC')
                          ->searchable()
                          ->required(),
                      ]),

                    Fieldset::make('Additional Languages')->schema([
                      Toggle::make('is_multilingual')
                        ->label('Enable Multiple Languages')
                        ->default(false)
                        ->helperText('Allow content in multiple languages')
                        ->live(),

                      Select::make('additional_languages')
                        ->label('Select additional languages')
                        ->options(self::getLanguageOptions())
                        ->multiple()
                        ->searchable()
                        ->placeholder('Choose languages...')
                        ->visible(fn(callable $get) => $get('is_multilingual')),
                    ]),
                  ]),

                // TAB 3 - Domain & Technical Settings
                Tabs\Tab::make('Domain & Technical')
                  ->icon('heroicon-o-globe-alt')
                  ->schema([
                    Fieldset::make('Domain Settings')
                      ->columns([
                        'default' => 1,
                        'md' => 2,
                      ])
                      ->schema([
                        TextInput::make('canonical_url')
                          ->label('Canonical URL')
                          ->url()
                          ->placeholder('https://www.example.com')
                          ->helperText('Preferred URL for SEO (usually with www)'),

                        TextInput::make('password_protection')
                          ->label('Site Password Protection')
                          ->password()
                          ->revealable()
                          ->placeholder('Optional site-wide password')
                          ->helperText('Protect entire site with password (staging/private sites)'),
                      ]),

                    Fieldset::make('Advanced Options')
                      ->columns([
                        'default' => 1,
                        'md' => 2,
                      ])
                      ->schema([
                        Toggle::make('preserve_url_parameters')
                          ->label('Preserve URL Parameters')
                          ->default(true)
                          ->helperText('Keep UTM and tracking parameters during navigation'),

                        Toggle::make('no_accessibility')
                          ->label('Disable Accessibility Features')
                          ->default(false)
                          ->helperText('Not recommended - accessibility should be enabled for compliance'),
                      ]),
                  ]),

                // TAB 4 - SEO & Metadata
                Tabs\Tab::make('SEO & Metadata')
                  ->icon('heroicon-o-magnifying-glass')
                  ->schema([
                    Fieldset::make('Meta Tags & Translation Keys')
                      ->columns([
                        'default' => 1,
                        'md' => 2,
                      ])
                      ->schema([
                        TextInput::make('og_title_key')
                          ->label('Open Graph Title Key')
                          ->placeholder('og.title')
                          ->helperText('Translation key for social media title'),

                        TextInput::make('og_description_key')
                          ->label('Open Graph Description Key')
                          ->placeholder('og.description')
                          ->helperText('Translation key for social media description'),
                      ]),

                    Fieldset::make('Icons & Images')
                      ->columns([
                        'default' => 1,
                        'md' => 3,
                      ])
                      ->schema([
                        FileUpload::make('favicon_path')
                          ->label('Favicon (16x16, 32x32)')
                          ->image()
                          ->disk('public')
                          ->directory('website-assets/favicons')
                          ->visibility('public')
                          ->acceptedFileTypes(['image/x-icon', 'image/png'])
                          ->helperText('Small icon displayed in browser tabs (.ico or .png)'),

                        FileUpload::make('og_image_path')
                          ->label('Social Share Image (1200x630)')
                          ->image()
                          ->disk('public')
                          ->directory('website-assets/social')
                          ->visibility('public')
                          ->helperText('Image shown when sharing on social media'),

                        FileUpload::make('apple_touch_icon_path')
                          ->label('Apple Touch Icon (180x180)')
                          ->image()
                          ->disk('public')
                          ->directory('website-assets/favicons')
                          ->visibility('public')
                          ->helperText('Icon for Apple devices home screen'),
                      ]),
                  ]),
              ]),

            // Branding Section
            self::createHeroCard(
              'Branding & Visual Identity',
              'Logo files, colors, and visual branding elements for your website',
              'https://images.unsplash.com/photo-1748019349201-3708ad4d904c?q=80&w=1171&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
              0.3,
              'md',
            ),

            Tabs::make('branding_tabs')
              ->columnSpanFull()
              ->tabs([
                // TAB 1 - Logos & Assets
                Tabs\Tab::make('Logos & Assets')
                  ->icon('heroicon-o-photo')
                  ->schema([
                    Fieldset::make('Logo Files')
                      ->columns([
                        'default' => 1,
                        'md' => 2,
                      ])
                      ->schema([
                        FileUpload::make('logo_path')
                          ->label('Main Logo')
                          ->image()
                          ->disk('public')
                          ->directory('website-assets/logos')
                          ->visibility('public')
                          ->helperText('Primary logo (preferably SVG or high-res PNG)'),

                        FileUpload::make('logo_dark')
                          ->label('Dark Mode Logo')
                          ->image()
                          ->disk('public')
                          ->directory('website-assets/logos')
                          ->visibility('public')
                          ->helperText('Logo variant for dark themes'),
                      ]),

                    Fieldset::make('Background Settings')
                      ->columns([
                        'default' => 1,
                        'md' => 2,
                      ])
                      ->schema([
                        FileUpload::make('background_image')
                          ->label('Hero Background Image')
                          ->image()
                          ->disk('public')
                          ->directory('website-assets/backgrounds')
                          ->visibility('public')
                          ->helperText('Main background image for hero sections'),

                        TextInput::make('opacity')
                          ->label('Background Opacity')
                          ->numeric()
                          ->minValue(0)
                          ->maxValue(100)
                          ->default(50)
                          ->suffix('%')
                          ->helperText('Background image transparency (0 = transparent, 100 = opaque)'),
                      ]),
                  ]),

                // TAB 2 - Brand Colors
                Tabs\Tab::make('Brand Colors')
                  ->icon('heroicon-o-swatch')
                  ->schema([
                    Fieldset::make('Color Palette')
                      ->columns([
                        'default' => 1,
                        'md' => 3,
                      ])
                      ->schema([
                        ColorPicker::make('primary_color')
                          ->label('Primary Brand Color')
                          ->default('#3B82F6')
                          ->helperText('Main brand color for buttons, links, highlights'),

                        ColorPicker::make('secondary_color')
                          ->label('Secondary Color')
                          ->default('#6B7280')
                          ->helperText('Supporting color for text, borders, subtle elements'),

                        ColorPicker::make('accent_color')
                          ->label('Accent Color')
                          ->default('#F59E0B')
                          ->helperText('Call-to-action color for important elements'),
                      ]),
                  ]),

                // TAB 3 - Custom Code
                Tabs\Tab::make('Custom Code')
                  ->icon('heroicon-o-code-bracket')
                  ->schema([
                    self::createHeroCard(
                      'Custom Code Injection',
                      'Add custom HTML, CSS, JavaScript, and tracking codes',
                      'https://images.unsplash.com/photo-1571171637578-41bc2dd41cd2?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
                      0.4,
                    ),

                    Tabs::make('code_injection_inner')
                      ->columnSpanFull()
                      ->tabs([
                        Tabs\Tab::make('Head Start')
                          ->icon('heroicon-o-document-arrow-down')
                          ->schema([
                            CodeEditor::make('custom_starthead_tags')
                              ->label('HTML Head Start')
                              ->language(Language::Html)
                              ->helperText(
                                'Code injected at the beginning of <head> - Meta tags, early stylesheets, preloaders',
                              ),
                          ]),

                        Tabs\Tab::make('Head End')
                          ->icon('heroicon-o-document-arrow-up')
                          ->schema([
                            CodeEditor::make('custom_endhead_tags')
                              ->label('HTML Head End')
                              ->language(Language::Html)
                              ->helperText('Code injected before </head> - Analytics, fonts, critical CSS'),
                          ]),

                        Tabs\Tab::make('Body Start')
                          ->icon('heroicon-o-document-arrow-down')
                          ->schema([
                            CodeEditor::make('custom_startbody_tags')
                              ->label('HTML Body Start')
                              ->language(Language::Html)
                              ->helperText(
                                'Code injected at the beginning of <body> - GTM noscript, body-level scripts',
                              ),
                          ]),

                        Tabs\Tab::make('Body End')
                          ->icon('heroicon-o-document-arrow-up')
                          ->schema([
                            CodeEditor::make('custom_endbody_tags')
                              ->label('HTML Body End')
                              ->language(Language::Html)
                              ->helperText(
                                'Code injected before </body> - Analytics, chat widgets, performance scripts',
                              ),
                          ]),
                      ])
                      ->contained(false),
                  ]),
              ]),
          ])
          ->columnSpan(2),

        // Enhanced Sidebar with project information and previews
        ...self::Views(),
      ])
      ->columns(3);
  }

  public static function Views(): array
  {
    return [Group::make([self::CollectorCard3D(), self::card()])->columnSpan(1)];
  }

  public static function card(): View
  {
    return View::make('ui::components.OptionCard.index')->viewData(function ($record) {
      return [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([
          'columns' => 2,
          'gap' => '9',

          // Container Padding
          'padding-left' => '36px',
          'padding-right' => '36px',
          'cards' => [
            [
              'title' => 'Edit Website',
              'description' => 'Customize Appearance',
              'icon' => 'swatch-book',
              'href' => $record ? "/v1/builder/{$record->slug}" : '#',
              'color' => 'primary',
            ],
            [
              'title' => 'URL Redirections',
              'description' => 'Configure Redirect Rules',
              'icon' => 'arrow-left-right',
              'href' => '#',
              'color' => 'success',
            ],
            [
              'title' => 'Well-Known Files',
              'description' => 'Manage Site-Wide Metadata',
              'icon' => 'text-search',
              'href' => '#',
              'color' => 'warning',
            ],
            [
              'title' => 'Clear Cache',
              'description' => 'Delete Cached Data',
              'icon' => 'trash',
              'href' => '#',
              'color' => 'danger',
            ],
          ],
        ]),
      ];
    });
  }

  public static function CollectorCard3D(): View
  {
    return View::make('ui::components.CollectorCard3D.index')->viewData(function ($record) {
      return [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([
          // Record-bound reference
          'record' => $record,
          'logoUrl' => $record?->logo_path ?? null,
          'href' => null,

          'tiltSensitivity' => 40,
          'tiltSpeed' => 800,
          'tiltScale' => 1.2,
          'flipSpeed' => 150,

          'showFrame' => true,
          'enableHover' => true,
          'enableClick' => true,
          'enableFlip' => true,
          'enableCopy' => true,

          // Primary display values
          'domain' => $record?->domain ?? 'example.com',
          'websiteName' => $record?->name ?? ($record?->title ?? 'Website Name'),
          'status' => $record?->status ?? 'draft',
          'type' => $record?->type ?? 'corporate',
          'language' => $record?->main_language ?? 'en',
          'createdAt' => $record?->created_at ? $record->created_at->format('d/m/Y') : now()->format('d/m/Y'),
          'updatedAt' => $record?->updated_at ?? now(),
        ]),
      ];
    });
  }
}
