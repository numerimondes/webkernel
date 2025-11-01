
<style>
    .fi-page-header-main-ctn {
        padding-block: 0 !important;
    }
    .fi-page-content {
        row-gap: 0;
    }
    x-filament\:\:section {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        margin: 1.5rem 0;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: none !important;
    }
    .error-details-container {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    .error-details-container.show {
        max-height: 500px;
        overflow-y: auto;
    }
    .error-trace {
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-word;
        background: rgba(0, 0, 0, 0.2);
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }
    .error-code-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        background: rgba(239, 68, 68, 0.1);
        color: rgb(239, 68, 68);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .dark .error-code-badge {
        background: rgba(239, 68, 68, 0.2);
        color: rgb(252, 165, 165);
    }
    .error-token-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        background: rgba(59, 130, 246, 0.1);
        color: rgb(59, 130, 246);
        border: 2px solid rgba(59, 130, 246, 0.3);
        cursor: pointer;
        transition: all 0.2s;
    }
    .error-token-badge:hover {
        background: rgba(59, 130, 246, 0.2);
        border-color: rgba(59, 130, 246, 0.5);
    }
    .dark .error-token-badge {
        background: rgba(59, 130, 246, 0.2);
        color: rgb(147, 197, 253);
        border-color: rgba(59, 130, 246, 0.4);
    }
    .dark .error-token-badge:hover {
        background: rgba(59, 130, 246, 0.3);
        border-color: rgba(59, 130, 246, 0.6);
    }
</style>

<style>
    :root {
        --z-background: -2;
        --z-card-shadow: 0;
        --z-card: 1;
        --z-content: 10;
        --card-max-width: 560px;
        --card-padding: 2rem;
        --card-border-radius: 12px;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Text', 'Segoe UI', Roboto, sans-serif;
    }

    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background:
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 0, 0, 0.03) 2px,
                rgba(0, 0, 0, 0.03) 4px
            ),
            repeating-linear-gradient(
                90deg,
                transparent,
                transparent 2px,
                rgba(0, 0, 0, 0.03) 2px,
                rgba(0, 0, 0, 0.03) 4px
            ),
            var(--primary-50);
        z-index: var(--z-background);
        opacity: 1;
        transition: opacity 1.5s ease-in-out;
    }

    body::after {
        content: "";
        position: fixed;
        inset: 0;
        background:
            repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(255, 255, 255, 0.02) 2px,
                rgba(255, 255, 255, 0.02) 4px
            ),
            repeating-linear-gradient(
                90deg,
                transparent,
                transparent 2px,
                rgba(255, 255, 255, 0.02) 2px,
                rgba(255, 255, 255, 0.02) 4px
            ),
            var(--gray-900);
        z-index: var(--z-background);
        opacity: 0;
        transition: opacity 1.5s ease-in-out;
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
        max-width: var(--card-max-width);
        z-index: var(--z-content);
    }

    .fi-simple-main {
        position: relative;
        border-radius: var(--card-border-radius);
        border: 0.5px solid rgba(0, 0, 0, 0.06);
        animation: fadeIn 1s ease-out;
        padding: var(--card-padding) !important;
        z-index: var(--z-card);
        box-shadow:
            0 2px 4px color-mix(in srgb, var(--primary-100) 25%, transparent),
            0 8px 16px color-mix(in srgb, var(--primary-200) 20%, transparent),
            0 32px 64px -12px color-mix(in srgb, var(--primary-400) 15%, transparent),
            inset 0 0 0 0.5px color-mix(in srgb, var(--primary-300) 30%, transparent);
    }

    html:where(.dark) .fi-simple-main {
        background-color: var(--gray-900) !important;
        border: 0.5px solid rgba(255, 255, 255, 0.08);
    }

    .fi-simple-main::before {
        content: "";
        position: absolute;
        border-radius: var(--card-border-radius);
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        background: rgba(0, 0, 0, 0.08);
        border: 0.5px solid rgba(0, 0, 0, 0.06);
        pointer-events: none;
        z-index: var(--z-card-shadow);
        transition: all 1.5s ease;
    }

    :where(.dark) .fi-simple-main::before {
        background: rgba(0, 0, 0, 0.4);
        border: 0.5px solid rgba(255, 255, 255, 0.08);
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
</style>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('browser-back', () => {
            window.history.back();
        });
    });
</script>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/Base/Resources/Views/error-page-styling.blade.php ENDPATH**/ ?>