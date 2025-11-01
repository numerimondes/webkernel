<?php
use Webkernel\Aptitudes\UI\Resources\Views\components\HeroCard\HeroCard;
$hero_card = new HeroCard($attributes->getAttributes());
?>
<<?php echo e($hero_card->tag); ?> style="
    position: relative;
    width: 100%;
    background-image: url('<?php echo e($backgroundImage ?? $hero_card->backgroundImage); ?>');
    background-size: cover;
    background-position: center;
    padding: 2rem;
    border-radius: 1rem;
    overflow: hidden;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    -webkit-user-drag: none;
    -khtml-user-drag: none;
    -moz-user-drag: none;
    -o-user-drag: none;
    pointer-events: auto;
">
    <!-- Overlay -->
    <div style="
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, <?php echo e($hero_card->overlayOpacity ?? 0.2); ?>);
        user-select: none;
        pointer-events: none;
    "></div>
    <!-- Content -->
    <div style="
        position: relative;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    ">
        <h2 style="
            font-size: 34px;
            font-weight: 400;
            text-align: left;
            color: white;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            margin: 0;
            user-select: none;
            -webkit-user-select: none;
        ">
            <?php echo e($title ?? $hero_card->title); ?>

        </h2>
        <p style="
            margin-top: 0.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            margin-bottom: 0;
            user-select: none;
            -webkit-user-select: none;
        ">
            <?php echo e($description ?? $hero_card->description); ?>

        </p>
    </div>
</<?php echo e($hero_card->tag); ?>>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/UI/Resources/Views/components/HeroCard/index.blade.php ENDPATH**/ ?>