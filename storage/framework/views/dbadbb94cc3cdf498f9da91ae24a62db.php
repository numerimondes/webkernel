
<div id="scroll-top-btn" class="fixed bottom-4 right-4 z-50 opacity-0 invisible transition-all duration-300">
    <button
        onclick="scrollToTop()"
        class="bg-primary-dynamic hover:opacity-90 text-white rounded-full p-2 shadow-lg transition-all duration-200"
        aria-label="Retour en haut"
        title="Retour en haut"
    >
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lucide-arrow-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
</div>

<div id="scroll-progress" class="fixed top-0 left-0 w-0 bg-red z-50 transition-all duration-150" style="height: 2px;"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollTopBtn = document.getElementById('scroll-top-btn');
    const scrollProgress = document.getElementById('scroll-progress');

    // Show/hide scroll button based on scroll position
    function toggleScrollButton() {
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        // Show button when scrolled more than 300px
        if (scrollY > 300) {
            scrollTopBtn.classList.remove('opacity-0', 'invisible');
            scrollTopBtn.classList.add('opacity-100', 'visible');
        } else {
            scrollTopBtn.classList.add('opacity-0', 'invisible');
            scrollTopBtn.classList.remove('opacity-100', 'visible');
        }

        // Update progress bar
        const scrollPercent = (scrollY / (documentHeight - windowHeight)) * 100;
        scrollProgress.style.width = scrollPercent + '%';
    }

    // Smooth scroll to top function
    window.scrollToTop = function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    // Listen for scroll events with throttling
    let ticking = false;
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(toggleScrollButton);
            ticking = true;
        }
    }

    window.addEventListener('scroll', function() {
        ticking = false;
        requestTick();
    });

    // Initial check
    toggleScrollButton();
});
</script>

<style>
/* Smooth scroll behavior */
html { scroll-behavior: smooth; }

/* Custom scrollbar */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.1); }
::-webkit-scrollbar-thumb {
    background: var(--primary-color, #3b82f6);
    border-radius: 3px;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    html { scroll-behavior: auto; }
    #scroll-top-btn, #scroll-progress { transition: none; }
}
</style>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/WebsiteBuilder/Resources/Views/live-website/layouts/partials/scroll-top.blade.php ENDPATH**/ ?>