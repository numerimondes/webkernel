@php
    $brand = corePlatformInfos('brandName');
    $logo = platformAbsoluteUrlAnyPrivatetoPublic(corePlatformInfos('logoLink'));

    $rtlLanguages = config('webkernel.translation.rtl_languages', []);
    $userLanguage = null;
    if (auth()->check()) {
        $user = auth()->user();
        if (method_exists($user, 'getCurrentUserLanguageCode')) {
            $userLanguage = $user->getCurrentUserLanguageCode();
        } else {
            $userLanguage = $user->language ?? null;
        }
    }

    $isRTL = in_array($userLanguage, $rtlLanguages);
    $positionSide = $isRTL ? 'left: 10px;' : 'right: 10px;';
    $dropdownPlacement = $isRTL ? 'top-start' : 'top-end';

    if (!session()->has('credit_badge_session_id')) {
        session(['credit_badge_session_id' => 'credit-badge-ui-' . bin2hex(random_bytes(4))]);
    }

    $BADGE_SENSITIVITY_OPTIONS = ['clic', 'hover', 'nothing'];
    $BADGE_SENSITIVITY = 'nothing';

    $base_width = 32;
    $img_width = $base_width * 0.5;
    $svg_width = $base_width * 0.65;
    $id = session('credit_badge_session_id');
    $poweredByText = lang('powered_by');
    $poweredLinkBrand = "https://numerimondes.com";
    $brandLink = "Numerimondes";


        // exclude : fi-sidebar-close-overlay fixed inset-0 z-30 bg-gray-950/50 transition duration-500 dark:bg-gray-950/75 lg:hidden
@endphp

<style>
    @if($BADGE_SENSITIVITY === 'hover')
        .credit-badge-ui-main .powered-by-text {
            display: none;
            align-items: center;
            gap: 3px;
        }
        .credit-badge-ui-main:hover .powered-by-text {
            display: inline-flex;
        }
        .credit-badge-ui-main .powered-by-link {
            display: none;
            align-items: center;
            gap: 3px;
        }
        .credit-badge-ui-main:hover .powered-by-link {
            display: inline-flex;
        }
        .credit-badge-ui-main .brand-text {
            display: inline;
        }
        .credit-badge-ui-main:hover .brand-text {
            display: none;
        }
    @elseif($BADGE_SENSITIVITY === 'clic')
        .credit-badge-ui-main .powered-by-text {
            display: none;
            align-items: center;
            gap: 3px;
        }
        .credit-badge-ui-main.clicked .powered-by-text {
            display: inline-flex;
        }
        .credit-badge-ui-main .powered-by-link {
            display: none;
            align-items: center;
            gap: 3px;
        }
        .credit-badge-ui-main.clicked .powered-by-link {
            display: inline-flex;
        }
        .credit-badge-ui-main .brand-text {
            display: inline;
        }
        .credit-badge-ui-main.clicked .brand-text {
            display: none;
        }
        .credit-badge-ui-main {
            cursor: pointer;
        }
    @endif

    .external-link-icon {
        transition: color 0.2s ease;
        color: rgb(31, 41, 55);
    }
    .dark .external-link-icon {
        color: rgb(255, 255, 255);
    }

    .credit-badge-ui-main {
        border: 1px solid rgba(31, 41, 55, 0.2) !important;
    }
    .dark .credit-badge-ui-main {
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .universal-help-ui-button {
        color: rgb(31, 41, 55) !important;
        border: 1px solid rgba(31, 41, 55, 0.2) !important;
    }
    .dark .universal-help-ui-button {
        color: rgb(255, 255, 255) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .universal-help-ui-dropdown .fi-dropdown-panel {
        z-index: 10000 !important;
    }
    [data-fi-dropdown-panel] {
        z-index: 10000 !important;
    }
    .universal-help-ui-button:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        transform: scale(1.05);
    }
</style>

<div id="{{ $id }}" class="credit-badge-ui-container" style="{{ $positionSide }} position: fixed; bottom: 10px; display: flex; align-items: center; gap: 8px; z-index: 9999;">
    @include('webkernel::components.webkernel.ui.organism.universal-badge.parts.badge-white-label')
</div>

<script>
(function() {
    'use strict';
    const BADGE_ID = '{{ $id }}';
    const BADGE_SENSITIVITY = '{{ $BADGE_SENSITIVITY }}';
    const PROTECTED_ZONE_MARGIN = 20;
    let protectedZoneRect = null;
    let isInitialized = false;

    function setupBadgeInteraction() {
        if (BADGE_SENSITIVITY !== 'clic') return;

        const badgeMain = document.querySelector('.credit-badge-ui-main');
        if (!badgeMain) return;

        let isClicked = false;

        badgeMain.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            isClicked = !isClicked;

            if (isClicked) {
                badgeMain.classList.add('clicked');
            } else {
                badgeMain.classList.remove('clicked');
            }
        });

        document.addEventListener('click', function(e) {
            if (!badgeMain.contains(e.target) && isClicked) {
                isClicked = false;
                badgeMain.classList.remove('clicked');
            }
        });
    }

    function updateProtectedZone() {
        const container = document.getElementById(BADGE_ID);
        if (container) {
            const rect = container.getBoundingClientRect();
            protectedZoneRect = {
                top: rect.top - PROTECTED_ZONE_MARGIN,
                bottom: rect.bottom + PROTECTED_ZONE_MARGIN,
                left: rect.left - PROTECTED_ZONE_MARGIN,
                right: rect.right + PROTECTED_ZONE_MARGIN
            };
        }
    }

    function checkBadgeIntegrity() {
        const container = document.getElementById(BADGE_ID);
        if (!container || !container.classList.contains('credit-badge-ui-container')) {
            console.warn('Badge integrity compromised, reloading page');
            window.location.reload();
            return false;
        }
        return true;
    }

    function isInProtectedZone(element) {
        if (!protectedZoneRect) return false;
        const rect = element.getBoundingClientRect();
        return !(rect.right < protectedZoneRect.left ||
                rect.left > protectedZoneRect.right ||
                rect.bottom < protectedZoneRect.top ||
                rect.top > protectedZoneRect.bottom);
    }

    // Liste des sélecteurs à exclure de la protection
    function isExcludedElement(element) {
        const excludedSelectors = [
            `#${BADGE_ID}`,
            '.fi-dropdown-panel',
            '[data-fi-dropdown-panel]',
            '.fi-modal',
            '.fi-main-sidebar',
            '.fi-modal-close-overlay',
            '.fi-sidebar-close-overlay', // Ajouté pour l'overlay de sidebar
            '.fi-modal-content',
            '.fi-modal-header',
            '.fi-modal-footer',
            '.fi-ac-modal', // Modals Action
            '.fi-fo-modal', // Modals Form
            '.fi-in-modal', // Modals Infolist
            '.fi-no-modal', // Modals Notification
            '.fi-sc-modal', // Modals Section
            '.fi-ta-modal', // Modals Table
            '.fi-wi-modal', // Modals Widget
        ];

        // Vérifier si l'élément correspond à un sélecteur exclu
        for (const selector of excludedSelectors) {
            if (selector.startsWith('#') || selector.startsWith('.') || selector.startsWith('[')) {
                if (element.matches && element.matches(selector)) return true;
                if (element.closest && element.closest(selector)) return true;
            }
        }

        // Vérifier les classes qui commencent par 'fi-' (composants Filament)
        const classList = element.classList;
        if (classList) {
            for (const className of classList) {
                if (className.startsWith('fi-') && (
                    className.includes('modal') ||
                    className.includes('overlay') ||
                    className.includes('dropdown') ||
                    className.includes('sidebar')
                )) {
                    return true;
                }
            }
        }

        // Vérifier si l'élément est un overlay de modal
        const style = getComputedStyle(element);
        if (style.position === 'fixed' &&
            style.zIndex &&
            parseInt(style.zIndex) > 1000 &&
            (style.backgroundColor.includes('rgba') || style.background.includes('rgba'))) {
            // C'est probablement un overlay de modal
            return true;
        }

        return false;
    }

    function handleConflictingElement(element) {
        // Exclure les éléments protégés
        if (isExcludedElement(element)) {
            return;
        }

        const style = getComputedStyle(element);
        if (
            style.position === 'fixed' &&
            style.display !== 'none' &&
            style.visibility !== 'hidden' &&
            isInProtectedZone(element)
        ) {
            // Vérification supplémentaire pour éviter les faux positifs
            const zIndex = parseInt(style.zIndex) || 0;
            if (zIndex > 9999) {
                // Si l'élément a un z-index très élevé, il est probablement important
                return;
            }

            const shift = protectedZoneRect.bottom - element.getBoundingClientRect().top + 10;
            element.style.transform = `translateY(-${shift}px)`;
            element.setAttribute('data-credit-badge-shifted', 'true');
        }
    }

    function restoreShiftedElements() {
        const shiftedElements = document.querySelectorAll('[data-credit-badge-shifted="true"]');
        shiftedElements.forEach(element => {
            // Double vérification pour s'assurer de ne pas affecter les modals
            if (!isExcludedElement(element)) {
                element.style.transform = '';
                element.removeAttribute('data-credit-badge-shifted');
            }
        });
    }

    function processConflicts() {
        if (!checkBadgeIntegrity()) return;
        updateProtectedZone();
        restoreShiftedElements();

        const fixedElements = document.querySelectorAll('*');
        fixedElements.forEach(element => {
            if (getComputedStyle(element).position === 'fixed') {
                handleConflictingElement(element);
            }
        });
    }

    function setupEventBasedProtection() {
        let processTimeout = null;

        function scheduleProcess() {
            if (processTimeout) return;
            processTimeout = setTimeout(() => {
                processConflicts();
                processTimeout = null;
            }, 100);
        }

        const observer = new MutationObserver((mutations) => {
            let shouldProcess = false;
            for (let mutation of mutations) {
                if (mutation.type === 'childList') {
                    const hasRelevantChanges = [...mutation.addedNodes, ...mutation.removedNodes].some(node => {
                        if (node.nodeType !== Node.ELEMENT_NODE) return false;

                        // Ignorer les changements dans les modals et overlays
                        if (isExcludedElement(node)) return false;

                        const style = getComputedStyle(node);
                        return style.position === 'fixed';
                    });
                    if (hasRelevantChanges) {
                        shouldProcess = true;
                        break;
                    }
                }
                if (mutation.type === 'attributes' &&
                    (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                    const element = mutation.target;

                    // Ignorer les changements d'attributs sur les éléments exclus
                    if (isExcludedElement(element)) continue;

                    if (getComputedStyle(element).position === 'fixed') {
                        shouldProcess = true;
                        break;
                    }
                }
            }
            if (shouldProcess) {
                scheduleProcess();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class']
        });

        window.addEventListener('resize', scheduleProcess);
        document.addEventListener('scroll', () => {
            if (Math.random() < 0.05) {
                scheduleProcess();
            }
        }, { passive: true });
    }

    function setupDropdownManagement() {
        const badge = document.getElementById(BADGE_ID);
        const badgeMain = badge?.querySelector('.credit-badge-ui-main');
        if (!badge || !badgeMain) return;

        let stabilized = false;

        function stabilizeBadge() {
            if (stabilized) return;
            badge.style.transform = 'translateX(0)';
            badge.style.transition = 'none';
            stabilized = true;
        }

        function releaseBadge() {
            if (!stabilized) return;
            badge.style.transform = '';
            badge.style.transition = '';
            stabilized = false;
        }

        badgeMain.addEventListener('mouseenter', stabilizeBadge);
        badgeMain.addEventListener('mouseleave', () => {
            setTimeout(releaseBadge, 150);
        });

        const panelObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE &&
                        (node.matches('[data-fi-dropdown-panel]') || node.querySelector('[data-fi-dropdown-panel]'))) {
                        stabilizeBadge();
                        const panel = node.matches('[data-fi-dropdown-panel]') ? node : node.querySelector('[data-fi-dropdown-panel]');
                        if (panel) {
                            const panelCloseObserver = new MutationObserver((panelMutations) => {
                                panelMutations.forEach((panelMutation) => {
                                    if (panelMutation.type === 'childList' && panelMutation.removedNodes.length > 0) {
                                        setTimeout(releaseBadge, 300);
                                        panelCloseObserver.disconnect();
                                    }
                                });
                            });
                            panelCloseObserver.observe(panel.parentNode || document.body, {
                                childList: true,
                                subtree: true
                            });
                        }
                    }
                });
            });
        });

        panelObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    function initialize() {
        if (!checkBadgeIntegrity()) return;
        updateProtectedZone();
        if (!protectedZoneRect) {
            setTimeout(initialize, 100);
            return;
        }

        isInitialized = true;
        setupBadgeInteraction();
        setupEventBasedProtection();
        setupDropdownManagement();

        setTimeout(processConflicts, 500);
        setTimeout(processConflicts, 2000);

        console.log('Credit badge protection initialized');
    }

    window.creditBadgeProtection = {
        forceCheck: processConflicts,
        getStatus: function() {
            return {
                isInitialized: isInitialized,
                protectedZone: protectedZoneRect,
                shiftedElementsCount: document.querySelectorAll('[data-credit-badge-shifted="true"]').length
            };
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        setTimeout(initialize, 100);
    }
})();
</script>
