<?php
use Webkernel\Aptitudes\UI\Resources\Views\components\button\Button;
$button = new Button($attributes->getAttributes());
?>

<<?php echo e($button->tag); ?> <?php echo $button->getAttributes(); ?>

   style="position: relative;"
   <?php if($button->success): ?> onclick="playSound('<?php echo e($button->success); ?>')" <?php endif; ?>>
    <?php if($button->showIconBefore): ?>
        <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $button->icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
    <?php endif; ?>

    <?php echo e($slot); ?>


    <?php if($button->showIconAfter): ?>
        <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'lucide-' . $button->icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
    <?php endif; ?>

    <?php if($button->badge): ?>
        <span class="ml-0.3 min-w-[18px] h-[18px] px-1 bg-white/20 text-white text-xs rounded flex items-center justify-center">
            <?php echo e($button->badge); ?>

        </span>
    <?php endif; ?>

    <?php if(!empty($button->notification)): ?>
        <?php if($button->notification === true || $button->notification === 1): ?>
            <span style="position: absolute; top: -5px; right: -5px; width: 10px; height: 10px; background-color: #ef4444; border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid white;"></span>
        <?php else: ?>
            <span style="position: absolute; top: -8px; right: -8px; min-width: 16px; min-height: 16px; padding: 2px 4px; background: linear-gradient(135deg, #f87171, #dc2626); color: white; font-size: 10px; line-height: 1; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <?php echo e($button->notification); ?>

            </span>
        <?php endif; ?>
    <?php endif; ?>
</<?php echo e($button->tag); ?>>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/UI/Resources/Views/components/button/index.blade.php ENDPATH**/ ?>