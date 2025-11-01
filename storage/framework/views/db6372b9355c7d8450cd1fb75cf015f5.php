<?php
use Webkernel\Aptitudes\UI\Resources\Views\components\BadgeTitle\BadgeTitle;
$badge_title = new BadgeTitle($attributes->getAttributes());
?>

<<?php echo e($badge_title->tag); ?> <?php echo $badge_title->getAttributes(); ?>>
    <?php if($badge_title->showPingDot): ?>
        <span class="relative inline-flex h-2 w-2">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary-dynamic/70 opacity-75"></span>
            <span class="relative inline-flex h-2 w-2 rounded-full bg-primary-dynamic"></span>
        </span>
    <?php endif; ?>

    <span class="pointer-events-none select-none">
        <?php echo e($badge_title->text ?? $slot); ?>

    </span>

    <?php if($badge_title->arrow): ?>
        <span class="ml-1 inline-flex items-center text-primary-dynamic transition-transform group-hover:translate-x-0.5" aria-hidden="true">
            <?php echo e($badge_title->arrow); ?>

        </span>
    <?php endif; ?>
</<?php echo e($badge_title->tag); ?>>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/UI/Resources/Views/components/BadgeTitle/index.blade.php ENDPATH**/ ?>