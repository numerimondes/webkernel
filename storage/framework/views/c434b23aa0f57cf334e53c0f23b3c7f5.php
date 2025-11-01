<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'backgroundImageLight' => null,
    'backgroundImageDark' => null,
    'enableLightBackground' => true,
    'enableDarkBackground' => true,
    'cardMaxWidth' => '560px',
    'cardPadding' => '3rem',
    'cardBorderRadius' => '12px',
    'transitionDuration' => '1.5s',
    'fadeInDuration' => '1s',
    'darkOverlayOpacity' => '0.8',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'backgroundImageLight' => null,
    'backgroundImageDark' => null,
    'enableLightBackground' => true,
    'enableDarkBackground' => true,
    'cardMaxWidth' => '560px',
    'cardPadding' => '3rem',
    'cardBorderRadius' => '12px',
    'transitionDuration' => '1.5s',
    'fadeInDuration' => '1s',
    'darkOverlayOpacity' => '0.8',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $backgroundImageLightMode = $backgroundImageLight ?? asset('images/light-login.png');
    $backgroundImageDarkMode = $backgroundImageDark ?? asset('images/dark-login.png');
?>

<?php $__env->startPush('styles'); ?>
<link rel="preload" as="image" href="<?php echo e($backgroundImageLightMode); ?>">
<link rel="preload" as="image" href="<?php echo e($backgroundImageDarkMode); ?>">
<style>
    :root {
        --bg-light: var(--primary-200);
        --bg-fi-simple-main: var(--primary-50);
        --bg-fi-simple-main-dark: var(--gray-900) !important;
        --shadow-light: rgba(0, 0, 0, 0.08);
        --border-light: rgba(0, 0, 0, 0.06);
        --border-inner-light: rgba(255, 255, 255, 0.8);
        --shadow-dark: rgba(0, 0, 0, 0.4);
        --border-dark: rgba(255, 255, 255, 0.08);
        --border-inner-dark: rgba(255, 255, 255, 0.05);
        --z-background: -2;
        --z-card-shadow: 0;
        --z-card: 1;
        --z-content: 10;
        --simple-main-max-width: <?php echo e($cardMaxWidth); ?>;
        --bg-image-light: url('<?php echo e($backgroundImageLightMode); ?>');
        --bg-image-dark: url('<?php echo e($backgroundImageDarkMode); ?>');
    }

    html,
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: var(--bg-image-light) center/cover no-repeat fixed;
        z-index: var(--z-background);
        opacity: 1;

    }

    body.transition-enabled::before,
    body.transition-enabled::after {
        transition: opacity <?php echo e($transitionDuration); ?> ease-in-out;
    }

    body::after {
        content: "";
        position: fixed;
        inset: 0;
        background: var(--bg-image-dark) center/cover no-repeat fixed;
        z-index: var(--z-background);
        opacity: 0;
        transition: opacity <?php echo e($transitionDuration); ?> ease-in-out;
        filter: brightness(<?php echo e($darkOverlayOpacity); ?>);
    }

    html.dark body::before,
    .dark body::before {
        opacity: 0;
    }

    html.dark body::after,
    .dark body::after {
        opacity: 1;
    }

    @media (max-width: 640px) {
        .fi-simple-main {
            --tw-ring-shadow: none;
        }
    }

    .fi-simple-layout {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        overflow: visible;
    }

    .fi-simple-main-ctn {
        width: 100%;
        flex-grow: 0;
        height: auto;
        max-width: var(--simple-main-max-width);
        z-index: var(--z-content);
    }

    .fi-simple-main {
        position: relative;
        border-radius: <?php echo e($cardBorderRadius); ?>;
        border: 0.5px solid var(--border-light);
        animation: fadeIn <?php echo e($fadeInDuration); ?> ease-out;
        padding: <?php echo e($cardPadding); ?> !important;
        z-index: var(--z-card);
        box-shadow:
            0 2px 4px color-mix(in srgb, var(--primary-100) 25%, transparent),
            0 8px 16px color-mix(in srgb, var(--primary-200) 20%, transparent),
            0 32px 64px -12px color-mix(in srgb, var(--primary-400) 15%, transparent),
            inset 0 0 0 0.5px color-mix(in srgb, var(--primary-300) 30%, transparent);
    }

    html:not(.dark) .fi-simple-main {
        background-color: var(--bg-fi-simple-main) !important;
        box-shadow:
            0 2px 4px color-mix(in srgb, var(--primary-100) 25%, transparent),
            0 8px 16px color-mix(in srgb, var(--primary-200) 20%, transparent),
            0 32px 64px -12px color-mix(in srgb, var(--primary-400) 15%, transparent),
            inset 0 0 0 0.5px color-mix(in srgb, var(--primary-300) 30%, transparent);
    }

    html:where(.dark) .fi-simple-main {
        background-color: var(--bg-fi-simple-main-dark) !important;
    }

    :where(.dark) .fi-simple-main {
        border: 0.5px solid var(--border-dark);
    }

    .fi-simple-main::before {
        content: "";
        position: absolute;
        border-radius: <?php echo e($cardBorderRadius); ?>;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        background: var(--shadow-light);
        border: 0.5px solid var(--border-light);
        pointer-events: none;
        z-index: var(--z-card-shadow);
        transition: all <?php echo e($transitionDuration); ?> ease;
    }

    :where(.dark) .fi-simple-main::before {
        background: var(--shadow-dark);
        border: 0.5px solid var(--border-dark);
    }

    .fi-simple-main::after {
        content: "";
        position: absolute;
        border-radius: <?php echo e($cardBorderRadius); ?>;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        background: transparent;
        pointer-events: none;
        z-index: var(--z-card-shadow);
    }

    .fi-simple-page-content {
        position: relative;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-up {
        animation: slideUp <?php echo e($fadeInDuration); ?> ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/Base/Resources/Views/filament/pages/fi-simple.blade.php ENDPATH**/ ?>