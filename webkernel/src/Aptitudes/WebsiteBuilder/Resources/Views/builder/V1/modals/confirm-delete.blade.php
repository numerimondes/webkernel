<!-- Confirm Delete Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-data="{ open: false, itemType: '', itemName: '' }"
     x-show="open"
     x-cloak
     x-on:confirm-delete.window="open = true; itemType = $event.detail.type; itemName = $event.detail.name"
     x-on:close-confirm-delete.window="open = false"
     @click="open = false"
     style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 ease-out"
         @click.stop>
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-10 h-10 mx-auto bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                    <x-lucide-alert-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
            </div>

            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Delete <span x-text="itemType"></span>?
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Are you sure you want to delete "<span x-text="itemName" class="font-medium"></span>"? This action cannot be undone.
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-center space-x-3">
                <button @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-md transition-colors">
                    Cancel
                </button>
                <button @click="confirmDelete(); open = false"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        // Dispatch event to handle the actual deletion
        window.dispatchEvent(new CustomEvent('execute-delete', {
            detail: {
                type: this.itemType,
                name: this.itemName
            }
        }));
    }
</script>
