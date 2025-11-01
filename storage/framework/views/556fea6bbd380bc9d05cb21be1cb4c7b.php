<style>
    @media (min-width: 540px) {

        :root {
            /* Color System - Monochromatic Theme */
            --slate-50: oklch(98% 0.005 285.885);
            --webkernel-color-background: var(--primary-100);
            --webkernel-color-surface: var(--slate-50);
            --webkernel-color-background-dark: oklch(21% 0.006 285.885);
            --webkernel-color-surface-dark: var(--gray-950);
        }


        .fi-user-menu-trigger {
            border-radius: var(--webkernel-radius-container) !important;
            background-color: var(--webkernel-color-surface) !important;
        }

        .dark .fi-user-menu-trigger {
            border-radius: var(--webkernel-radius-container) !important;
            background-color: var(--gray-900) !important;
        }
    }
</style>

<?php echo $__env->make('users::base-webkernel-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/Users/Resources/Views/webkernel-css/monochromatic-webkernel-css.blade.php ENDPATH**/ ?>