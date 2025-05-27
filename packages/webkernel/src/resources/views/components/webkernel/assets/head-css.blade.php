@push('styles')
<style>
    aside.fi-main-sidebar {
        border-right: 0.1em solid rgba(var(--gray-200), 1) !important;
    }
    .dark aside.fi-main-sidebar {
        border-right-color: rgba(var(--gray-800), 1) !important;
    }
    .dark.fi-topbar nav {
        border-right-color: rgba(var(--gray-800), 1) !important;
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
    inset: 0;
    background: rgba(255, 255, 255, 0.05); /* lumière */
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

/* Mode sombre : ajustement automatique basé sur le thème */
.dark #overlay-loader {
    background: rgba(0, 0, 0, 0.3); /* verre fumé */
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
    border-top-color: var(--primary-600); /* s’adapte à Filament */
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    backdrop-filter: none; /* pour ne pas flouter le spinner */
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
@endpush
@push('scripts')
<div id="overlay-loader">
    <div class="loader-spinner"></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('overlay-loader');

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
