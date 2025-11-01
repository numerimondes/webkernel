<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\UI\Resources\Views\components\BadgeTitle;

use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;

class BadgeTitle extends ComponentBase
{
    protected function define(ComponentSchema $schema): void
    {
        $schema
            // Basic props
            ->string('color')->default('')
            ->string('size')->default('sm')
            ->boolean('disabled')->default(false)
            ->string('href')->nullable()
            ->string('text')->nullable()
            ->string('arrow')->nullable()

            // Badge props
            ->string('badge')->nullable()
            ->boolean('showPing')->default(false)

            // Computed properties
            ->compute('tag', fn($config) => $config['href'] ? 'a' : 'div')
            ->compute('showPingDot', fn($config) => $config['showPing'] ?? false)

            // Badge styling
            ->baseClasses([
                'group', 'inline-flex', 'items-center', 'gap-2', 'rounded-full',
                'border', 'px-3', 'py-1.5', 'text-xs', 'font-medium',
                'ring-1', 'transition-all', 'duration-300', 'ring-inset',
                'hover:translate-y-[-1px]', 'focus:ring-2', 'focus:outline-none'
            ])

            // No color variants - using theme colors from skeleton only

            // HTML attributes
            ->dynamicAttribute('href', fn($config) => $config['tag'] === 'a' ? $config['href'] : null);
    }
}
