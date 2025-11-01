@php

@endphp

<style>
    @if (function_exists('filament') && filament()->getCurrentOrDefaultPanel()?->hasTopNavigation())
        /* Hide sidebar when top navigation is enabled */
        aside.fi-sidebar.fi-main-sidebar {
            display: none !important;
        }

        /* Neutralize any effects of the sidebar-open state */
        .fi-sidebar-open {
            overflow: hidden !important;
        }
    @endif

    @media (min-width: 540px) {

        :root {
            /* Spacing System - Unified margins for all components */
            --webkernel-space-outer: 0.65rem;
            --webkernel-space-inner: 0.65rem;
            --webkernel-space-top: 0.65rem;
            --webkernel-space-bottom: 0.65rem;

            /* Border Radius System */
            --webkernel-radius-container: 7px;
            --webkernel-radius-content: 13px;

            /* Scrollbar System */
            --webkernel-scrollbar-size: 4px;
            --webkernel-scrollbar-radius: 2px;
            --webkernel-scrollbar-opacity: 0.2;
            --webkernel-scrollbar-opacity-hover: 0.3;
            --webkernel-scrollbar-offset: 8px;

            /* Shadow System */
            --webkernel-shadow-blur: 4px;
            --webkernel-shadow-spread: 0px;
            --webkernel-shadow-y: 2px;
            --webkernel-shadow-opacity: 0.06;
            --webkernel-shadow-border-opacity: 0.08;
            --webkernel-shadow-dark-opacity: 0.3;
            --webkernel-shadow-dark-border-opacity: 0.08;

            /* Effects */
            --webkernel-backdrop-blur: 10px;

            /* Component Dimensions */
            --webkernel-topbar-height: 2rem;
            --webkernel-sidebar-toggle-height: 2.5rem;
            --webkernel-content-offset-with-topbar: calc(2 * (var(--webkernel-topbar-height) + var(--webkernel-space-top) + var(--webkernel-space-bottom) + var(--webkernel-space-outer)));
            --webkernel-content-offset-without-topbar: calc(2 * (var(--webkernel-space-top) + var(--webkernel-space-bottom) + var(--webkernel-space-outer)));
            --webkernel-content-offset-with-toggle: calc(var(--webkernel-sidebar-toggle-height) + var(--webkernel-space-top) + var(--webkernel-space-bottom) + var(--webkernel-space-outer));
            --webkernel-content-offset: var(--webkernel-content-offset-with-topbar);
            --webkernel-sidebar-height: calc(100vh - var(--webkernel-content-offset));

            /* Sidebar System */
            --webkernel-sidebar-padding-x: 1rem;
            --webkernel-sidebar-item-margin-left: 1rem;
            --webkernel-sidebar-item-margin-right: 0.8rem;
        }

        /* Keyframes for spinning animation */
        @keyframes webkernel-spin-blob {
            0% {
                transform: translate(-50%, -50%) rotate(0deg) scale(var(--webkernel-animation-scale));
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg) scale(var(--webkernel-animation-scale));
            }
        }
    }

    @media (min-width: 540px) {

        /* Viewport Lock */
        html,
        body {
            overflow: hidden !important;
            height: 100vh !important;
            max-height: 100vh !important;
            width: 100% !important;
            position: relative !important;
        }

        /* Scrollbar Styling - Universal */
        .fi-main-ctn .fi-main::-webkit-scrollbar,
        .fi-sidebar-nav::-webkit-scrollbar,
        *::-webkit-scrollbar {
            width: var(--webkernel-scrollbar-size) !important;
            height: var(--webkernel-scrollbar-size) !important;
        }

        .fi-main-ctn .fi-main::-webkit-scrollbar-track,
        .fi-sidebar-nav::-webkit-scrollbar-track,
        *::-webkit-scrollbar-track {
            background: transparent !important;
            margin: var(--webkernel-scrollbar-offset) !important;
        }

        .fi-main-ctn .fi-main::-webkit-scrollbar-thumb,
        .fi-sidebar-nav::-webkit-scrollbar-thumb,
        *::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, var(--webkernel-scrollbar-opacity)) !important;
            border-radius: var(--webkernel-scrollbar-radius) !important;
        }

        .fi-main-ctn .fi-main::-webkit-scrollbar-thumb:hover,
        .fi-sidebar-nav::-webkit-scrollbar-thumb:hover,
        *::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, var(--webkernel-scrollbar-opacity-hover)) !important;
        }

        /* Dark Mode Scrollbar */
        .dark .fi-main-ctn .fi-main::-webkit-scrollbar-thumb,
        .dark .fi-sidebar-nav::-webkit-scrollbar-thumb,
        .dark *::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, var(--webkernel-scrollbar-opacity)) !important;
        }

        .dark .fi-main-ctn .fi-main::-webkit-scrollbar-thumb:hover,
        .dark .fi-sidebar-nav::-webkit-scrollbar-thumb:hover,
        .dark *::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, var(--webkernel-scrollbar-opacity-hover)) !important;
        }

        /* Database notifications modal only */
        #database-notifications>div:nth-child(2)>div {
            height: calc(100vh - var(--webkernel-content-offset) + var(--webkernel-space-outer) + 2 * var(--webkernel-topbar-height)) !important;
            background-color: var(--webkernel-color-surface) !important;
            border-radius: var(--webkernel-radius-container) !important;
        }

        .dark #database-notifications>div:nth-child(2)>div {
            background-color: var(--webkernel-color-surface-dark) !important;
        }

        /* Glassmorphism effect on background when modal is open */
        .fi-modal-overlay,
        .fi-modal-close-overlay {
            backdrop-filter: blur(8px) saturate(150%) brightness(0.6) !important;
            background: rgba(0, 0, 0, 0.2) !important;
            transition: backdrop-filter 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Enhance modal focus */
        .fi-modal-window {
            backdrop-filter: blur(25px) saturate(280%) !important;
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37) !important;
            border-radius: var(--webkernel-radius-container) !important;
        }

        .dark .fi-modal-window {
            background: rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5) !important;
        }

        /* Main Container Base */
        .fi-main {
            margin-inline: 0 !important;
            background-color: var(--webkernel-color-surface) !important;
            opacity: 1 !important;
        }

        .fi-main:where(.dark, .dark *) {
            background-color: var(--webkernel-color-surface-dark) !important;
        }

        /* Main Content Wrapper */
        .fi-main-ctn {
            width: 100% !important;
            padding-left: var(--webkernel-space-outer) !important;
            padding-right: var(--webkernel-space-outer) !important;
            position: relative !important;
        }

        @media (max-width: 1023px) {
            .fi-main-ctn {
                padding-left: var(--webkernel-space-outer) !important;
            }
        }

        /* Main Content Area */
        .fi-main-ctn .fi-main {
            max-width: none !important;
            margin-top: var(--webkernel-space-top) !important;
            margin-bottom: var(--webkernel-space-bottom) !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            border-radius: var(--webkernel-radius-container) !important;
            height: calc(100vh - var(--webkernel-content-offset)) !important;
            max-height: calc(100vh - var(--webkernel-content-offset)) !important;
            background-color: var(--webkernel-color-surface) !important;
            position: relative !important;
            overflow-y: auto !important;
            overflow-x: auto !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-opacity)),
                0 0 0 1px rgba(0, 0, 0, var(--webkernel-shadow-border-opacity)) !important;
        }

        /* Rounded Corners for Child Elements */
        .fi-main-ctn .fi-main>* {
            border-radius: inherit !important;
        }

        .fi-main-ctn .fi-main>*>* {
            border-radius: var(--webkernel-radius-content) !important;
        }

        .fi-main-ctn {
            border-radius: var(--webkernel-radius-container) !important;
        }

        /* Navigation Override */
        .fi-body-has-top-navigation .fi-main-ctn .fi-main {
            margin-left: 0 !important;
            margin-right: 0 !important;
            border-radius: var(--webkernel-radius-container) !important;
        }

        .fi-main-ctn .fi-main:where(.dark, .dark *) {
            background-color: var(--webkernel-color-surface-dark) !important;
        }

        /* Dark Mode Shadows */
        @media (prefers-color-scheme: dark) {
            .fi-main-ctn .fi-main {
                box-shadow:
                    0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                    0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
            }
        }

        [data-theme="dark"] .fi-main-ctn .fi-main {
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
        }

        /* Topbar */
        .fi-topbar-ctn {
            position: sticky !important;
            top: 0 !important;
        }

        .fi-topbar {
            background-color: var(--webkernel-color-surface) !important;
            border-radius: var(--webkernel-radius-container) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-opacity)),
                0 0 0 1px rgba(0, 0, 0, var(--webkernel-shadow-border-opacity)) !important;
            margin-left: var(--webkernel-space-outer) !important;
            margin-right: var(--webkernel-space-outer) !important;
            margin-top: var(--webkernel-space-top) !important;
            margin-bottom: 0 !important;
            padding: 0.5rem 1rem !important;
            height: var(--webkernel-topbar-height) !important;
            backdrop-filter: blur(var(--webkernel-backdrop-blur)) !important;
            position: relative !important;
            overflow: visible !important;
        }

        .fi-topbar:where(.dark, .dark *) {
            background-color: var(--webkernel-color-surface-dark) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
        }

        [data-theme="dark"] .fi-topbar {
            background-color: var(--webkernel-color-surface-dark) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
        }

        @media (prefers-color-scheme: dark) {
            .fi-topbar {
                box-shadow:
                    0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                    0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
            }
        }

        /* Sidebar Toggle Button (when topbar is hidden) */
        .fi-layout-sidebar-toggle-btn-ctn {
            position: sticky !important;
            top: 0 !important;
            height: var(--webkernel-sidebar-toggle-height) !important;
            background-color: var(--webkernel-color-surface) !important;
            border-radius: var(--webkernel-radius-container) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-opacity)),
                0 0 0 1px rgba(0, 0, 0, var(--webkernel-shadow-border-opacity)) !important;
            margin-left: var(--webkernel-space-outer) !important;
            margin-right: var(--webkernel-space-outer) !important;
            margin-top: var(--webkernel-space-top) !important;
            margin-bottom: 0 !important;
            padding: 0.5rem 1rem !important;
            backdrop-filter: blur(var(--webkernel-backdrop-blur)) !important;
            position: relative !important;
            overflow: visible !important;
        }

        .fi-layout-sidebar-toggle-btn-ctn:where(.dark, .dark *) {
            background-color: var(--webkernel-color-surface-dark) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
        }

        [data-theme="dark"] .fi-layout-sidebar-toggle-btn-ctn {
            background-color: var(--webkernel-color-surface-dark) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
        }

        @media (prefers-color-scheme: dark) {
            .fi-layout-sidebar-toggle-btn-ctn {
                box-shadow:
                    0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                    0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
            }
        }



        /* Adjust content offset when no topbar exists */
        .fi-body:not(:has(.fi-topbar-ctn)) {
            --webkernel-content-offset: var(--webkernel-content-offset-without-topbar);
            --webkernel-sidebar-height: calc(100vh - var(--webkernel-content-offset-without-topbar));
        }

        /* Adjust content offset when sidebar toggle button is present (intermediate screens) */
        .fi-body:not(:has(.fi-topbar-ctn)):has(.fi-layout-sidebar-toggle-btn-ctn) {
            --webkernel-content-offset: var(--webkernel-content-offset-with-toggle);
            --webkernel-sidebar-height: calc(100vh - var(--webkernel-content-offset-with-toggle));
        }

        /* Sidebar */
        aside.fi-sidebar.fi-main-sidebar {
            height: var(--webkernel-sidebar-height) !important;
            max-height: var(--webkernel-sidebar-height) !important;
            margin-left: var(--webkernel-space-outer) !important;
            margin-top: var(--webkernel-space-top) !important;
            margin-bottom: var(--webkernel-space-bottom) !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            background-color: var(--webkernel-color-surface) !important;
            border-radius: var(--webkernel-radius-container) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-opacity)),
                0 0 0 1px rgba(0, 0, 0, var(--webkernel-shadow-border-opacity)) !important;
            backdrop-filter: blur(var(--webkernel-backdrop-blur)) !important;
        }

        aside.fi-sidebar.fi-main-sidebar:where(.dark, .dark *) {
            background-color: var(--webkernel-color-surface-dark) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
        }

        [data-theme="dark"] aside.fi-sidebar.fi-main-sidebar {
            background-color: var(--webkernel-color-surface-dark) !important;
            box-shadow:
                0 var(--webkernel-shadow-y) var(--webkernel-shadow-blur) var(--webkernel-shadow-spread) rgba(0, 0, 0, var(--webkernel-shadow-dark-opacity)),
                0 0 0 1px rgba(255, 255, 255, var(--webkernel-shadow-dark-border-opacity)) !important;
        }

        .fi-sidebar-nav {
            padding-left: var(--webkernel-sidebar-padding-x) !important;
            padding-right: var(--webkernel-sidebar-padding-x) !important;
            padding-inline: 0 !important;
            position: relative !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }


        .fi-sidebar-item,
        .fi-sidebar-group-btn {
            margin-left: var(--webkernel-sidebar-item-margin-left) !important;
            margin-right: var(--webkernel-sidebar-item-margin-right) !important;
        }

        .fi-sidebar-nav-groups{
            row-gap: calc(var(--spacing) * 2.5);
        }

        .fi-sidebar-footer {
            row-gap: calc(var(--spacing) * 2.5);
        }

        .fi-sidebar-group-label {
            font-weight: 400 !important;
        }

        .fi-user-menu-trigger{
            border-radius: var(--webkernel-radius-container) !important;
        }
        /* Background */
        .fi-body {
            background-color: var(--webkernel-color-background) !important;
            position: relative;
            overflow: hidden;
        }

        .fi-body:where(.dark, .dark *) {
            background-color: var(--webkernel-color-background-dark) !important;
        }

        [data-theme="dark"] .fi-body {
            background-color: var(--webkernel-color-background-dark) !important;
        }

        @media (prefers-color-scheme: dark) {
            .fi-body {
                background-color: var(--webkernel-color-background-dark) !important;
            }
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        .fi-body {
            background-color: var(--webkernel-color-background) !important;
        }

        .fi-body:where(.dark, .dark *) {
            background-color: var(--webkernel-color-background-dark) !important;
        }

        [data-theme="dark"] .fi-body {
            background-color: var(--webkernel-color-background-dark) !important;
        }

        @media (prefers-color-scheme: dark) {
            .fi-body {
                background-color: var(--webkernel-color-background-dark) !important;
            }
        }
    }

    @media (max-width: 1024px) {
        .fi-main-sidebar .fi-sidebar-nav {
            margin-right: var(--webkernel-space-outer) !important;
        }
    }
</style>
