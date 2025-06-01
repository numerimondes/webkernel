@php
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
    $BADGE_SENSITIVITY = 'clic';

    $brand = 'Webkernel';
    $logo = 'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://numerimondes.com/&size=256';
    $base_width = 32;
    $img_width = $base_width * 0.5;
    $svg_width = $base_width * 0.65;
    $id = session('credit_badge_session_id');
    $poweredByText = lang('powered_by');
    $poweredLinkBrand = "https://numerimondes.com";
    $brandLink = "Numerimondes";
@endphp

<style>
    @if($BADGE_SENSITIVITY === 'hover')
        .credit-badge-ui-main .powered-by-text {
            display: none;
            align-items: center;
            gap: 3px;
            margin-left: 2px;
            margin-right: 2px;
        }
        .credit-badge-ui-main:hover .powered-by-text {
            display: inline-flex;
        }
        .credit-badge-ui-main .powered-by-link {
            display: none;
            align-items: center;
            gap: 3px;
            margin-left: 2px;
            margin-right: 2px;
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
            margin-left: 2px;
            margin-right: 2px;
        }
        .credit-badge-ui-main.clicked .powered-by-text {
            display: inline-flex;
        }
        .credit-badge-ui-main .powered-by-link {
            display: none;
            align-items: center;
            gap: 3px;
            margin-left: 2px;
            margin-right: 2px;
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
    @media (prefers-color-scheme: dark) {
        .external-link-icon {
            color: rgb(255, 255, 255);
        }
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
    <div class="credit-badge-ui-main" style="
        padding: 6px 12px; font-size: 11px;
        font-family: sans-serif; color: white; background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        display: flex; align-items: center; white-space: nowrap;
        gap: 5px; cursor: {{ $BADGE_SENSITIVITY === 'clic' ? 'pointer' : 'default' }}; transition: all 0.2s ease;
        height: 32px;
        @if($isRTL) flex-direction: row-reverse; @endif
    ">
        @if($isRTL)
            @if($BADGE_SENSITIVITY !== 'nothing')
                <span class="powered-by-link">
                    <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer" style="color: currentColor; text-decoration: none;">
                        <x-filament::icon
                            icon="heroicon-m-arrow-top-right-on-square"
                            class="external-link-icon"
                            style="width: {{ $svg_width }}px; height: {{ $svg_width }}px; max-width: 100%; max-height: 100%;"
                        />
                    </a>
                    <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    style="color: currentColor; text-decoration: none;">
                       <span class="text-sm font-bold leading-6 text-gray-950 dark:text-white">
                        {{ $brandLink }}
                    </span>
                    </a>
                </span>
                    <span class="powered-by-text text-sm font-bold leading-6 text-gray-950 dark:text-white">
                    {{ $poweredByText }}
                </span>
            @endif
            <span class="brand-text text-sm font-bold leading-6 text-gray-950 dark:text-white">{{ $brand }}</span>
            <img src="{{ $logo }}" alt="Logo" style="width: {{ $img_width }}px; height: auto; display: inline-block; vertical-align: middle;">
        @else
            <img src="{{ $logo }}" alt="Logo" style="width: {{ $img_width }}px; height: auto; display: inline-block; vertical-align: middle;">
            <span class="brand-text text-sm font-bold leading-6 text-gray-950 dark:text-white">{{ $brand }}</span>
            @if($BADGE_SENSITIVITY !== 'nothing')
                    <span class="powered-by-text text-sm font-bold leading-6 text-gray-950 dark:text-white">
                    {{ $poweredByText }}
                </span>
                <span class="powered-by-link">
                    <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer" style="color: currentColor; text-decoration: none;">
                       <span class="text-sm font-bold leading-6 text-gray-950 dark:text-white">
                        {{ $brandLink }}
                    </span>
                    </a>
                    <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer" style="color: currentColor; text-decoration: none;">
                        <x-filament::icon
                            icon="heroicon-m-arrow-top-right-on-square"
                            class="external-link-icon"
                            style="width: {{ $svg_width }}px; height: {{ $svg_width }}px; max-width: 100%; max-height: 100%;"
                        />
                    </a>
                </span>
            @endif
        @endif
    </div>

    <x-filament::dropdown :placement="$dropdownPlacement">
        <x-slot name="trigger">
            <button
                type="button"
                class="universal-help-ui-button"
                style="
                    padding: 6px;
                    font-size: 14px;
                    font-family: sans-serif;
                    cursor: pointer;
                    color: white;
                    background: rgba(255, 255, 255, 0.15);
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    transition: all 0.2s ease;
                "
            >
                <x-filament::icon
                    icon="heroicon-m-question-mark-circle"
                    class="text-white"
                    style="width: 18px; height: 18px; max-width: 100%; max-height: 100%;"
                />
            </button>
        </x-slot>
        <x-filament::dropdown.header>
            <div class="flex items-center gap-2">
                <x-filament::icon
                    icon="heroicon-m-question-mark-circle"
                    class="text-gray-500 dark:text-gray-400"
                    style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;"
                />
                Help & Support
            </div>
        </x-filament::dropdown.header>
        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item
                href="https://numerimondes.com/docs"
                tag="a"
                target="_blank"
            >
                <div class="flex items-center gap-3">
                    <x-filament::icon
                        icon="heroicon-m-book-open"
                        class="text-gray-500 dark:text-gray-400"
                        style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;"
                    />
                    <span>Documentation</span>
                </div>
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item
                href="https://numerimondes.com/support"
                tag="a"
                target="_blank"
            >
                <div class="flex items-center gap-3">
                    <x-filament::icon
                        icon="heroicon-m-chat-bubble-left-right"
                        class="text-gray-500 dark:text-gray-400"
                        style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;"
                    />
                    <span>Contact Support</span>
                </div>
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item
                href="https://numerimondes.com/faq"
                tag="a"
                target="_blank"
            >
                <div class="flex items-center gap-3">
                    <x-filament::icon
                        icon="heroicon-m-information-circle"
                        class="text-gray-500 dark:text-gray-400"
                        style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;"
                    />
                    <span>FAQ</span>
                </div>
            </x-filament::dropdown.list.item>
            <x-filament::dropdown.list.item
                href="https://numerimondes.com/bug-report"
                tag="a"
                target="_blank"
            >
                <div class="flex items-center gap-3">
                    <x-filament::icon
                        icon="heroicon-m-bug-ant"
                        class="text-gray-500 dark:text-gray-400"
                        style="width: 16px; height: 16px; max-width: 100%; max-height: 100%;"
                    />
                    <span>Report Bug</span>
                </div>
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
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

    function handleConflictingElement(element) {
        if (element.closest(`#${BADGE_ID}`)) return;
        if (element.closest('.fi-dropdown-panel')) return;
        if (element.closest('[data-fi-dropdown-panel]')) return;

        const style = getComputedStyle(element);
        if (style.position === 'fixed' &&
            style.display !== 'none' &&
            style.visibility !== 'hidden' &&
            isInProtectedZone(element)) {
            const shift = protectedZoneRect.bottom - element.getBoundingClientRect().top + 10;
            element.style.transform = `translateY(-${shift}px)`;
            element.setAttribute('data-credit-badge-shifted', 'true');
        }
    }

    function restoreShiftedElements() {
        const shiftedElements = document.querySelectorAll('[data-credit-badge-shifted="true"]');
        shiftedElements.forEach(element => {
            element.style.transform = '';
            element.removeAttribute('data-credit-badge-shifted');
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
                        const style = node.style || {};
                        return style.position === 'fixed' ||
                               node.querySelector && node.querySelector('[style*="position: fixed"]');
                    });
                    if (hasRelevantChanges) {
                        shouldProcess = true;
                        break;
                    }
                }
                if (mutation.type === 'attributes' &&
                    (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                    const element = mutation.target;
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
                                        panelCloseObserver.close();
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
