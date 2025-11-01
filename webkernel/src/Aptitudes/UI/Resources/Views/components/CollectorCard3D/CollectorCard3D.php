<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\UI\Resources\Views\components\CollectorCard3D;

use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;

class CollectorCard3D extends ComponentBase
{
    protected function define(ComponentSchema $schema): void
    {
        $schema
            // Record data props
            ->mixed('record')->nullable()
            ->string('logoUrl')->nullable()
            ->string('domain')->default('example.com')
            ->string('websiteName')->default('Website Name')
            ->string('status')->default('maintenance')
            ->string('type')->default('Type will appear here')
            ->string('language')->default('Language will appear here')
            ->string('createdAt')->default('Creation date')
            ->string('updatedAt')->default('Last update')
            ->string('href')->nullable()
            ->string('backgroundStyle')->nullable()
            ->compute('tag', fn($config) => !empty($config['href']) ? 'a' : 'div')

            // Card behavior props
            ->boolean('enableHover')->default(true)
            ->boolean('enableClick')->default(true)
            ->boolean('enableFlip')->default(true)
            ->boolean('enableCopy')->default(true)

            // Tilt control properties
            ->integer('tiltSensitivity')->default(20) // Max tilt angle in degrees
            ->integer('tiltSpeed')->default(100) // Transition speed in ms
            ->float('tiltScale')->default(1.02) // Scale factor on hover
            ->integer('flipSpeed')->default(600) // Flip animation speed in ms

            // Visual customization
            ->string('cardSize')->default('standard') // standard, compact, large
            ->boolean('showFrame')->default(true)

            // Computed properties
            ->compute('uniqueId', fn($config) => $config['record']?->id ?? rand(1000, 9999))
            ->compute('serialNumber', fn($config) => 'NM-' . str_pad((string)($config['record']?->id ?? rand(1000, 9999)), 4, '0', STR_PAD_LEFT))

            ->compute('finalLogoUrl', function($config) {
                return $config['record']?->logo_path
                    ?? $config['logoUrl']
                    ?? 'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png';
            })

            ->compute('finalDomain', fn($config) => $config['record']?->domain ?? $config['domain'])
            ->compute('finalWebsiteName', fn($config) => $config['record']?->name ?? $config['record']?->title ?? $config['websiteName'])
            ->compute('finalStatus', fn($config) => $config['record']?->status ?? $config['status'])
            ->compute('finalType', fn($config) => $config['record']?->type ?? $config['type'])
            ->compute('finalLanguage', fn($config) => $config['record']?->main_language ?? $config['language'])

            ->compute('finalCreatedAt', function($config) {
                if ($config['record']?->created_at) {
                    return $config['record']->created_at->format('d/m/Y');
                }
                return $config['createdAt'];
            })

            ->compute('finalUpdatedAt', function($config) {
                if ($config['record']?->updated_at) {
                    return $config['record']->updated_at->diffForHumans();
                }
                return $config['updatedAt'];
            })

            // Parse domain into parts for card number display
            ->compute('domainParts', function($config) {
                $domain = $config['finalDomain'];

                // Remove protocol if present
                $domain = preg_replace('/^https?:\/\//', '', $domain);

                // Split domain into parts
                $parts = explode('.', $domain);

                if (count($parts) >= 2) {
                    $extension = array_pop($parts); // .com, .fr, etc.
                    $mainDomain = array_pop($parts); // domain name
                    $subdomain = !empty($parts) ? implode('.', $parts) : 'www'; // subdomain or www

                    return [
                        'subdomain' => $subdomain,
                        'domain' => $mainDomain,
                        'extension' => $extension,
                        'full' => $subdomain . '.' . $mainDomain . '.' . $extension
                    ];
                }

                // Fallback for malformed domains
                return [
                    'subdomain' => 'www',
                    'domain' => $domain,
                    'extension' => 'com',
                    'full' => 'www.' . $domain . '.com'
                ];
            })

            ->compute('statusLabel', function($config) {
                $status = $config['finalStatus'];
                $labels = [
                    'staging' => 'Staging',
                    'draft' => 'Draft',
                    'published' => 'Live',
                    'maintenance' => 'Maintenance',
                    'archived' => 'Archived',
                ];

                return $labels[$status] ?? 'Draft';
            })

            ->compute('cardClasses', function($config) {
                $classes = ['numerimondes-website-card'];

                if ($config['cardSize'] !== 'standard') {
                    $classes[] = 'size-' . $config['cardSize'];
                }

                return implode(' ', $classes);
            })

            // Base classes for styling
            ->baseClasses([
                'numerimondes-website-card-wrapper'
            ])

            ->conditionalAttribute('data-enable-flip', fn($config) => $config['enableFlip'] ? 'true' : 'false')
            ->conditionalAttribute('data-enable-copy', fn($config) => $config['enableCopy'] ? 'true' : 'false')
            ->conditionalAttribute('data-unique-id', fn($config) => $config['uniqueId'])
            ->conditionalAttribute('data-tilt-sensitivity', fn($config) => $config['tiltSensitivity'])
            ->conditionalAttribute('data-tilt-speed', fn($config) => $config['tiltSpeed'])
            ->conditionalAttribute('data-tilt-scale', fn($config) => $config['tiltScale'])
            ->conditionalAttribute('data-flip-speed', fn($config) => $config['flipSpeed'])

            ->dynamicAttribute('href', fn($config) => $config['tag'] === 'a' ? ($config['href'] ?? null) : null)
            ->dynamicAttribute('style', fn($config) => $config['backgroundStyle'] ?? null);
    }
}
