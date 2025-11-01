// WebkernelBuilder Drag and Drop System
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.wkb-item');
        const dropzone = document.getElementById('dropzone');

        items.forEach(item => {
            item.addEventListener('dragstart', e => {
                e.dataTransfer.setData('text/plain', e.target.id);
                e.target.classList.add('wkb-dragging');
            });

            item.addEventListener('dragend', e => {
                e.target.classList.remove('wkb-dragging');
            });
        });

        if (dropzone) {
            dropzone.addEventListener('dragover', e => {
                e.preventDefault();
                dropzone.classList.add('wkb-drag-over');
            });

            dropzone.addEventListener('dragleave', e => {
                dropzone.classList.remove('wkb-drag-over');
            });

            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                dropzone.classList.remove('wkb-drag-over');

                const id = e.dataTransfer.getData('text/plain');
                const dragged = document.getElementById(id);

                if (dragged) {
                    const blockType = dragged.getAttribute('data-block-type');
                    console.log('Dropped block:', blockType);

                    // Create block directly in DOM
                    WebkernelBuilderCreateBlockDirectly(blockType, dropzone);
                }
            });
        }

        // Function to create block directly in DOM
        window.WebkernelBuilderCreateBlockDirectly = function(blockType, container) {
            const blockId = 'block_' + Date.now();

            // Create block HTML
            const blockHTML = `
                <div class="relative group border-2 border-blue-500 p-4 m-2 bg-white rounded-lg"
                     data-block-id="${blockId}"
                     data-block-type="${blockType}">
                    <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs font-medium">
                        ${blockType}
                    </div>
                    <div class="block-content mt-8">
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
                    </div>
                    <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="WebkernelBuilderDeleteBlock('${blockId}')" class="bg-red-600 text-white p-1 rounded hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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

            console.log('WebkernelBuilder block created successfully:', blockId);
        };

        // Function to delete block
        window.WebkernelBuilderDeleteBlock = function(blockId) {
            const block = document.querySelector(`[data-block-id="${blockId}"]`);
            if (block) {
                block.remove();
                console.log('WebkernelBuilder block deleted:', blockId);
            }
        };
    });
})();
