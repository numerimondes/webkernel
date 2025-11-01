<?php

declare(strict_types=1);

namespace Webkernel\Aptitudes\UI\Resources\Views\components\ThemeSwitcher;

use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;

class ThemeSwitcher extends ComponentBase
{
    protected function define(ComponentSchema $schema): void
    {
        $schema
            // Theme Switcher specific props
            ->enum('variant')->options(['button', 'dropdown', 'inline'])->default('button')
            ->boolean('showLabels')->default(false)
            ->string('size')->default('md')
            ->string('defaultTheme')->default('system')

            // Basic props (inherited)
            ->boolean('disabled')->default(false)
            ->string('href')->nullable()
            ->string('type')->default('button')
            ->string('tooltip')->nullable()

            // Icon props
            ->string('icon')->nullable()
            ->enum('iconPosition')->options(['before', 'after'])->default('before')

            // Badge props
            ->string('badge')->nullable()
            ->number('notification')->nullable()

            // Computed properties
            ->compute('tag', fn ($config) => 'div') // Always div for theme switcher
            ->compute('showIconBefore', fn ($config) => $config['icon'] && $config['iconPosition'] === 'before')
            ->compute('showIconAfter', fn ($config) => $config['icon'] && $config['iconPosition'] === 'after')

            // Theme Switcher base styling
            ->baseClasses([

            ])

            // Size variants for theme switcher buttons
            ->variantClasses('size', [
                'sm' => 'w-8 h-8 text-sm',
                'md' => 'w-10 h-10 text-base',
                'lg' => 'w-12 h-12 text-lg',
            ])

            // Variant container classes
            ->variantClasses('variant', [
                'button' => 'flex items-center space-x-1',
                'dropdown' => 'relative',
                'inline' => 'flex items-center space-x-2',
            ])

            // HTML attributes
            ->dynamicAttribute('type', fn ($config) => $config['tag'] === 'button' ? $config['type'] : null)
            ->dynamicAttribute('href', fn ($config) => $config['tag'] === 'a' ? $config['href'] : null)
            ->conditionalAttribute('disabled', 'disabled', 'disabled')
            ->attribute('title', 'tooltip');
    }

    /**
     * Get available theme modes from the current theme
     */
    public function getAvailableModes(): array
    {
        try {
            // Try to get the current theme from the active project
            $theme = \Webkernel\Aptitudes\WebsiteBuilder\Models\Theme::getActiveTheme(1);

            return $theme ? $theme->getAvailableModes() : ['light', 'dark', 'system'];
        } catch (\Exception $e) {
            // Fallback to default modes
            return ['light', 'dark', 'system'];
        }
    }

    /**
     * Get mode configuration from the current theme
     */
    public function getModeConfig(): array
    {
        try {
            // Try to get the current theme from the active project
            $theme = \Webkernel\Aptitudes\WebsiteBuilder\Models\Theme::getActiveTheme(1);

            return $theme ? $theme->getModeConfig() : [
                'light' => ['icon' => 'sun', 'title' => 'Light mode', 'label' => 'Light'],
                'dark' => ['icon' => 'moon', 'title' => 'Dark mode', 'label' => 'Dark'],
                'system' => ['icon' => 'monitor', 'title' => 'System mode', 'label' => 'System'],
            ];
        } catch (\Exception $e) {
            // Fallback to default mode config
            return [
                'light' => ['icon' => 'sun', 'title' => 'Light mode', 'label' => 'Light'],
                'dark' => ['icon' => 'moon', 'title' => 'Dark mode', 'label' => 'Dark'],
                'system' => ['icon' => 'monitor', 'title' => 'System mode', 'label' => 'System'],
            ];
        }
    }
}
