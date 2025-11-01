
<?php
    use Webkernel\Aptitudes\UI\Resources\Views\components\ThemeSwitcher\ThemeSwitcher;
    if (!isset($attributes)) {$attributes = new \Illuminate\View\ComponentAttributeBag();}
    $theme_switcher = new ThemeSwitcher($attributes->getAttributes());
    $availableModes = $theme_switcher->getAvailableModes();
    $modeConfig = $theme_switcher->getModeConfig();
?>


<style>
    /* Theme switcher button styles using webkernel theme classes */
    .fi-theme-switcher-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.15s ease-out;
        flex-shrink: 0 !important;
        min-width: 0;
        min-height: 0;
        aspect-ratio: 1;
        box-sizing: border-box;
        z-index: 10;
        isolation: isolate;
    }

    /* Force square dimensions for each size */
    .fi-theme-switcher-btn.w-8 {
        width: 2rem !important;
        height: 2rem !important;
    }

    .fi-theme-switcher-btn.w-10 {
        width: 2.5rem !important;
        height: 2.5rem !important;
    }

    .fi-theme-switcher-btn.w-12 {
        width: 3rem !important;
        height: 3rem !important;
    }

    /* Container styling to prevent overflow */
    .fi-theme-switcher {
        position: relative;
        z-index: 10;
        isolation: isolate;
        contain: layout;
    }

    /* Active state for buttons */
    .fi-theme-switcher-btn.fi-active {
        background-color: var(--color-brand-primary);
        color: var(--color-background-primary);
    }

    .fi-theme-switcher-btn.fi-active svg {
        color: var(--color-background-primary);
    }

    /* Dropdown specific styles */
    .fi-theme-switcher-dropdown {
        background-color: var(--color-background-primary);
        border: 1px solid var(--color-border-primary);
        color: var(--color-text-primary);
    }

    .fi-theme-switcher-dropdown button:hover {
        background-color: var(--color-background-secondary);
    }

    /* Inline variant active state */
    .fi-theme-switcher-inline button.active {
        color: var(--color-brand-primary);
        background-color: var(--color-background-secondary);
    }

    /* Hover states for theme switcher buttons */
    .fi-theme-switcher-btn:hover {
        background-color: var(--color-background-secondary) !important;
    }

    .fi-theme-switcher-btn:hover svg {
        color: var(--color-text-primary) !important;
    }
</style>


<<?php echo e($theme_switcher->tag); ?> <?php echo $theme_switcher->getAttributes(); ?> x-data="{
    theme: null,
    availableModes: <?php echo \Illuminate\Support\Js::from($availableModes)->toHtml() ?>,
    modeConfig: <?php echo \Illuminate\Support\Js::from($modeConfig)->toHtml() ?>
}" x-init="
    theme = localStorage.getItem('theme') || '<?php echo e($theme_switcher->defaultTheme); ?>';
    $watch('theme', (newTheme) => {
        localStorage.setItem('theme', newTheme);
        $dispatch('theme-changed', newTheme);

        // Force update of parent layout if it exists
        if (window.Alpine && window.Alpine.store) {
            window.Alpine.store('theme', newTheme);
        }

        // Also dispatch a custom event for any other listeners
        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: newTheme,
            bubbles: true
        }));
    });
"
    class="fi-theme-switcher <?php echo e($theme_switcher->variant === 'button' ? 'flex items-center gap-0.5 flex-shrink-0 relative z-10' : ($theme_switcher->variant === 'dropdown' ? 'relative flex-shrink-0 z-10' : 'flex items-center space-x-2 flex-shrink-0 relative z-10')); ?>">

    <?php if($theme_switcher->variant === 'button'): ?>
        

        <?php $__currentLoopData = $availableModes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $config = $modeConfig[$mode] ?? ['icon' => 'sun', 'title' => ucfirst($mode) . ' mode', 'label' => ucfirst($mode)];
            ?>

            <button @click="(theme = '<?php echo e($mode); ?>') && close()" x-bind:class="{ 'fi-active': theme === '<?php echo e($mode); ?>' }"
                class="fi-theme-switcher-btn <?php echo e($theme_switcher->size === 'sm' ? 'w-8 h-8 text-sm' : ($theme_switcher->size === 'lg' ? 'w-12 h-12 text-lg' : 'w-10 h-10 text-base')); ?> rounded-lg border transition-all duration-200 flex items-center justify-center flex-shrink-0"
                style="border-color: var(--color-border-primary); background-color: var(--color-background-primary); color: var(--color-text-primary);"
                title="<?php echo e($config['title']); ?>">
                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $config['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                <?php if($theme_switcher->showLabels): ?>
                    <span class="ml-2 text-sm"><?php echo e($config['label']); ?></span>
                <?php endif; ?>
            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php elseif($theme_switcher->variant === 'dropdown'): ?>
        
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="fi-theme-switcher-btn <?php echo e($theme_switcher->size === 'sm' ? 'w-8 h-8 text-sm' : ($theme_switcher->size === 'lg' ? 'w-12 h-12 text-lg' : 'w-10 h-10 text-base')); ?> rounded-lg border transition-all duration-200 flex items-center justify-center flex-shrink-0"
                style="border-color: var(--color-border-primary); background-color: var(--color-background-primary); color: var(--color-text-primary);"
                title="Theme switcher">
                <?php $__currentLoopData = $availableModes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $config = $modeConfig[$mode] ?? ['icon' => 'sun', 'title' => ucfirst($mode) . ' mode', 'label' => ucfirst($mode)];
                    ?>
                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $config['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'theme === \''.e($mode).'\'','class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </button>

            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="fi-theme-switcher-dropdown absolute top-full right-0 mt-2 w-48 rounded-lg shadow-lg z-50"
                style="background-color: var(--color-background-primary); border: 1px solid var(--color-border-primary);">
                <?php $__currentLoopData = $availableModes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $config = $modeConfig[$mode] ?? ['icon' => 'sun', 'title' => ucfirst($mode) . ' mode', 'label' => ucfirst($mode)];
                    ?>

                    <button @click="(theme = '<?php echo e($mode); ?>') && (open = false)"
                        class="w-full px-4 py-2 text-left text-sm flex items-center gap-3 transition-colors duration-150"
                        style="color: var(--color-text-primary);"
                        x-bind:style="theme === '<?php echo e($mode); ?>' ? 'background-color: var(--color-background-secondary); color: var(--color-text-primary);' : 'color: var(--color-text-primary);'"
                        x-on:mouseenter="$el.style.backgroundColor = 'var(--color-background-secondary)'"
                        x-on:mouseleave="if (theme !== '<?php echo e($mode); ?>') $el.style.backgroundColor = 'transparent'">
                        <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $config['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                        <span><?php echo e($config['label']); ?></span>
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-check'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'theme === \''.e($mode).'\'','class' => 'w-4 h-4 ml-auto text-primary']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

    <?php elseif($theme_switcher->variant === 'inline'): ?>
        
        <?php $__currentLoopData = $availableModes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $config = $modeConfig[$mode] ?? ['icon' => 'sun', 'title' => ucfirst($mode) . ' mode', 'label' => ucfirst($mode)];
            ?>

            <button @click="theme = '<?php echo e($mode); ?>'" x-bind:class="{ 'active': theme === '<?php echo e($mode); ?>' }"
                class="fi-theme-switcher-inline px-3 py-2 text-sm font-medium transition-colors duration-150"
                style="color: var(--color-text-secondary);"
                x-bind:style="theme === '<?php echo e($mode); ?>' ? 'color: var(--color-brand-primary); background-color: var(--color-background-secondary);' : 'color: var(--color-text-secondary);'"
                x-on:mouseenter="if (theme !== '<?php echo e($mode); ?>') $el.style.color = 'var(--color-text-primary)'"
                x-on:mouseleave="if (theme !== '<?php echo e($mode); ?>') $el.style.color = 'var(--color-text-secondary)'"
                title="<?php echo e($config['title']); ?>">
                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $config['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 inline mr-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                <?php echo e($config['label']); ?>

            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</<?php echo e($theme_switcher->tag); ?>>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/UI/Resources/Views/components/ThemeSwitcher/index.blade.php ENDPATH**/ ?>