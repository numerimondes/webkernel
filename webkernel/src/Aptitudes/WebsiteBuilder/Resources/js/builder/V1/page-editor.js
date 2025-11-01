// WebkernelBuilder Page Editor Component
function WebkernelBuilderPageEditor() {
    return {
        // State
        xrayMode: false,
        viewport: 'auto',
        activeMode: 'design',
        activeTab: 'editor',
        leftCollapsed: false,
        rightCollapsed: false,
        componentSearch: '',
        draggedBlockType: null,
        selectedElementData: null,
        selectedDynamicElement: null,
        currentPageIndex: 0,

        // Theme
        selectedTheme: 'base',
        selectedFontSize: 16,
        selectedSpacing: 16,

        // Data
        pages: [
            {
                id: 1,
                name: 'Home',
                elements: []
            }
        ],

        availableComponents: [
            {
                type: 'HeaderBasic',
                name: 'Header Basic',
                category: 'Layout',
                icon: 'layout',
                properties: ['text', 'variant', 'size']
            },
            {
                type: 'Button',
                name: 'Button',
                category: 'Forms',
                icon: 'mouse-pointer',
                properties: ['text', 'variant', 'size', 'disabled']
            },
            {
                type: 'Input',
                name: 'Input',
                category: 'Forms',
                icon: 'type',
                properties: ['placeholder', 'type', 'required', 'disabled']
            },
            {
                type: 'Card',
                name: 'Card',
                category: 'Layout',
                icon: 'square',
                properties: ['title', 'content', 'footer']
            },
            {
                type: 'Hero',
                name: 'Hero Section',
                category: 'Sections',
                icon: 'image',
                properties: ['title', 'subtitle', 'backgroundImage', 'ctaText']
            },
            {
                type: 'Text',
                name: 'Text',
                category: 'Typography',
                icon: 'type',
                properties: ['content', 'size', 'weight', 'color']
            },
            {
                type: 'Image',
                name: 'Image',
                category: 'Media',
                icon: 'image',
                properties: ['src', 'alt', 'width', 'height']
            }
        ],

        themePresets: ['base', 'dark', 'deep', 'funk', 'future', 'roboto', 'swiss', 'system', 'tosh', 'bootstrap', 'bulma', 'polaris', 'tailwind'],
        fontSizes: [12, 14, 16, 20, 24, 32, 48, 64, 72],
        spaceValues: [0, 4, 8, 16, 32, 64],
        themeColors: [
            { name: 'text', value: '#000000', description: 'Primary text color' },
            { name: 'background', value: '#FFFFFF', description: 'Background color' },
            { name: 'primary', value: '#3333EE', description: 'Primary brand color' },
            { name: 'secondary', value: '#111199', description: 'Secondary color' },
            { name: 'muted', value: '#F6F6F6', description: 'Muted backgrounds' },
            { name: 'highlight', value: '#EFEFFE', description: 'Highlight color' },
            { name: 'gray', value: '#777777', description: 'Gray text' },
            { name: 'accent', value: '#660099', description: 'Accent color' }
        ],

        // Methods
        init() {
            this.initializeDragAndDrop();
            console.log('WebkernelBuilder Page Editor initialized');
        },

        toggleXrayMode() {
            this.xrayMode = !this.xrayMode;
        },

        getCurrentPage() {
            return this.pages[this.currentPageIndex] || this.pages[0];
        },

        switchToPage(index) {
            this.currentPageIndex = index;
        },

        addNewPage() {
            const newPage = {
                id: Date.now(),
                name: `Page ${this.pages.length + 1}`,
                elements: []
            };
            this.pages.push(newPage);
            this.currentPageIndex = this.pages.length - 1;
        },

        closePage(index) {
            if (this.pages.length > 1) {
                this.pages.splice(index, 1);
                if (this.currentPageIndex >= this.pages.length) {
                    this.currentPageIndex = this.pages.length - 1;
                }
            }
        },

        duplicateCurrentPage() {
            const currentPage = this.getCurrentPage();
            const duplicatedPage = {
                id: Date.now(),
                name: `${currentPage.name} (Copy)`,
                elements: JSON.parse(JSON.stringify(currentPage.elements))
            };
            this.pages.push(duplicatedPage);
            this.currentPageIndex = this.pages.length - 1;
        },

        showPageSettings() {
            console.log('Show page settings');
        },

        // Drag and Drop
        dragStart(event, blockType) {
            this.draggedBlockType = blockType;
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', blockType);
            event.target.style.opacity = '0.5';
        },

        dragEnd(event) {
            this.draggedBlockType = null;
            event.target.style.opacity = '1';
        },

        handleDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'copy';
        },

        handleDragEnter(event) {
            event.preventDefault();
        },

        handleDragLeave(event) {
            if (!event.currentTarget.contains(event.relatedTarget)) {
                // Remove highlight
            }
        },

        dropBlock(event) {
            event.preventDefault();
            const blockType = event.dataTransfer.getData('text/plain');
            console.log('Drop event triggered, blockType:', blockType);

            // Create block directly in DOM
            this.createBlockDirectly(blockType, event.currentTarget);
        },

        createBlockDirectly(blockType, container) {
            const blockId = 'block_' + Date.now();
            const blockConfig = this.getBlockConfig(blockType);

            // Create block HTML
            const blockHTML = `
                <div class="relative group border-2 border-blue-500 p-4 m-2 bg-white rounded-lg"
                     data-block-id="${blockId}"
                     data-block-type="${blockType}"
                     @click="selectBlock('${blockId}')">
                    <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs font-medium">
                        ${blockConfig.name || blockType}
                    </div>
                    <div class="block-content mt-8">
                        ${this.renderBlockPreview(blockType, blockConfig)}
                    </div>
                    <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click.stop="deleteBlock('${blockId}')" class="bg-red-600 text-white p-1 rounded hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            // Insert block into container
            container.insertAdjacentHTML('beforeend', blockHTML);

            // Hide empty state if it exists
            const emptyState = container.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = 'none';
            }

            console.log('Block created successfully:', blockId);
        },

        getBlockConfig(blockType) {
            return this.availableComponents.find(comp => comp.type === blockType) || { name: blockType };
        },

        renderBlockPreview(blockType, config) {
            switch(blockType) {
                case 'Hero':
                    return `
                        <div class="p-8 bg-gradient-to-r from-blue-600 to-purple-700 text-white rounded-lg">
                            <h1 class="text-4xl font-bold mb-4">Welcome to Our Website</h1>
                            <p class="text-xl mb-6">Discover amazing content and services</p>
                            <div class="flex space-x-4">
                                <button class="bg-white text-blue-600 px-6 py-2 rounded-lg font-medium hover:bg-gray-100">
                                    Get Started
                                </button>
                                <button class="border-2 border-white text-white px-6 py-2 rounded-lg font-medium hover:bg-white hover:text-blue-600">
                                    Learn More
                                </button>
                            </div>
                        </div>
                    `;
                case 'Button':
                    return `
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700">
                            Button
                        </button>
                    `;
                case 'Card':
                    return `
                        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                            <h3 class="text-lg font-semibold mb-2">Card Title</h3>
                            <p class="text-gray-600 mb-4">This is a card component with some content.</p>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Action</button>
                        </div>
                    `;
                default:
                    return `
                        <div class="p-4 bg-gray-100 rounded-lg text-center text-gray-600">
                            <p>${config.name || blockType} Component</p>
                        </div>
                    `;
            }
        },

        selectBlock(blockId) {
            const block = document.querySelector(`[data-block-id="${blockId}"]`);
            if (block) {
                // Remove previous selection
                document.querySelectorAll('.border-blue-500').forEach(el => {
                    el.classList.remove('border-blue-500');
                    el.classList.add('border-gray-200');
                });

                // Add selection to current block
                block.classList.remove('border-gray-200');
                block.classList.add('border-blue-500');

                // Set selected element data
                const blockType = block.getAttribute('data-block-type');
                this.selectedElementData = this.getBlockConfig(blockType);
                this.selectedDynamicElement = { id: blockId, type: blockType };
            }
        },

        deleteBlock(blockId) {
            const block = document.querySelector(`[data-block-id="${blockId}"]`);
            if (block) {
                block.remove();

                // Check if we need to show empty state
                const container = document.getElementById('dropzone');
                const blocks = container.querySelectorAll('[data-block-id]');
                if (blocks.length === 0) {
                    const emptyState = container.querySelector('.empty-state');
                    if (emptyState) {
                        emptyState.style.display = 'flex';
                    }
                }

                // Clear selection if this was the selected block
                if (this.selectedDynamicElement && this.selectedDynamicElement.id === blockId) {
                    this.selectedElementData = null;
                    this.selectedDynamicElement = null;
                }

                console.log('Block deleted:', blockId);
            }
        },

        initializeDragAndDrop() {
            console.log('Initializing drag and drop...');
        },

        // Properties Panel Methods
        getElementProperties() {
            if (this.selectedElementData) {
                return this.selectedElementData.properties || [];
            }
            return [];
        },

        getElementPropertyValue(propertyName) {
            return '';
        },

        updateElementProperty(propertyName, value) {
            console.log(`Update ${propertyName} to ${value}`);
        },

        addComponent(componentType) {
            console.log('Add component:', componentType);
        },

        updateThemeColor(colorName, value) {
            console.log(`Update ${colorName} to ${value}`);
        },

        generateCode() {
            return '// Generated code will appear here';
        }
    }
}
