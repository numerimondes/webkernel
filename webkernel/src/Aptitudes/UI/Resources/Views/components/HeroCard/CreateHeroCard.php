<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\UI\Resources\Views\components\HeroCard;
use Filament\Schemas\Components\View;
class CreateHeroCard
{
  /**
   * Creates a reusable HeroCard component
   */
  public static function make(
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
}
