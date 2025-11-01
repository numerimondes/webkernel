<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n\Resources\Views\components\LanguageSelector;

use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;
use Webkernel\Aptitudes\I18n\Models\Language;

class LanguageSelector extends ComponentBase
{
    protected function define(ComponentSchema $schema): void
    {
        $schema
            ->string('size')->default('md')
            ->boolean('showFlags')->default(true)
            ->string('changeUrl')->default('/lang')

            // Computed properties pour toutes les données
            ->compute('selectorData', fn($config) => Language::getSelectorData())
            ->compute('languages', fn($config) => $config['selectorData']['languages'] ?? collect([]))
            ->compute('currentCode', fn($config) => $config['selectorData']['current_code'] ?? 'en')
            ->compute('currentLabel', fn($config) => $config['selectorData']['current_label'] ?? 'English')
            ->compute('currentLanguage', fn($config) => $config['selectorData']['current_language'] ?? null)

            // Classes de base pour le conteneur du trigger
            ->baseClasses(['flex', 'items-center', 'justify-center', 'rounded-md', 'transition-colors', 'hover:bg-gray-50', 'dark:hover:bg-white/5', 'cursor-pointer'])

            // Variantes de taille
            ->variantClasses('size', [
                'sm' => 'h-7 w-7',
                'md' => 'h-8 w-8',
                'lg' => 'h-9 w-9',
            ])

            // Attributs data pour le JavaScript
            ->attribute('data-component', 'language-selector')
            ->dynamicAttribute('data-change-url', fn($config) => $config['changeUrl'])
            ->dynamicAttribute('data-show-flags', fn($config) => $config['showFlags'] ? 'true' : 'false');
    }

}
