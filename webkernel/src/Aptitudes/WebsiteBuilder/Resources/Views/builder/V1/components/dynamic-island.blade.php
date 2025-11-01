<div id="dynamic-island" class="wkb-dynamic-island">
    <div class="wkb-dynamic-island-content bg-black/80 backdrop-blur-xl rounded-full px-6 py-3 shadow-2xl border border-white/10">
        <div class="flex items-center space-x-4">
            <!-- Selection Tool -->
            <button id="selection-tool"
                class="wkb-tool-button wkb-active p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                data-tool="selection" title="Selection Tool">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                    </path>
                </svg>
            </button>

            <!-- Edit Tool -->
            <button id="edit-tool"
                class="wkb-tool-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                data-tool="edit" title="Edit Tool">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
            </button>

            <!-- Reorder Tool -->
            <button id="reorder-tool"
                class="wkb-tool-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                data-tool="reorder" title="Reorder Tool">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Separator -->
            <div class="w-px h-6 bg-white/20"></div>

            <!-- Quick Actions -->
            <button id="duplicate-action"
                class="wkb-action-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                title="Duplicate Element">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                    </path>
                </svg>
            </button>

            <button id="delete-action"
                class="wkb-action-button p-2 rounded-full bg-white/10 hover:bg-red-500/50 transition-all duration-200"
                title="Delete Element">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
            </button>

            <!-- Separator -->
            <div class="w-px h-6 bg-white/20"></div>

            <!-- Layer Actions -->
            <button id="bring-forward"
                class="wkb-action-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                title="Bring Forward">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12">
                    </path>
                </svg>
            </button>

            <button id="send-backward"
                class="wkb-action-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                title="Send Backward">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                </svg>
            </button>

            <!-- Separator -->
            <div class="w-px h-6 bg-white/20"></div>

            <!-- Alignment Actions -->
            <button id="align-left"
                class="wkb-action-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                title="Align Left">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16">
                    </path>
                </svg>
            </button>

            <button id="align-center"
                class="wkb-action-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                title="Align Center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-8 6h8">
                    </path>
                </svg>
            </button>

            <button id="align-right"
                class="wkb-action-button p-2 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-200"
                title="Align Right">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-8 6h8">
                    </path>
                </svg>
            </button>

            <!-- Separator -->
            <div class="w-px h-6 bg-white/20"></div>

            <!-- Quick Insert -->
            <button id="quick-insert"
                class="wkb-action-button p-2 rounded-full bg-builder-accent/80 hover:bg-builder-accent transition-all duration-200"
                title="Quick Insert">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
