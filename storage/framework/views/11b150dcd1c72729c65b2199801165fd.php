
<div class="flex flex-col min-h-screen">
    <?php if ($__env->exists('enjoy-the-world::blocks.navigation-before')) echo $__env->make('enjoy-the-world::blocks.navigation-before', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if ($__env->exists('enjoy-the-world::blocks.navigation')) echo $__env->make('enjoy-the-world::blocks.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1 relative">
        <?php if ($__env->exists('website-builder::live-website.layouts.partials.flash-messages')) echo $__env->make('website-builder::live-website.layouts.partials.flash-messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="animate-fade-in">

        <?php if ($__env->exists('enjoy-the-world::pages.01-home.01-hero')) echo $__env->make('enjoy-the-world::pages.01-home.01-hero', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if ($__env->exists('enjoy-the-world::pages.01-home.02-welcome-paradise')) echo $__env->make('enjoy-the-world::pages.01-home.02-welcome-paradise', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if ($__env->exists('enjoy-the-world::pages.01-home.03-exclusivity-enjoysxm')) echo $__env->make('enjoy-the-world::pages.01-home.03-exclusivity-enjoysxm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if ($__env->exists('enjoy-the-world::pages.01-home.04-map')) echo $__env->make('enjoy-the-world::pages.01-home.04-map', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if ($__env->exists('enjoy-the-world::pages.01-home.05-featured-activities')) echo $__env->make('enjoy-the-world::pages.01-home.05-featured-activities', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->yieldContent('content'); ?>
            <?php echo $__env->yieldPushContent('content'); ?>
        </div>
    </main>
    <?php if ($__env->exists('enjoy-the-world::blocks.footer')) echo $__env->make('enjoy-the-world::blocks.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/platform/EnjoyTheWorld/Resources/Views/main.blade.php ENDPATH**/ ?>