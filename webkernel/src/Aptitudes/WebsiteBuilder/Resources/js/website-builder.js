/**
 * Website Builder Application
 * Main JavaScript file for the website builder functionality
 */

class WebsiteBuilderApp {
    constructor(options) {
        this.canvas = options.canvas;
        this.leftSidebar = options.leftSidebar;
        this.rightSidebar = options.rightSidebar;
        this.dynamicIsland = options.dynamicIsland;
        this.contextMenu = options.contextMenu;

        this.currentTool = 'selection';
        this.selectedElement = null;
        this.isDragging = false;
        this.dragStart = { x: 0, y: 0 };

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupTabSwitching();
        this.setupToolSwitching();
        this.setupCanvasInteractions();
        this.setupThemeSystem();
        this.setupDragAndDrop();
    }

    setupEventListeners() {
        // Global keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardShortcuts(e);
        });

        // Window resize
        window.addEventListener('resize', () => {
            this.handleWindowResize();
        });

        // Prevent default context menu on canvas
        if (this.canvas) {
            this.canvas.addEventListener('contextmenu', (e) => {
                e.preventDefault();
            });
        }
    }

    setupTabSwitching() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.dataset.tab;

                // Remove active class from all tabs
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.add('hidden'));

                // Add active class to clicked tab
                button.classList.add('active');
                const targetTab = document.getElementById(tabName + '-tab');
                if (targetTab) {
                    targetTab.classList.remove('hidden');
                }
            });
        });
    }

    setupToolSwitching() {
        const toolButtons = document.querySelectorAll('.tool-button');

        toolButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tool = button.dataset.tool;
                this.switchTool(tool);

                // Update button states
                toolButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });
    }

    setupCanvasInteractions() {
        if (!this.canvas) return;

        // Element selection
        this.canvas.addEventListener('click', (e) => {
            const element = e.target.closest('.canvas-element');
            if (element) {
                this.selectElement(element);
            } else {
                this.deselectAll();
            }
        });

        // Element hover effects
        this.canvas.addEventListener('mouseover', (e) => {
            const element = e.target.closest('.canvas-element');
            if (element && !element.classList.contains('selected')) {
                element.classList.add('hover');
            }
        });

        this.canvas.addEventListener('mouseout', (e) => {
            const element = e.target.closest('.canvas-element');
            if (element) {
                element.classList.remove('hover');
            }
        });

        // Double click to edit
        this.canvas.addEventListener('dblclick', (e) => {
            const element = e.target.closest('.canvas-element');
            if (element) {
                this.editElement(element);
            }
        });
    }

    setupThemeSystem() {
        // Theme toggle button
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }

        // Initialize theme
        this.initTheme();
    }

    setupDragAndDrop() {
        if (!this.canvas) return;

        // Make canvas elements draggable
        this.canvas.addEventListener('mousedown', (e) => {
            const element = e.target.closest('.canvas-element');
            if (element && this.currentTool === 'reorder') {
                this.startDrag(element, e);
            }
        });

        document.addEventListener('mousemove', (e) => {
            if (this.isDragging) {
                this.drag(e);
            }
        });

        document.addEventListener('mouseup', () => {
            if (this.isDragging) {
                this.endDrag();
            }
        });
    }

    switchTool(tool) {
        this.currentTool = tool;

        // Update canvas cursor
        if (this.canvas) {
            switch(tool) {
                case 'selection':
                    this.canvas.style.cursor = 'default';
                    break;
                case 'edit':
                    this.canvas.style.cursor = 'text';
                    break;
                case 'reorder':
                    this.canvas.style.cursor = 'move';
                    break;
                case 'hand':
                    this.canvas.style.cursor = 'grab';
                    break;
            }
        }

        // Update dynamic island visibility
        this.updateDynamicIslandVisibility();
    }

    selectElement(element) {
        // Remove selection from all elements
        document.querySelectorAll('.canvas-element').forEach(el => {
            el.classList.remove('selected');
        });

        // Add selection to clicked element
        element.classList.add('selected');
        this.selectedElement = element;

        // Update properties panel
        this.updatePropertiesPanel(element);

        // Update dynamic island
        this.updateDynamicIslandVisibility();
    }

    deselectAll() {
        document.querySelectorAll('.canvas-element').forEach(el => {
            el.classList.remove('selected');
        });
        this.selectedElement = null;
        this.updateDynamicIslandVisibility();
    }

    editElement(element) {
        if (element.contentEditable !== undefined) {
            element.contentEditable = true;
            element.focus();

            // Select all text
            const range = document.createRange();
            range.selectNodeContents(element);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

    updatePropertiesPanel(element) {
        if (!this.rightSidebar) return;

        // Update the properties panel with element data
        const elementId = element.dataset.element;
        const propertiesHeader = this.rightSidebar.querySelector('h2 + p');
        if (propertiesHeader) {
            propertiesHeader.textContent = this.getElementDisplayName(elementId);
        }

        // Update style properties
        this.updateStyleProperties(element);
    }

    updateStyleProperties(element) {
        if (!this.rightSidebar) return;

        const computedStyle = window.getComputedStyle(element);

        // Update opacity slider
        const opacitySlider = this.rightSidebar.querySelector('input[type="range"]');
        if (opacitySlider) {
            opacitySlider.value = computedStyle.opacity;
        }

        // Update color picker
        const colorInput = this.rightSidebar.querySelector('input[type="color"]');
        if (colorInput) {
            const bgColor = computedStyle.backgroundColor;
            if (bgColor && bgColor !== 'rgba(0, 0, 0, 0)') {
                colorInput.value = this.rgbToHex(bgColor);
            }
        }

        // Update text color input
        const textColorInput = this.rightSidebar.querySelector('input[type="text"][value*="#"]');
        if (textColorInput) {
            const textColor = computedStyle.color;
            if (textColor) {
                textColorInput.value = this.rgbToHex(textColor);
            }
        }
    }

    updateDynamicIslandVisibility() {
        if (!this.dynamicIsland) return;

        if (this.selectedElement) {
            this.dynamicIsland.classList.remove('hidden');
        } else {
            this.dynamicIsland.classList.add('hidden');
        }
    }

    startDrag(element, e) {
        this.isDragging = true;
        this.draggedElement = element;
        this.dragStart = { x: e.clientX, y: e.clientY };

        element.classList.add('dragging');
        document.body.style.cursor = 'grabbing';
    }

    drag(e) {
        if (!this.isDragging || !this.draggedElement) return;

        const deltaX = e.clientX - this.dragStart.x;
        const deltaY = e.clientY - this.dragStart.y;

        this.draggedElement.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
    }

    endDrag() {
        if (this.draggedElement) {
            this.draggedElement.classList.remove('dragging');
            this.draggedElement = null;
        }

        this.isDragging = false;
        document.body.style.cursor = '';
    }

    handleKeyboardShortcuts(e) {
        // Ctrl/Cmd + C - Copy
        if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
            e.preventDefault();
            this.copyElement();
        }

        // Ctrl/Cmd + V - Paste
        if ((e.ctrlKey || e.metaKey) && e.key === 'v') {
            e.preventDefault();
            this.pasteElement();
        }

        // Ctrl/Cmd + D - Duplicate
        if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
            e.preventDefault();
            this.duplicateElement();
        }

        // Delete key - Delete element
        if (e.key === 'Delete' || e.key === 'Backspace') {
            e.preventDefault();
            this.deleteElement();
        }

        // Escape - Deselect
        if (e.key === 'Escape') {
            this.deselectAll();
        }

        // Arrow keys - Move element
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
            this.moveElement(e.key);
        }
    }

    copyElement() {
        if (this.selectedElement) {
            this.copiedElement = this.selectedElement.cloneNode(true);
        }
    }

    pasteElement() {
        if (this.copiedElement && this.selectedElement) {
            const clone = this.copiedElement.cloneNode(true);
            clone.classList.remove('selected');
            this.selectedElement.parentNode.insertBefore(clone, this.selectedElement.nextSibling);
            clone.style.transform = 'translate(10px, 10px)';
            setTimeout(() => {
                clone.style.transform = '';
            }, 200);
        }
    }

    duplicateElement() {
        if (this.selectedElement) {
            const clone = this.selectedElement.cloneNode(true);
            clone.classList.remove('selected');
            this.selectedElement.parentNode.insertBefore(clone, this.selectedElement.nextSibling);
            clone.style.transform = 'translate(10px, 10px)';
            setTimeout(() => {
                clone.style.transform = '';
            }, 200);
        }
    }

    deleteElement() {
        if (this.selectedElement && confirm('Are you sure you want to delete this element?')) {
            this.selectedElement.remove();
            this.selectedElement = null;
            this.updateDynamicIslandVisibility();
        }
    }

    moveElement(direction) {
        if (!this.selectedElement) return;

        const step = 10; // pixels
        const currentTransform = this.selectedElement.style.transform || '';
        let translateX = 0;
        let translateY = 0;

        // Parse existing transform
        const translateMatch = currentTransform.match(/translate\(([^,]+),\s*([^)]+)\)/);
        if (translateMatch) {
            translateX = parseInt(translateMatch[1]) || 0;
            translateY = parseInt(translateMatch[2]) || 0;
        }

        // Update position based on direction
        switch(direction) {
            case 'ArrowUp':
                translateY -= step;
                break;
            case 'ArrowDown':
                translateY += step;
                break;
            case 'ArrowLeft':
                translateX -= step;
                break;
            case 'ArrowRight':
                translateX += step;
                break;
        }

        this.selectedElement.style.transform = `translate(${translateX}px, ${translateY}px)`;
    }

    initTheme() {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    toggleTheme() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
    }

    handleWindowResize() {
        // Handle responsive adjustments
        if (!this.canvas) return;

        const container = this.canvas.parentNode;

        // Adjust canvas size if needed
        const containerWidth = container.clientWidth;
        const maxWidth = 1200; // Desktop breakpoint

        if (containerWidth > maxWidth) {
            this.canvas.style.maxWidth = maxWidth + 'px';
            this.canvas.style.margin = '0 auto';
        } else {
            this.canvas.style.maxWidth = '100%';
            this.canvas.style.margin = '0';
        }
    }

    getElementDisplayName(elementId) {
        const names = {
            'hero-section': 'Hero Section',
            'header': 'Header',
            'content': 'Content',
            'footer': 'Footer'
        };
        return names[elementId] || elementId;
    }

    rgbToHex(rgb) {
        const result = rgb.match(/\d+/g);
        if (result) {
            const r = parseInt(result[0]);
            const g = parseInt(result[1]);
            const b = parseInt(result[2]);
            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
        return '#000000';
    }

    // Public API methods
    getSelectedElement() {
        return this.selectedElement;
    }

    getCurrentTool() {
        return this.currentTool;
    }

    setCurrentTool(tool) {
        this.switchTool(tool);
    }

    // Export/Import functionality
    exportProject() {
        if (!this.canvas) return null;

        const elements = Array.from(this.canvas.querySelectorAll('.canvas-element')).map(el => ({
            id: el.dataset.element,
            content: el.innerHTML,
            styles: el.style.cssText,
            classes: el.className
        }));

        return {
            elements: elements,
            metadata: {
                name: document.title,
                created: new Date().toISOString(),
                version: '1.0.0'
            }
        };
    }

    importProject(projectData) {
        if (!this.canvas) return;

        // Clear existing elements
        this.canvas.innerHTML = '';

        // Add imported elements
        projectData.elements.forEach(elementData => {
            const element = document.createElement('div');
            element.className = 'canvas-element ' + elementData.classes;
            element.dataset.element = elementData.id;
            element.style.cssText = elementData.styles;
            element.innerHTML = elementData.content;

            this.canvas.appendChild(element);
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // The WebsiteBuilderApp will be initialized from the main index.blade.php file
    console.log('Website Builder JavaScript loaded');
});

// Export for global access
window.WebsiteBuilderApp = WebsiteBuilderApp;
