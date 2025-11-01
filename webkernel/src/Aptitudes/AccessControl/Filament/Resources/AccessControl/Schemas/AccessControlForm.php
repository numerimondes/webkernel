<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas;

use Filament\Forms\Components\{Select, Slider};
use Filament\Forms\Components\Slider\Enums\{Behavior, PipsMode};
use Filament\Schemas\Components\{Grid, Section};
use Filament\Schemas\Components\View as ViewComponent;
use Filament\Schemas\Schema;
use Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Sections\GroupDetails;
use Webkernel\Aptitudes\UI\Resources\Views\components\HeroCard\CreateHeroCard;

/**
 * Access Control Form Schema
 *
 * Provides a comprehensive form structure for managing access control groups,
 * including group identification, user assignments, priority configuration,
 * and granular permission management across discovered resources, panels,
 * pages, widgets, custom privileges, and wildcard patterns.
 *
 * @package Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas
 */
final class AccessControlForm
{
  /**
   * Configure the main schema with all form components
   *
   * @param Schema $schema The schema instance to configure
   * @return Schema The fully configured schema with all components
   */
  public static function configure(Schema $schema): Schema
  {
    return $schema->components([
      self::getMainSection(),
      self::createAccessControlHeroCard(),
      AccessControlTabs::getTabs(),
    ]);
  }

  /**
   * Constructs the main section with group configuration
   *
   * @return Grid The main grid component with all configuration sections
   */
  private static function getMainSection(): Grid
  {
    return Grid::make(12)
      ->schema([
        // Group Details: spans 9 columns
        GroupDetails::main(),

        // System Flags: spans 3 columns
        GroupDetails::systemFlags(),

        // Users: spans 9 columns
        self::getUsersSection(),

        // Priority: spans 3 columns
        self::getPrioritySection(),
      ])
      ->columnSpanFull();
  }

  /**
   * Creates the HeroCard component for access control context
   *
   * @return ViewComponent The configured HeroCard component for access control
   */
  private static function createAccessControlHeroCard(): ViewComponent
  {
    $backgroundImage = module_image(
      'module://media-store/Resources/Assets/images/wallpapers/gradients/others/gradient-squares/aron-yigin-Ba6CjJFZMb4-unsplash.jpg',
    );

    return CreateHeroCard::make(
      title: 'Access Control and Full Permission Management',
      description: 'Configure security groups, assign users, and manage granular permissions across resources, panels, pages, widgets, and custom privileges.',
      backgroundImage: $backgroundImage,
      overlayOpacity: 0.75,
      size: 'md',
    )->columnSpanFull();
  }

  /**
   * Creates the user relationship management section
   *
   * @return Section The section for managing user attachments
   */
  private static function getUsersSection(): Section
  {
    return Section::make('Users')
      ->schema([
        Select::make('users')
          ->label('Attached Users')
          ->relationship('users', 'name')
          ->multiple()
          ->searchable()
          ->preload()
          ->helperText('Select users who belong to this access control group'),
      ])
      ->columnSpan(9)
      ->compact();
  }

  /**
   * Creates the priority configuration section
   *
   * @return Section The section containing priority configuration slider
   */
  private static function getPrioritySection(): Section
  {
    return Section::make('Priority Level')
      ->schema([
        Slider::make('priority')
          ->label('Higher priority takes precedence in conflicts')
          ->default(0)
          ->minValue(0)
          ->maxValue(1000)
          ->required()
          ->tooltips(false)
          ->fillTrack()
          ->step(100)
          ->pips(PipsMode::Steps)
          ->pipsValues([0, 25, 50, 75, 100])
          ->behavior([Behavior::Tap, Behavior::Drag, Behavior::SmoothSteps])
          ->steppedPips()
          ->helperText(''),
      ])
      ->columnSpan(3)
      ->compact();
  }
}
