<!-- Page Settings Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-data="{ open: false }"
     x-show="open"
     x-cloak
     x-on:open-page-settings.window="open = true"
     x-on:close-page-settings.window="open = false"
     @click="open = false"
     style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 ease-out"
         @click.stop>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Page Settings</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit.prevent="updatePageSettings">
                @csrf
                <div class="space-y-4">
                    <!-- Page Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Page Name
                        </label>
                        <input type="text" wire:model="pageName"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Enter page name">
                        @error('pageName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Page Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            URL Slug
                        </label>
                        <input type="text" wire:model="pageSlug"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="page-slug">
                        @error('pageSlug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Page Path -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Page Path
                        </label>
                        <input type="text" wire:model="pagePath"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="/page-path">
                        @error('pagePath') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Template -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Template
                        </label>
                        <select wire:model="pageTemplate"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="default">Default</option>
                            <option value="landing">Landing Page</option>
                            <option value="blog">Blog Post</option>
                            <option value="contact">Contact</option>
                        </select>
                        @error('pageTemplate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- SEO Settings -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">SEO Settings</h4>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Meta Title
                                </label>
                                <input type="text" wire:model="metaTitle"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Page title for search engines">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Meta Description
                                </label>
                                <textarea wire:model="metaDescription" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                          placeholder="Page description for search engines"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Homepage Checkbox -->
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="isHomepage"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Set as homepage
                        </label>
                    </div>
                    @error('isHomepage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-md transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
