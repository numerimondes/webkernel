// WebkernelBuilder Canvas Zoom and Pan Functionality
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Canvas zoom and pan functionality
        const canvasContainer = document.getElementById('canvas-container');
        const canvasWrapper = document.getElementById('canvas-wrapper');
        const canvas = document.getElementById('canvas');
        const zoomInBtn = document.getElementById('zoom-in');
        const zoomOutBtn = document.getElementById('zoom-out');
        const zoomLevelSpan = document.getElementById('zoom-level');
        const resetViewBtn = document.getElementById('reset-view');
        const handToolBtn = document.getElementById('hand-tool');
        const pointerToolBtn = document.getElementById('pointer-tool');

        if (!canvasContainer || !canvasWrapper) {
            console.warn('WebkernelBuilder canvas elements not found');
            return;
        }

        let currentZoom = 1;
        let isPanning = false;
        let startX, startY, translateX = 0, translateY = 0;
        let currentTool = 'pointer';

        // Zoom levels
        const zoomLevels = [0.25, 0.5, 0.75, 1, 1.25, 1.5, 2, 3, 4];
        let currentZoomIndex = 3; // Start at 100%

        // Update zoom display
        function updateZoomDisplay() {
            if (zoomLevelSpan) {
                zoomLevelSpan.textContent = Math.round(currentZoom * 100) + '%';
            }
        }

        // Apply zoom and pan transforms
        function applyTransforms() {
            canvasWrapper.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom})`;
        }

        // Zoom in
        function zoomIn() {
            if (currentZoomIndex < zoomLevels.length - 1) {
                currentZoomIndex++;
                currentZoom = zoomLevels[currentZoomIndex];
                applyTransforms();
                updateZoomDisplay();

                // Dispatch zoom change event
                window.dispatchEvent(new CustomEvent('wkb-zoom-changed', {
                    detail: { zoom: currentZoom }
                }));
            }
        }

        // Zoom out
        function zoomOut() {
            if (currentZoomIndex > 0) {
                currentZoomIndex--;
                currentZoom = zoomLevels[currentZoomIndex];
                applyTransforms();
                updateZoomDisplay();

                // Dispatch zoom change event
                window.dispatchEvent(new CustomEvent('wkb-zoom-changed', {
                    detail: { zoom: currentZoom }
                }));
            }
        }

        // Reset view
        function resetView() {
            currentZoom = 1;
            currentZoomIndex = 3;
            translateX = 0;
            translateY = 0;
            applyTransforms();
            updateZoomDisplay();

            // Dispatch zoom change event
            window.dispatchEvent(new CustomEvent('wkb-zoom-changed', {
                detail: { zoom: currentZoom }
            }));
        }

        // Tool switching
        function switchTool(tool) {
            currentTool = tool;

            // Update button states
            document.querySelectorAll('.wkb-tool-button').forEach(btn => {
                btn.classList.remove('wkb-active', 'bg-builder-accent', 'text-white');
                btn.classList.add('bg-gray-100', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-200');
            });

            const activeBtn = document.querySelector(`[data-tool="${tool}"]`);
            if (activeBtn) {
                activeBtn.classList.add('wkb-active', 'bg-builder-accent', 'text-white');
                activeBtn.classList.remove('bg-gray-100', 'dark:bg-gray-600', 'text-gray-700', 'dark:text-gray-200');
            }

            // Update cursor
            if (tool === 'hand') {
                canvasContainer.style.cursor = 'grab';
            } else {
                canvasContainer.style.cursor = 'default';
            }
        }

        // Mouse wheel zoom
        canvasContainer.addEventListener('wheel', function(e) {
            e.preventDefault();

            const delta = e.deltaY > 0 ? -1 : 1;
            const newIndex = currentZoomIndex + delta;

            if (newIndex >= 0 && newIndex < zoomLevels.length) {
                currentZoomIndex = newIndex;
                currentZoom = zoomLevels[currentZoomIndex];
                applyTransforms();
                updateZoomDisplay();

                // Dispatch zoom change event
                window.dispatchEvent(new CustomEvent('wkb-zoom-changed', {
                    detail: { zoom: currentZoom }
                }));
            }
        });

        // Pan functionality
        canvasContainer.addEventListener('mousedown', function(e) {
            if (currentTool === 'hand') {
                isPanning = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
                canvasContainer.style.cursor = 'grabbing';
            }
        });

        canvasContainer.addEventListener('mousemove', function(e) {
            if (isPanning && currentTool === 'hand') {
                e.preventDefault();
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                applyTransforms();
            }
        });

        canvasContainer.addEventListener('mouseup', function() {
            if (isPanning) {
                isPanning = false;
                if (currentTool === 'hand') {
                    canvasContainer.style.cursor = 'grab';
                }
            }
        });

        canvasContainer.addEventListener('mouseleave', function() {
            if (isPanning) {
                isPanning = false;
                if (currentTool === 'hand') {
                    canvasContainer.style.cursor = 'grab';
                }
            }
        });

        // Button event listeners
        if (zoomInBtn) zoomInBtn.addEventListener('click', zoomIn);
        if (zoomOutBtn) zoomOutBtn.addEventListener('click', zoomOut);
        if (resetViewBtn) resetViewBtn.addEventListener('click', resetView);
        if (handToolBtn) handToolBtn.addEventListener('click', () => switchTool('hand'));
        if (pointerToolBtn) pointerToolBtn.addEventListener('click', () => switchTool('pointer'));

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case '=':
                    case '+':
                        e.preventDefault();
                        zoomIn();
                        break;
                    case '-':
                        e.preventDefault();
                        zoomOut();
                        break;
                    case '0':
                        e.preventDefault();
                        resetView();
                        break;
                }
            }

            // Space bar for hand tool
            if (e.code === 'Space' && !e.repeat) {
                e.preventDefault();
                switchTool('hand');
            }
        });

        document.addEventListener('keyup', function(e) {
            if (e.code === 'Space') {
                e.preventDefault();
                switchTool('pointer');
            }
        });

        // Initialize
        updateZoomDisplay();
        applyTransforms();

        console.log('WebkernelBuilder canvas zoom and pan initialized');
    });
})();
