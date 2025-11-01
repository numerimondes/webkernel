<style>
@media (min-width: 540px) {

    :root {
        /* Color System - Colored Theme */
        --webkernel-color-background: oklch(21% 0.006 285.885);
        --webkernel-color-surface: oklch(92.8% 0.006 264.531);
        --webkernel-color-background-dark: oklch(21% 0.006 285.885);
        --webkernel-color-surface-dark: var(--gray-950);

        /* Animation System */
        --webkernel-animation-duration: 12s;
        --webkernel-animation-blur: 120px;
        --webkernel-animation-opacity: 0.2;
        --webkernel-animation-scale: 2;
        --webkernel-animation-dark-blur: 140px;
        --webkernel-animation-dark-opacity: 1;

        /* Gradient Colors */
        --webkernel-gradient-color-1: #3498db;
        --webkernel-gradient-color-2: #e67e22;
        --webkernel-gradient-color-3: #1abc9c;
        --webkernel-gradient-color-4: #9b59b6;
        --webkernel-gradient-color-5: #e74c3c;
        --webkernel-gradient-color-6: #f39c12;
    }

    /* Animated gradient blob */
    .fi-body::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100%;
        filter: blur(var(--webkernel-animation-blur));
        opacity: var(--webkernel-animation-opacity);
        background: conic-gradient(
            from 0deg,
            var(--webkernel-gradient-color-1),
            var(--webkernel-gradient-color-2),
            var(--webkernel-gradient-color-3),
            var(--webkernel-gradient-color-4),
            var(--webkernel-gradient-color-5),
            var(--webkernel-gradient-color-6),
            var(--webkernel-gradient-color-1)
        );
        animation: webkernel-spin-blob var(--webkernel-animation-duration) linear infinite;
        will-change: transform;
        pointer-events: none;
    }

    .fi-body:where(.dark, .dark *)::before {
        filter: blur(var(--webkernel-animation-dark-blur));
        opacity: var(--webkernel-animation-dark-opacity);
    }

    [data-theme="dark"] .fi-body::before {
        filter: blur(var(--webkernel-animation-dark-blur));
        opacity: var(--webkernel-animation-dark-opacity);
    }

    @media (prefers-color-scheme: dark) {
        .fi-body::before {
            filter: blur(var(--webkernel-animation-dark-blur));
            opacity: var(--webkernel-animation-dark-opacity);
        }
    }


}
</style>

@include('users::base-webkernel-css')
