<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\UI\Resources\Views\components\HeroCard;
use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;
class HeroCard extends ComponentBase
{
  protected function define(ComponentSchema $schema): void
  {
    $schema
      // Hero Card props
      ->string('title')
      ->default('Hero Title')
      ->string('description')
      ->default('Hero description text')
      ->string('backgroundImage')
      ->default(
        'https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
      )
      ->string('href')
      ->nullable()
      ->number('overlayOpacity')
      ->default(0.4)
      ->string('size')
      ->default('md')
      // Computed properties - seulement les essentiels
      ->compute('tag', fn($config) => $config['href'] ? 'a' : 'div')
      ->compute(
        'overlayStyle',
        fn($config) => 'background-color: rgba(0, 0, 0, ' . ($config['overlayOpacity'] ?? 0.2) . ');',
      )
      ->compute(
        'backgroundStyle',
        fn($config) => "background-image: url('" . ($config['backgroundImage'] ?? '') . "');",
      )
      // Base styling for hero card
      ->baseClasses([
        'relative',
        'w-full',
        'bg-cover',
        'bg-center',
        'rounded-2xl',
        'overflow-hidden',
        'transition-transform',
        'duration-300',
        'hover:scale-105',
      ])
      // Size variants
      ->variantClasses('size', [
        'sm' => 'p-4',
        'md' => 'p-8',
        'lg' => 'p-12',
        'xl' => 'p-16',
      ])
      // HTML attributes
      ->dynamicAttribute('href', fn($config) => $config['tag'] === 'a' ? $config['href'] : null)
      ->dynamicAttribute('style', fn($config) => $config['backgroundStyle']);
  }
}
