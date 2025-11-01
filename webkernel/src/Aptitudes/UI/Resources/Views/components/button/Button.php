<?php
declare(strict_types=1);
//webkernel/src/Aptitudes/UI/Resources/Views/components/button/Button.php
namespace Webkernel\Aptitudes\UI\Resources\Views\components\button;

use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;

class Button extends ComponentBase
{
    protected function define(ComponentSchema $schema): void
    {
        $schema
            // Basic props
            ->string('color')->default('primary')
            ->string('size')->default('md')
            ->boolean('disabled')->default(false)
            ->string('href')->nullable()
            ->string('type')->default('button')
            ->string('tooltip')->nullable()

            // SuccessSound
            ->sound('success')->default('access-allowed-tone')

            // Icon props
            ->string('icon')->nullable()
            ->enum('iconPosition')->options(['before', 'after'])->default('before')

            // Badge props
            ->string('badge')->nullable()
            ->number('notification')->nullable()

            // Computed properties
            ->compute('tag', fn($config) => $config['href'] ? 'a' : 'button')
            ->compute('showIconBefore', fn($config) => $config['icon'] && $config['iconPosition'] === 'before')
            ->compute('showIconAfter', fn($config) => $config['icon'] && $config['iconPosition'] === 'after')

            // Simple styling (Filament style)
            ->baseClasses([
                'inline-flex', 'items-center', 'justify-center', 'gap-1.5', 'rounded-lg',
                'text-sm', 'font-medium', 'transition-colors', 'duration-75', 'outline-none',
                'disabled:opacity-70', 'disabled:cursor-not-allowed', 'hover:-translate-y-0.5'
            ])

            // Size variants (Filament exact sizes)
            ->variantClasses('size', [
                'xs' => 'px-2 py-1.5 text-xs',
                'sm' => 'px-2.5 py-1.5 text-sm',
                'md' => 'px-3 py-2 text-sm',
                'lg' => 'px-3.5 py-2.5 text-sm',
                'xl' => 'px-4 py-3 text-sm',
            ])

            // Color variants - only exceptions defined explicitly (standard colors auto-generated)
            ->variantClasses('color', [
                'ghost' => 'bg-transparent text-primary hover:bg-primary/10 focus:ring-primary',
                'outline' => 'border border-primary text-primary hover:bg-primary hover:text-background focus:ring-primary',
            ])

            // HTML attributes
            ->dynamicAttribute('type', fn($config) => $config['tag'] === 'button' ? $config['type'] : null)
            ->dynamicAttribute('href', fn($config) => $config['tag'] === 'a' ? $config['href'] : null)
            ->conditionalAttribute('disabled', 'disabled', 'disabled')
            ->attribute('title', 'tooltip');
    }
}


//webkernel/src/Aptitudes/UI/Resources/Views/components/button/index.blade.php

/**
  * @php
  *use Webkernel\Aptitudes\UI\Resources\Views\components\button\Button;
  *$button = new Button($attributes->getAttributes());
  *@endphp
  *
  *<{{ $button->tag }} {!! $button->getAttributes() !!}
  *   style="position: relative;"
  *   @if($button->success) onclick="playSound('{{ $button->success }}')" @endif>
  *    @if($button->showIconBefore)
  *        <x-dynamic-component :component="'lucide-' . $button->icon" class="w-4 h-4" />
  *    @endif
  *
  *    {{ $slot }}
  *
  *    @if($button->showIconAfter)
  *        <x-dynamic-component :component="'lucide-' . $button->icon" class="w-4 h-4" />
  *    @endif
  *
  *    @if($button->badge)
  *        <span class="ml-0.3 min-w-[18px] h-[18px] px-1 bg-white/20 text-white text-xs rounded flex items-center justify-center">
  *            {{ $button->badge }}
  *        </span>
  *    @endif
  *
  *    @if(!empty($button->notification))
  *        @if($button->notification === true || $button->notification === 1)
  *            <span style="position: absolute; top: -5px; right: -5px; width: 10px; height: 10px; background-color: #ef4444; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid white;"></span>
  *        @else
  *            <span style="position: absolute; top: -8px; right: -8px; min-width: 16px; min-height: 16px; padding: 2px 4px; background: linear-gradient(135deg, #f87171, #dc2626); color: white; font-size: 10px; line-height: 1; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
  *                {{ $button->notification }}
  *            </span>
  *        @endif
  *    @endif
  *</{{ $button->tag }}>
  *
 */
