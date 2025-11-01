<header  class="flex items-center justify-between h-full w-full max-w-[1920px] px-12 mx-auto text-sm font-sans" }}>
    <!-- Logo -->
    <div class="flex-1 min-w-[140px]">
        <a href="/" aria-label="Page d'accueil" class="inline-flex items-center h-20 text-hof">
            <span>
                <?php if ($__env->exists('enjoy-the-world::blocks.navigation.logo')) echo $__env->make('enjoy-the-world::blocks.navigation.logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </span>
        </a>
    </div>

    <!-- Search Bar -->
    <div class="min-w-[348px] px-6 text-center">
        <?php if ($__env->exists('enjoy-the-world::blocks.navigation.search-bar')) echo $__env->make('enjoy-the-world::blocks.navigation.search-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!-- User Nav -->
    <div class="flex-1 min-w-[140px]">
        <nav class="flex items-center justify-end h-20">
            <?php if ($__env->exists('enjoy-the-world::blocks.navigation.user-nav')) echo $__env->make('enjoy-the-world::blocks.navigation.user-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </nav>
    </div>
</header>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/platform/EnjoyTheWorld/Resources/Views/blocks/navigation.blade.php ENDPATH**/ ?>