
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

    /* Exclure fi-breadcrumbs mb-2 hidden sm:block */
    .fi-breadcrumbs.mb-2.hidden.sm\:block {
        display: none !important; /* Empêche l'affichage de cet élément */
    }

    :root {
        --sidebar-width: 16rem;
    }

</style>
@endpush
