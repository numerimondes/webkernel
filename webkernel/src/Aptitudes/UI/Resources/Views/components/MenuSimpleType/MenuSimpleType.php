<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\UI\Resources\Views\components\MenuSimpleType;

use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;

class MenuSimpleType extends ComponentBase
{
    protected function define(ComponentSchema $schema): void
    {
        $schema
            // Navigation props
            ->string('logoUrl')->default('/numerimondes')
            ->string('logoImage')->default('https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png')
            ->string('logoText')->default('Numerimondes')
            ->string('githubUrl')->default('https://github.com/numerimondes')
            ->array('navigationItems')->default([])
            ->boolean('showSearch')->default(true)
            ->boolean('showThemeToggle')->default(true)
            ->boolean('showGitHub')->default(true)

            // Computed properties
            ->compute('tag', fn($config) => 'header')

            // Navigation styling - Dynamic classes that adapt to any theme
            ->baseClasses([
                'w-full', 'sticky', 'top-0', 'z-50', 'bg-background', 'backdrop-blur-md', 'shadow-sm',  'transition-all', 'duration-300'
            ]);
    }
}
