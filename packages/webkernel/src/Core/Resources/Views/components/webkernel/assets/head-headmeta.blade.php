@push('styles')
<style>
    /* Ensure sidebar takes full height */
    .fi-sidebar.fi-main-sidebar {
        height: 100vh;
        position: sticky;
        overflow-y: auto;
    }


    /* Main content area adjustments */
    .fi-main-ctn {
        flex: 1;
        min-height: 100vh;
    }

    /* Responsive padding adjustments */
    @media (min-width: 768px) {
        .fi-main-ctn {
            padding-left: 10px !important;
            padding-right: 10px !important;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
    }

    @media (min-width: 1024px) {
        .fi-main-ctn {
            padding-left: 1% !important;
            padding-right: 10px !important;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
    }

    /* Sidebar border styles */
    :dir(ltr) aside.fi-main-sidebar {
        border-right: 0.1em solid #e5e7eb !important; /* Tailwind gray-200 */
        border-left: none !important;
    }

    :dir(ltr).dark aside.fi-main-sidebar {
        border-right: 0.1em solid #1f2937 !important; /* Tailwind gray-800 */
        border-left: none !important;
    }

    :dir(ltr).dark nav.fi-topbar {
        border-right: 0.1em solid #1f2937 !important; /* Tailwind gray-800 */
        border-left: none !important;
    }

    :dir(rtl) aside.fi-main-sidebar {
        border-left: 0.1em solid #e5e7eb !important; /* Tailwind gray-200 */
        border-right: none !important;
    }

    :dir(rtl).dark aside.fi-main-sidebar {
        border-left: 0.1em solid #1f2937 !important; /* Tailwind gray-800 */
        border-right: none !important;
    }

    :dir(rtl).dark nav.fi-topbar {
        border-left: 0.1em solid #1f2937 !important; /* Tailwind gray-800 */
        border-right: none !important;
    }

    /* Sidebar header and nav styles */
    .fi-sidebar-header {
        background: transparent !important;
    }

    nav.fi-topbar {
        background-color: transparent !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* Hide breadcrumbs */
    .fi-breadcrumbs.mb-2.hidden.sm\:block {
        display: none !important;
    }

    /* Sidebar width */
    :root {
        --sidebar-width: 16rem;
    }

    /* Modal overlay */
    .fi-modal-close-overlay {
        background-color: rgba(0, 0, 0, 0) !important;
    }

    /* Overlay loader */
    #overlay-loader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(12px) saturate(160%);
        -webkit-backdrop-filter: blur(12px) saturate(160%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        z-index: 40;
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
        background: rgba(0, 0, 0, 0.3);
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
        border-top-color: var(--primary-600);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        backdrop-filter: none;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
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
