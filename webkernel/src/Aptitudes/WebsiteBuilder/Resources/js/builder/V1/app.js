// WebkernelBuilder V1 Main JavaScript

class WebkernelBuilderV1 {
    constructor() {
        this.currentTool = 'pointer';
        this.selectedElement = null;
        this.isInitialized = false;

        this.init();
    }

    init() {
        if (this.isInitialized) return;

        this.setupEventListeners();
        this.initializeComponents();
        this.setupKeyboardShortcuts();

        this.isInitialized = true;
        console.log('Builder V1 initialized');
    }

    setupEventListeners() {
        // Global event listeners
        document.addEventListener('DOMContentLoaded', () => {
            this.onDOMReady();
        });

        // Custom events
        window.addEventListener('wkb-tool-changed', (e) => {
            this.onToolChanged(e.detail.tool);
        });

        window.addEventListener('wkb-element-selected', (e) => {
            this.onElementSelected(e.detail.element);
        });

        window.addEventListener('wkb-element-deleted', (e) => {
            this.onElementDeleted(e.detail.element);
        });
    }

    initializeComponents() {
        // Initialize all components
        this.initializeToolbar();
        this.initializeCanvas();
        this.initializeSidebar();
        this.initializeProperties();
        this.initializeModals();
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + S - Save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                this.save();
            }

            // Delete key - Delete selected element
            if (e.key === 'Delete' && this.selectedElement) {
                e.preventDefault();
                this.deleteSelectedElement();
            }

            // Escape - Deselect
            if (e.key === 'Escape') {
                this.deselectElement();
            }

            // Space - Toggle hand tool
            if (e.code === 'Space' && !e.repeat) {
                e.preventDefault();
                this.toggleHandTool();
            }
        });

        document.addEventListener('keyup', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                this.releaseHandTool();
            }
        });
    }

    onDOMReady() {
        // DOM is ready, perform final initialization
        this.setupDragAndDrop();
        this.setupContextMenus();
    }

    onToolChanged(tool) {
        this.currentTool = tool;
        this.updateToolUI();
    }

    onElementSelected(element) {
        this.selectedElement = element;
        this.updatePropertiesPanel();
    }

    onElementDeleted(element) {
        if (this.selectedElement === element) {
            this.selectedElement = null;
            this.updatePropertiesPanel();
        }
    }

    // Tool Management
    setTool(tool) {
        this.currentTool = tool;
        window.dispatchEvent(new CustomEvent('builder-tool-changed', {
            detail: { tool }
        }));
    }

    toggleHandTool() {
        if (this.currentTool === 'hand') {
            this.setTool('pointer');
        } else {
            this.setTool('hand');
        }
    }

    releaseHandTool() {
        if (this.currentTool === 'hand') {
            this.setTool('pointer');
        }
    }

    updateToolUI() {
        // Update toolbar UI to reflect current tool
        document.querySelectorAll('.tool-button').forEach(btn => {
            btn.classList.remove('active');
        });

        const activeBtn = document.querySelector(`[data-tool="${this.currentTool}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
    }

    // Element Management
    selectElement(element) {
        this.selectedElement = element;
        window.dispatchEvent(new CustomEvent('builder-element-selected', {
            detail: { element }
        }));
    }

    deselectElement() {
        this.selectedElement = null;
        this.updatePropertiesPanel();
    }

    deleteSelectedElement() {
        if (this.selectedElement) {
            const element = this.selectedElement;
            this.selectedElement = null;

            window.dispatchEvent(new CustomEvent('builder-element-deleted', {
                detail: { element }
            }));
        }
    }

    // Canvas Management
    initializeCanvas() {
        // Canvas-specific initialization
        console.log('Canvas initialized');
    }

    // Sidebar Management
    initializeSidebar() {
        // Sidebar-specific initialization
        this.initializeSidebarTabs();
        this.initializeSidebarSearch();
        this.initializeLayerTree();
        console.log('Sidebar initialized');
    }

    initializeSidebarTabs() {
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                // Update active tab button
                tabButtons.forEach(btn => btn.classList.remove('active', 'text-builder-accent',
                    'border-builder-accent', 'bg-builder-accent/5'));
                tabButtons.forEach(btn => btn.classList.add('text-gray-500',
                    'dark:text-gray-400'));

                this.classList.remove('text-gray-500', 'dark:text-gray-400');
                this.classList.add('active', 'text-builder-accent', 'border-b-2',
                    'border-builder-accent', 'bg-builder-accent/5');

                // Update tab content visibility
                tabContents.forEach(content => content.classList.add('hidden'));
                const targetContent = document.getElementById(targetTab + '-tab');
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
    }

    initializeSidebarSearch() {
        // Search functionality
        document.querySelectorAll('input[placeholder*="Search"]').forEach(searchInput => {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const container = this.closest('.tab-content');
                const items = container.querySelectorAll(
                '.page-item, .layer-item, .asset-item');

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                });
            });
        });
    }

    initializeLayerTree() {
        // Layer tree collapse/expand
        document.querySelectorAll('.layer-item').forEach(item => {
            const chevron = item.querySelector('.lucide-chevron-down, .lucide-chevron-right');
            if (chevron) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isExpanded = chevron.classList.contains('lucide-chevron-down');
                    const nextSibling = this.parentElement.querySelector('.ml-4');

                    if (isExpanded) {
                        chevron.setAttribute('class', chevron.getAttribute('class').replace(
                            'lucide-chevron-down', 'lucide-chevron-right'));
                        if (nextSibling) nextSibling.style.display = 'none';
                    } else {
                        chevron.setAttribute('class', chevron.getAttribute('class').replace(
                            'lucide-chevron-right', 'lucide-chevron-down'));
                        if (nextSibling) nextSibling.style.display = 'block';
                    }
                });
            }
        });
    }

    // Properties Panel Management
    initializeProperties() {
        // Properties panel initialization
        console.log('Properties panel initialized');
    }

    updatePropertiesPanel() {
        // Update properties panel based on selected element
        console.log('Properties panel updated');
    }

    // Modal Management
    initializeModals() {
        // Modal initialization
        console.log('Modals initialized');
    }

    openModal(modalName, data = {}) {
        window.dispatchEvent(new CustomEvent(`open-${modalName}`, {
            detail: data
        }));
    }

    closeModal(modalName) {
        window.dispatchEvent(new CustomEvent(`close-${modalName}`));
    }

    // Drag and Drop
    setupDragAndDrop() {
        // Drag and drop setup
        console.log('Drag and drop setup');
    }

    // Context Menus
    setupContextMenus() {
        // Context menu setup
        console.log('Context menus setup');
    }

    // Save/Load
    save() {
        console.log('Saving builder state...');
        // Implement save functionality
    }

    load(data) {
        console.log('Loading builder state...');
        // Implement load functionality
    }

    // Utility Methods
    showNotification(message, type = 'info') {
        // Show notification
        console.log(`Notification [${type}]: ${message}`);
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }
}

// Global function to open create page modal
function openCreatePageModal() {
    // Dispatch custom event
    window.dispatchEvent(new CustomEvent('openCreatePageModal'));
}

// Global function to toggle sections in properties panel
function toggleSection(sectionName) {
    const content = document.getElementById(sectionName + '-content');
    const arrow = document.getElementById(sectionName + '-arrow');

    if (content && arrow) {
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }
}

// Initialize WebkernelBuilder V1 when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.WebkernelBuilderV1 = new WebkernelBuilderV1();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = WebkernelBuilderV1;
}
