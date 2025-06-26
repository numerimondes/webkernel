@push('styles')
<style>


@media (min-width: 768px) {
    padding-left: 10 !important;
    padding-right: 10 !important;
    padding-top: 2rem;
    padding-bottom: 2rem;

}

@media (min-width: 1024px) {
    padding-left: 1% !important;
    padding-right: 10 !important;
    padding-top: 2rem;
    padding-bottom: 2rem;

}

</style>

<style>
    :dir(ltr) aside.fi-main-sidebar {
        border-right: 0.1em solid rgba(var(--gray-200), 1) !important;
        border-left: none !important;
    }

    :dir(ltr).dark aside.fi-main-sidebar {
        border-right-color: rgba(var(--gray-800), 1) !important;
        border-left-color: transparent !important;
    }

    :dir(ltr).dark.fi-topbar nav {
        border-right-color: rgba(var(--gray-800), 1) !important;
        border-left-color: transparent !important;
    }

    :dir(rtl) aside.fi-main-sidebar {
        border-left: 0.1em solid rgba(var(--gray-200), 1) !important;
        border-right: none !important;
    }

    :dir(rtl).dark aside.fi-main-sidebar {
        border-left-color: rgba(var(--gray-800), 1) !important;
        border-right-color: transparent !important;
    }

    :dir(rtl).dark.fi-topbar nav {
        border-left-color: rgba(var(--gray-800), 1) !important;
        border-right-color: transparent !important;
    }

    .fi-sidebar-header {
        background: transparent !important;
    }
    nav {
        background-color: transparent !important;
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .fi-breadcrumbs.mb-2.hidden.sm\:block {
        display: none !important;
    }
    :root {
        --sidebar-width: 16rem;
    }
    .fi-modal-close-overlay {
        background-color: rgba(0, 0, 0, 0) !important;
    }

    #overlay-loader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.05); /* Light translucent background */
        backdrop-filter: blur(12px) saturate(160%);
        -webkit-backdrop-filter: blur(12px) saturate(160%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        z-index: 40; /* To be under the notifications*/
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    .fi-header-subheading {
        max-width: none !important;
        white-space: normal !important;
    }
    /* Dark mode adjustments */
    .dark #overlay-loader {
        background: rgba(0, 0, 0, 0.3); /* Dark translucent background */
        border-color: rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
    }
    #overlay-loader.active {
        opacity: 1;
        pointer-events: auto;
    }
    .loader-spinner {
        width: 3rem;
        height: 3rem;
        border: 4px solid rgba(255, 255, 255, 0.2);
        border-top-color: var(--primary-600); /* Matches Filament theme */
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        backdrop-filter: none; /* Prevents spinner from being blurred */
    }
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<style>

</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure loader is appended directly to body for full-screen coverage
        const overlay = document.createElement('div');
        overlay.id = 'overlay-loader';
        overlay.innerHTML = '<div class="loader-spinner"></div>';
        document.body.appendChild(overlay);

        const reloadController = {
            handleReload() {
                overlay.classList.add('active');
                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 550);
            }
        };

        window.addEventListener('triggerSmoothReload', reloadController.handleReload);

        window.addEventListener('beforeunload', () => {
            window.removeEventListener('triggerSmoothReload', reloadController.handleReload);
        });
    });
</script>
@endpush
