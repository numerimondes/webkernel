<div id="context-menu"
    class="context-menu bg-white dark:bg-builder-gray rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 py-1 min-w-40">

    @php
    $favoriteItems = [
        ['action' => 'edit', 'label' => 'Edit', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
        ['action' => 'duplicate', 'label' => 'Duplicate', 'icon' => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z']
    ];

    $menuItems = [
        'element' => [
            'label' => 'Element',
            'items' => [
                ['action' => 'select', 'label' => 'Select', 'icon' => 'M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122']
            ]
        ],
        'layer' => [
            'label' => 'Layer',
            'items' => [
                ['action' => 'bring-forward', 'label' => 'Forward', 'icon' => 'M7 11l5-5m0 0l5 5m-5-5v12'],
                ['action' => 'send-backward', 'label' => 'Backward', 'icon' => 'M17 13l-5 5m0 0l-5-5m5 5V6'],
                ['action' => 'bring-to-front', 'label' => 'To Front', 'icon' => 'M7 11l5-5m0 0l5 5m-5-5v12'],
                ['action' => 'send-to-back', 'label' => 'To Back', 'icon' => 'M17 13l-5 5m0 0l-5-5m5 5V6']
            ]
        ],
        'align' => [
            'label' => 'Align',
            'items' => [
                ['action' => 'align-left', 'label' => 'Left', 'icon' => 'M4 6h16M4 12h8m-8 6h16'],
                ['action' => 'align-center', 'label' => 'Center', 'icon' => 'M4 6h16M4 12h16m-8 6h8'],
                ['action' => 'align-right', 'label' => 'Right', 'icon' => 'M4 6h16M4 12h16m-8 6h8'],
                ['action' => 'align-justify', 'label' => 'Justify', 'icon' => 'M4 6h16M4 12h16M4 18h16']
            ]
        ],
        'transform' => [
            'label' => 'Transform',
            'items' => [
                ['action' => 'flip-horizontal', 'label' => 'Flip H', 'icon' => 'M8 3H5a2 2 0 00-2 2v3m18 0V5a2 2 0 00-2-2h-3m0 18h3a2 2 0 002-2v-3M3 16v3a2 2 0 002 2h3'],
                ['action' => 'flip-vertical', 'label' => 'Flip V', 'icon' => 'M3 8l4-4m0 0l4 4m-4-4v18m0 0l-4-4m4 4l4-4'],
                ['action' => 'rotate-90', 'label' => 'Rotate 90°', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15']
            ]
        ],
        'style' => [
            'label' => 'Style',
            'items' => [
                ['action' => 'copy-style', 'label' => 'Copy Style', 'icon' => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'],
                ['action' => 'paste-style', 'label' => 'Paste Style', 'icon' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['action' => 'reset-style', 'label' => 'Reset', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15']
            ]
        ]
    ];
    @endphp

    <!-- Favorites -->
    @foreach($favoriteItems as $item)
    <div class="context-section">
        <button class="context-item w-full px-3 py-1.5 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center space-x-2" data-action="{{ $item['action'] }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
            </svg>
            <span>{{ $item['label'] }}</span>
        </button>
    </div>
    @endforeach

    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

    <!-- Submenus -->
    @foreach($menuItems as $sectionKey => $section)
    <div class="context-section">
        <div class="context-submenu" data-submenu="{{ $sectionKey }}">
            <button class="context-item w-full px-3 py-1.5 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center justify-between group">
                <div class="flex items-center space-x-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>{{ $section['label'] }}</span>
                </div>
                <svg class="w-3 h-3 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <div class="context-submenu-items hidden absolute left-full top-0 ml-1 bg-white dark:bg-builder-gray rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 py-1 min-w-32 z-50">
                @foreach($section['items'] as $item)
                <button class="context-item w-full px-3 py-1.5 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 flex items-center space-x-2" data-action="{{ $item['action'] }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

    <!-- Delete Action -->
    <div class="context-section">
        <button class="context-item w-full px-3 py-1.5 text-left text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center space-x-2" data-action="delete">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            <span>Delete</span>
        </button>
    </div>

    <style>
        .context-menu {
            position: fixed;
            z-index: 9999;
            display: none;
            animation: contextMenuFadeIn 0.15s ease-out;
            backdrop-filter: blur(10px);
        }

        .context-menu.show {
            display: block;
        }

        @keyframes contextMenuFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .context-item {
            transition: all 0.15s ease;
        }

        .context-item:hover {
            transform: translateX(1px);
        }

        .context-submenu {
            position: relative;
        }

        .context-submenu-items {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.2s ease;
            pointer-events: none;
        }

        .context-submenu:hover .context-submenu-items {
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
            display: block !important;
        }

        .context-section:last-child .context-item {
            color: #dc2626;
        }

        .context-section:last-child .context-item:hover {
            background-color: #fef2f2;
        }

        .dark .context-section:last-child .context-item:hover {
            background-color: rgba(239, 68, 68, 0.1);
        }
    </style>

    <script>
        // Context Menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const contextMenu = document.getElementById('context-menu');
            let currentElement = null;

            // Show context menu on right click
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();

                // Check if right-clicking on a canvas element
                const canvasElement = e.target.closest('.canvas-element');
                if (canvasElement) {
                    currentElement = canvasElement;
                    showContextMenu(e.clientX, e.clientY);
                } else {
                    hideContextMenu();
                }
            });

            // Hide context menu on left click
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#context-menu')) {
                    hideContextMenu();
                }
            });

            // Hide context menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideContextMenu();
                }
            });

            // Handle context menu actions
            contextMenu.addEventListener('click', function(e) {
                const actionButton = e.target.closest('.context-item[data-action]');
                if (actionButton) {
                    const action = actionButton.dataset.action;
                    performContextAction(action, currentElement);
                    hideContextMenu();
                }
            });

            // Handle submenu hover
            contextMenu.addEventListener('mouseenter', function(e) {
                const submenu = e.target.closest('.context-submenu');
                if (submenu) {
                    // Close other submenus
                    document.querySelectorAll('.context-submenu-items').forEach(item => {
                        if (item !== submenu.querySelector('.context-submenu-items')) {
                            item.classList.add('hidden');
                        }
                    });
                }
            });

            function showContextMenu(x, y) {
                contextMenu.style.left = x + 'px';
                contextMenu.style.top = y + 'px';
                contextMenu.classList.add('show');

                // Adjust position if menu goes off screen
                const rect = contextMenu.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                if (rect.right > viewportWidth) {
                    contextMenu.style.left = (x - rect.width) + 'px';
                }

                if (rect.bottom > viewportHeight) {
                    contextMenu.style.top = (y - rect.height) + 'px';
                }
            }

            function hideContextMenu() {
                contextMenu.classList.remove('show');
                currentElement = null;
            }

            function performContextAction(action, element) {
                if (!element) return;

                switch (action) {
                    case 'select':
                        selectElement(element);
                        break;
                    case 'edit':
                        editElement(element);
                        break;
                    case 'duplicate':
                        duplicateElement(element);
                        break;
                    case 'bring-forward':
                        bringForward(element);
                        break;
                    case 'send-backward':
                        sendBackward(element);
                        break;
                    case 'bring-to-front':
                        bringToFront(element);
                        break;
                    case 'send-to-back':
                        sendToBack(element);
                        break;
                    case 'align-left':
                        alignElement(element, 'left');
                        break;
                    case 'align-center':
                        alignElement(element, 'center');
                        break;
                    case 'align-right':
                        alignElement(element, 'right');
                        break;
                    case 'align-justify':
                        alignElement(element, 'justify');
                        break;
                    case 'flip-horizontal':
                        flipElement(element, 'horizontal');
                        break;
                    case 'flip-vertical':
                        flipElement(element, 'vertical');
                        break;
                    case 'rotate-90':
                        rotateElement(element, 90);
                        break;
                    case 'copy-style':
                        copyElementStyle(element);
                        break;
                    case 'paste-style':
                        pasteElementStyle(element);
                        break;
                    case 'reset-style':
                        resetElementStyle(element);
                        break;
                    case 'delete':
                        deleteElement(element);
                        break;
                }
            }

            // Action implementations
            function selectElement(element) {
                document.querySelectorAll('.canvas-element').forEach(el => {
                    el.classList.remove('selected');
                });
                element.classList.add('selected');
            }

            function editElement(element) {
                // Enable edit mode for the element
                element.contentEditable = true;
                element.focus();
            }

            function duplicateElement(element) {
                const clone = element.cloneNode(true);
                clone.classList.remove('selected');
                element.parentNode.insertBefore(clone, element.nextSibling);
                clone.style.transform = 'translate(10px, 10px)';
                setTimeout(() => {
                    clone.style.transform = '';
                }, 200);
            }

            function bringForward(element) {
                const parent = element.parentNode;
                const nextSibling = element.nextElementSibling;
                if (nextSibling) {
                    parent.insertBefore(element, nextSibling.nextSibling);
                }
            }

            function sendBackward(element) {
                const parent = element.parentNode;
                const prevSibling = element.previousElementSibling;
                if (prevSibling) {
                    parent.insertBefore(element, prevSibling);
                }
            }

            function bringToFront(element) {
                element.parentNode.appendChild(element);
            }

            function sendToBack(element) {
                element.parentNode.insertBefore(element, element.parentNode.firstChild);
            }

            function alignElement(element, alignment) {
                element.style.textAlign = alignment;
            }

            function flipElement(element, direction) {
                if (direction === 'horizontal') {
                    element.style.transform = 'scaleX(-1)';
                } else {
                    element.style.transform = 'scaleY(-1)';
                }
            }

            function rotateElement(element, degrees) {
                const currentTransform = element.style.transform || '';
                element.style.transform = currentTransform + ` rotate(${degrees}deg)`;
            }

            function copyElementStyle(element) {
                const computedStyle = window.getComputedStyle(element);
                const styleData = {
                    backgroundColor: computedStyle.backgroundColor,
                    color: computedStyle.color,
                    fontSize: computedStyle.fontSize,
                    fontWeight: computedStyle.fontWeight,
                    textAlign: computedStyle.textAlign,
                    padding: computedStyle.padding,
                    margin: computedStyle.margin,
                    border: computedStyle.border,
                    borderRadius: computedStyle.borderRadius
                };

                // Store in a global variable instead of localStorage
                window.copiedElementStyle = styleData;
            }

            function pasteElementStyle(element) {
                // Get from global variable instead of localStorage
                const styleData = window.copiedElementStyle;
                if (styleData) {
                    Object.keys(styleData).forEach(property => {
                        element.style[property] = styleData[property];
                    });
                }
            }

            function resetElementStyle(element) {
                element.style.cssText = '';
            }

            function deleteElement(element) {
                if (confirm('Are you sure you want to delete this element?')) {
                    element.remove();
                }
            }
        });
    </script>
</div>
