<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Sections;

use Filament\Schemas\Components\View as ViewComponent;
use Webkernel\Aptitudes\UI\Resources\Views\components\HeroCard\CreateHeroCard;

/**
 * Hero Card Builder
 *
 * Builds the hero card component for access control context.
 *
 * @package Webkernel\Aptitudes\AccessControl\Filament\Resources\AccessControl\Schemas\Sections
 */
final class HeroCard
{
  /**
   * Build the hero card component
   *
   * @return ViewComponent The configured HeroCard component
   */
  public static function build(): ViewComponent
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
}
