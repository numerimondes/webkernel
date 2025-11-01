@php
    use Webkernel\Aptitudes\UI\Resources\Views\components\CollectorCard3D\CollectorCard3D;
    $collector_card_3_d = new CollectorCard3D($attributes->getAttributes());
@endphp

<link href="https://fonts.googleapis.com/css2?family=PT+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<x-base::UIQuery  module="ui-module" scope="components/CollectorCard3D" types="js,css" recursive />


<{{ $collector_card_3_d->tag }} {!! $collector_card_3_d->getAttributes() !!}>
    <div class="numerimondes-website-card-wrapper">
        <div class="numerimondes-website-card-container">
            <div class="card-tilt-container"
                 onmousemove="handleTilt(event, this)"
                 onmouseleave="resetTilt(this)"
                 data-tilt-sensitivity="{{ $collector_card_3_d->tiltSensitivity }}"
                 data-tilt-speed="{{ $collector_card_3_d->tiltSpeed }}"
                 data-tilt-scale="{{ $collector_card_3_d->tiltScale }}">
                <div class="numerimondes-website-card tilt-effect crisp-rendering"
                     data-unique-id="{{ $collector_card_3_d->uniqueId }}"
                     data-enable-flip="{{ $collector_card_3_d->enableFlip ? 'true' : 'false' }}"
                     data-flip-speed="{{ $collector_card_3_d->flipSpeed }}"
                     onclick="flipCard(this, event)"
                     ontouchstart="handleTouchStart(this, event)"
                     ontouchend="handleTouchEnd(this, event)">

                    <!-- Front face -->
                    <div class="numerimondes-website-card-face numerimondes-website-card-front crisp-content">
                        <div class="light-reflection"></div>
                        <div class="card-content crisp-text">
                            <div class="card-header">
                                <img class="numerimondes-logo"
                                    src="{{ $collector_card_3_d->finalLogoUrl }}"
                                    alt="Numerimondes logo">
                                    {{-- numerimondes website builder logo --}}
                                <x-base::UIQuery  element="logo" module="website-builder" width="35" height="35"
                                inject-style="fill: white !important; stroke: white !important;" inject-class="mr-2" />
                            </div>

                            <div class="card-form">
                                <div class="field">
                                    <label for="card-number-{{ $collector_card_3_d->uniqueId }}">URL</label>
                                    <div class="full-url-display">
                                        <input type="text" value="{{ $collector_card_3_d->domainParts['full'] }}" readonly>
                                    </div>
                                </div>

                                <div class="field">
                                    <label for="website-name-{{ $collector_card_3_d->uniqueId }}">Website Name</label>
                                    <input type="text" value="{{ $collector_card_3_d->finalWebsiteName }}" readonly>
                                </div>

                                <div class="fields-row">
                                    <div class="field">
                                        <label for="status-{{ $collector_card_3_d->uniqueId }}">Status</label>
                                        <input type="text" value="{{ $collector_card_3_d->statusLabel }}" readonly>
                                    </div>
                                    <div class="field">
                                        <label for="type-{{ $collector_card_3_d->uniqueId }}">Type</label>
                                        <input type="text" value="{{ $collector_card_3_d->finalType }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back face -->
                    <div class="numerimondes-website-card-face numerimondes-website-card-back crisp-content">
                        <div class="light-reflection"></div>
                        <div class="card-content crisp-text">
                            <div class="card-header-back">
                                <div class="serial-number">{{ $collector_card_3_d->serialNumber }}</div>
                                <div class="numerimondes-branding">{{ $collector_card_3_d->finalWebsiteName }}</div>
                            </div>

                            <div class="back-content">
                                <div class="info-section">
                                    <h3>Technical Information</h3>
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <span class="label">Language:</span>
                                            <span class="value">{{ $collector_card_3_d->finalLanguage }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">Created:</span>
                                            <span class="value">{{ $collector_card_3_d->finalCreatedAt }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">Updated:</span>
                                            <span class="value">{{ $collector_card_3_d->finalUpdatedAt }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">ID:</span>
                                            <span class="value">{{ $collector_card_3_d->uniqueId }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="magnetic-strip"></div>

                                <div class="signature-panel">
                                    <div class="signature-label">Authorized Signature</div>
                                    <div class="signature-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</{{ $collector_card_3_d->tag }}>

<script>
let lastTouchStart = null;
let touchStartTime = 0;

function getClientXFromEvent(evt) {
    if (!evt) return null;
    // Touch support
    if (evt.touches && evt.touches[0]) {
        return evt.touches[0].clientX;
    }
    if (evt.changedTouches && evt.changedTouches[0]) {
        return evt.changedTouches[0].clientX;
    }
    // Mouse support
    return typeof evt.clientX === 'number' ? evt.clientX : null;
}

function handleTilt(event, element) {
    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    const mouseX = event.clientX - centerX;
    const mouseY = event.clientY - centerY;

    // Get dynamic tilt settings with ultra-high sensitivity
    const maxTilt = Math.min(parseInt(element.dataset.tiltSensitivity) || 10, 45);
    const tiltScale = parseFloat(element.dataset.tiltScale) || 1;

    const rotateY = (mouseX / rect.width) * maxTilt;
    const rotateX = -(mouseY / rect.height) * maxTilt;

    const tiltElement = element.querySelector('.tilt-effect');
    if (tiltElement) {
        // Force hardware acceleration and pixel-perfect positioning
        // Fix: Use transform-origin center to prevent content shifting
        tiltElement.style.transformOrigin = 'center center';
        tiltElement.style.transform = `translate3d(0, 0, 0) perspective(2000px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale(${tiltScale})`;

        // Ensure crisp rendering during transform
        tiltElement.style.imageRendering = 'crisp-edges';
    }
}

function resetTilt(element) {
    const tiltSpeed = Math.min(parseInt(element.dataset.tiltSpeed) || 150, 1000);

    const tiltElement = element.querySelector('.tilt-effect');
    if (tiltElement) {
        tiltElement.style.transition = `transform ${tiltSpeed}ms cubic-bezier(0.4, 0, 0.2, 1)`;
        tiltElement.style.transformOrigin = 'center center';
        tiltElement.style.transform = 'translate3d(0, 0, 0) perspective(2000px) rotateX(0deg) rotateY(0deg) scale(1)';

        // Remove transition after animation completes for better performance
        setTimeout(() => {
            tiltElement.style.transition = 'transform 60ms cubic-bezier(0.4, 0, 0.2, 1)';
        }, tiltSpeed);
    }
}

function handleTouchStart(element, evt) {
    if (element.dataset.enableFlip !== 'true') return;
    
    // Prevent flip if touching input elements or their containers
    const target = evt.target || evt.srcElement;
    if (target && (
        target.tagName === 'INPUT' || 
        target.closest('input') ||
        target.closest('.field')
    )) {
        return;
    }
    
    const clientX = getClientXFromEvent(evt);
    if (clientX !== null) {
        lastTouchStart = {
            x: clientX,
            element: element
        };
        touchStartTime = Date.now();
    }
}

function handleTouchEnd(element, evt) {
    if (element.dataset.enableFlip !== 'true') return;
    
    // Prevent flip if touching input elements or their containers
    const target = evt.target || evt.srcElement;
    if (target && (
        target.tagName === 'INPUT' || 
        target.closest('input') ||
        target.closest('.field')
    )) {
        lastTouchStart = null;
        return;
    }
    
    const now = Date.now();
    const touchDuration = now - touchStartTime;
    
    // Only process if touch was quick (< 300ms) and we have a start position
    if (touchDuration < 300 && lastTouchStart && lastTouchStart.element === element) {
        evt.preventDefault();
        evt.stopPropagation();
        
        const clientX = getClientXFromEvent(evt) || lastTouchStart.x;
        performFlip(element, clientX);
    }
    
    lastTouchStart = null;
}

function flipCard(element, evt) {
    if (element.dataset.enableFlip !== 'true') return;

    // Prevent flip if clicking on input elements or their containers
    const target = evt.target || evt.srcElement;
    if (target && (
        target.tagName === 'INPUT' || 
        target.closest('input') ||
        target.closest('.field')
    )) {
        evt.stopPropagation();
        return;
    }

    // Prevent double execution from touch events
    if (evt.type === 'click' && lastTouchStart) {
        return;
    }

    const clientX = getClientXFromEvent(evt);
    if (clientX !== null) {
        performFlip(element, clientX);
    }
}

function performFlip(element, clientX) {
    const flipSpeed = Math.min(parseInt(element.dataset.flipSpeed) || 500, 2000);

    // Force pixel-perfect positioning during flip
    element.style.transition = `transform ${flipSpeed}ms cubic-bezier(0.25, 0.46, 0.45, 0.94)`;
    element.style.imageRendering = 'pixelated';
    element.style.imageRendering = 'crisp-edges';

    // Determine click side relative to card center
    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const isRightSide = clientX > centerX;

    // Clear previous direction classes
    element.classList.remove('flip-left', 'flip-right');
    
    // Set transform origin and direction based on click side
    if (isRightSide) {
        // Clicked on right side - flip to the right
        element.classList.add('flip-right');
        element.style.transformOrigin = 'right center';
    } else {
        // Clicked on left side - flip to the left
        element.classList.add('flip-left');
        element.style.transformOrigin = 'left center';
    }

    // Toggle flipped state
    element.classList.toggle('flipped');

    // Restore normal rendering after flip
    setTimeout(() => {
        element.style.imageRendering = 'auto';
    }, flipSpeed);
}
</script>

<style>
/* Critical: High-quality text rendering base */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

/* Force GPU acceleration and crisp rendering */
.crisp-rendering {
    transform: translate3d(0, 0, 0);
    -webkit-transform: translate3d(0, 0, 0);
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    -webkit-perspective: 2000px;
    perspective: 2000px;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    image-rendering: pixelated;
}

.crisp-content {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: geometricPrecision;
    font-feature-settings: "kern" 1, "liga" 1;
    transform: translate3d(0, 0, 0);
}

.crisp-text,
.crisp-text *,
.crisp-text input,
.crisp-text label,
.crisp-text span,
.crisp-text div {
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
    font-feature-settings: "kern" 1, "liga" 1 !important;
    text-shadow: none !important;
    transform: translate3d(0, 0, 0);
    will-change: auto;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transform: translateZ(0.001px);
}

/* Base layout with proper flip container sizing */
.numerimondes-website-card-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.numerimondes-website-card-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    min-height: 300px;
}

.card-tilt-container {
    perspective: 2000px;
    -webkit-perspective: 2000px;
    display: inline-block;
    width: 100%;
    padding: 16px;
    transform-style: preserve-3d;
    -webkit-transform-style: preserve-3d;
}

/* Ultra-crisp tilt effect - Fixed transform origin */
.tilt-effect {
    transition: transform 60ms cubic-bezier(0.4, 0, 0.2, 1);
    transform-style: preserve-3d;
    -webkit-transform-style: preserve-3d;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transform-origin: center center !important; /* Fix: Force center origin */
    will-change: transform;

    /* Force high-quality rendering */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;

    /* Prevent sub-pixel rendering issues */
    transform: translate3d(0, 0, 0);
    -webkit-transform: translate3d(0, 0, 0);
}

/* Disable tilt on mobile for better performance */
@media (max-width: 768px) {
    .tilt-effect {
        transform: translate3d(0, 0, 0) !important;
        transition: none;
    }
}

/* Credit card structure - Ultra-crisp flip */
.numerimondes-website-card {
    position: relative;
    width: min(100%, 400px);
    height: 252px;
    perspective: 2000px;
    -webkit-perspective: 2000px;
    cursor: pointer;
    transform-style: preserve-3d;
    -webkit-transform-style: preserve-3d;
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.35s ease-out, border-color 0.35s ease-out;
    border-radius: 10px;
    overflow: visible;
    margin: 0 auto;
    transform-origin: center center; /* Default center origin */

    /* Ultra-crisp rendering */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    transform: translate3d(0, 0, 0);
    -webkit-transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

/* Flip direction origins - will be set dynamically by JS */
.numerimondes-website-card.flip-left {
    transform-origin: left center;
}

.numerimondes-website-card.flip-right {
    transform-origin: right center;
}

/* Deep bottom-right shadow inspired by reference */
.card-tilt-container .numerimondes-website-card {
    filter: drop-shadow(-30px 30px 30px rgba(0, 0, 0, 0.35));
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94),
        filter 0.35s ease-out,
        border-color 0.35s ease-out;
}

.card-tilt-container:hover .numerimondes-website-card,
.card-tilt-container:focus-within .numerimondes-website-card {
    filter: drop-shadow(0px 20px 16px rgba(0, 0, 0, 0.45));
}

/* Correct flip rotations based on click side */
.numerimondes-website-card.flip-right.flipped {
    transform: rotateY(180deg) translate3d(0, 0, 0);
    filter: drop-shadow(30px 30px 30px rgba(0, 0, 0, 0.35));
}

.numerimondes-website-card.flip-left.flipped {
    transform: rotateY(-180deg) translate3d(0, 0, 0);
    filter: drop-shadow(-30px 30px 30px rgba(0, 0, 0, 0.35));
}

.numerimondes-website-card-face {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(45deg,
            rgb(25, 27, 34) 25%,
            hsl(217, 28%, 16%) 50%,
            hsl(219, 22%, 25%) 70%);
    border: 1px solid rgb(25, 30, 50);
    
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    overflow: hidden;

    /* Force crisp rendering */
    transform-style: preserve-3d;
    -webkit-transform-style: preserve-3d;
    will-change: transform;
    transform: translate3d(0, 0, 0);
    -webkit-transform: translate3d(0, 0, 0);
}

.card-tilt-container:hover .numerimondes-website-card-face,
.card-tilt-container:focus-within .numerimondes-website-card-face {
    border-left-width: 1px;
    border-bottom-width: 1px;
}

.card-content {
    padding: 16px;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    position: relative;
    z-index: 2;

    /* Ultra-crisp text rendering */
    transform: translate3d(0, 0, 0);
    -webkit-transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.numerimondes-website-card-front {
    transform: rotateY(0deg) translate3d(0, 0, 0);
    -webkit-transform: rotateY(0deg) translate3d(0, 0, 0);
}

.numerimondes-website-card-back {
    transform: rotateY(180deg) translate3d(0, 0, 0);
    -webkit-transform: rotateY(180deg) translate3d(0, 0, 0);
}

.numerimondes-website-card.flipped .numerimondes-website-card-front {
    transform: rotateY(-180deg) translate3d(0, 0, 0);
    -webkit-transform: rotateY(-180deg) translate3d(0, 0, 0);
}

.numerimondes-website-card.flipped .numerimondes-website-card-back {
    transform: rotateY(0deg) translate3d(0, 0, 0);
    -webkit-transform: rotateY(0deg) translate3d(0, 0, 0);
}

/* Ultra-subtle light reflection */
.light-reflection {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    border-radius: 10px;
    z-index: 1;
    background: linear-gradient(65deg, rgba(255, 255, 255, 0) 20%, rgba(255, 255, 255, 0.10) 50%, rgba(255, 255, 255, 0) 80%);
    opacity: 0;
    transform: translateX(-70%);
    transition: opacity 0.375s ease-out, transform 0.375s ease-out;
}

.card-tilt-container:hover .light-reflection,
.card-tilt-container:focus-within .light-reflection {
    opacity: 1;
    transform: translateX(70%);
}

/* Front face elements */
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    transform: translate3d(0, 0, 0);
}

.numerimondes-logo {
    width: 32px;
    height: auto;
    filter: contrast(1.3);
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translate3d(0, 0, 0);
}

.website-logo {
    width: 40px;
    height: auto;
    object-fit: contain;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translate3d(0, 0, 0);
}

.card-form {
    font-family: "PT Mono", monospace;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transform: translate3d(0, 0, 0);
}

.field {
    display: flex;
    flex-direction: column;
    gap: 3px;
    transform: translate3d(0, 0, 0);
}

.field label {
    color: #d5d7de;
    font-size: 0.65rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-weight: 500;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
    transform: translate3d(0, 0, 0);
}

.field input[type="text"] {
    font-family: "PT Mono", monospace;
    padding: 6px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid #484b58;
    transition: all 0.2s ease;
    box-sizing: border-box;
    width: 100%;

    /* Ultra-crisp input text */
    text-rendering: geometricPrecision !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transform: translateZ(0.001px);
}

.field input[type="text"]:focus {
    outline: none;
    border: 1px solid #7d8295;
    background: rgba(255, 255, 255, 0.1);
}

.fields-row {
    display: flex;
    gap: 8px;
    transform: translate3d(0, 0, 0);
}

.fields-row .field {
    flex: 1;
}

.full-url-display input {
    text-align: center;
    font-weight: 700;
    letter-spacing: 0.5px;
    font-size: 0.7rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Back face elements */
.card-header-back {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    transform: translate3d(0, 0, 0);
}

.serial-number {
    font-family: "PT Mono", monospace;
    color: #c8c8c8;
    font-size: 0.6rem;
    letter-spacing: 2px;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
    transform: translate3d(0, 0, 0);
}

.numerimondes-branding {
    font-family: "PT Mono", monospace;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 2px;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
    transform: translate3d(0, 0, 0);
}

.back-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transform: translate3d(0, 0, 0);
}

.info-section h3 {
    color: #c8c8c8;
    font-size: 0.7rem;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 1px;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
    transform: translate3d(0, 0, 0);
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    transform: translate3d(0, 0, 0);
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
    transform: translate3d(0, 0, 0);
}

.info-item .label {
    font-size: 0.6rem;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
    transform: translate3d(0, 0, 0);
}

.info-item .value {
    font-family: "PT Mono", monospace;
    color: #fff;
    font-size: 0.65rem;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
    transform: translate3d(0, 0, 0);
}

.magnetic-strip {
    height: 24px;
    background: linear-gradient(90deg, #333 0%, #666 50%, #333 100%);
    margin: 8px 0;
    border-radius: 2px;
    transform: translate3d(0, 0, 0);
}

.signature-panel {
    background: rgba(255, 255, 255, 0.9);
    padding: 6px;
    border-radius: 4px;
    color: #333;
    transform: translate3d(0, 0, 0);
}

.signature-label {
    font-size: 0.6rem;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: geometricPrecision !important;
}

.signature-line {
    height: 1px;
    background: #333;
    border-radius: 1px;
}

/* Responsive scaling */
@media screen and (max-width: 768px) {
    .numerimondes-website-card-wrapper {
        padding: 0.75rem;
    }

    .card-tilt-container {
        padding: 15px;
    }

    .numerimondes-website-card {
        width: min(100%, 350px);
        height: 220px;
        margin: 0 auto;
    }

    .card-content {
        padding: 14px;
    }

    .info-grid {
        grid-template-columns: 1fr;
        gap: 4px;
    }
}

@media screen and (max-width: 600px) {
    .numerimondes-website-card-wrapper {
        padding: 0.5rem;
    }

    .card-tilt-container {
        padding: 10px;
    }

    .numerimondes-website-card {
        width: min(100%, 300px);
        height: 189px;
        margin: 0 auto;
    }

    .card-content {
        padding: 12px;
    }

    .numerimondes-logo {
        width: 28px;
    }

    .website-logo {
        width: 35px;
    }

    .card-form {
        gap: 6px;
    }

    .fields-row {
        gap: 6px;
    }
}

@media screen and (max-width: 480px) {
    .numerimondes-website-card-container {
        min-height: 250px;
    }

    .numerimondes-website-card {
        width: min(100%, 280px);
        height: 176px;
        margin: 0 auto;
    }

    .card-content {
        padding: 10px;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .tilt-effect {
        transform: translate3d(0, 0, 0) !important;
        transition: none;
    }

    .numerimondes-website-card {
        transition: none;
    }

    .numerimondes-website-card-face {
        transition: none;
    }

    .light-reflection {
        display: none;
    }
}
</style>