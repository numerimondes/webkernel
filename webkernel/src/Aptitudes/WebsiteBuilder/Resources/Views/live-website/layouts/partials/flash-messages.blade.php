@if (!isset($hideFlashMessages) || !$hideFlashMessages)
    <div class="fixed top-20 right-4 z-40 space-y-2"
         x-data="{
            messages: [],
            removeMessage(index) {
                this.messages.splice(index, 1);
            },
            addMessage(type, message) {
                this.messages.push({ type, message, id: Date.now() });
                setTimeout(() => {
                    this.messages = this.messages.filter(m => m.id !== this.messages[0]?.id);
                }, 5000);
            }
         }">

        {{-- Success Messages --}}
        @if (session('success'))
            <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700 text-success-700 dark:text-success-300 px-4 py-3 rounded-lg shadow-soft animate-slide-down flex items-center justify-between max-w-md"
                 role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="ml-2 text-success-500 hover:text-success-700 focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Error Messages --}}
        @if (session('error'))
            <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-700 text-danger-700 dark:text-danger-300 px-4 py-3 rounded-lg shadow-soft animate-slide-down flex items-center justify-between max-w-md"
                 role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="ml-2 text-danger-500 hover:text-danger-700 focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Warning Messages --}}
        @if (session('warning'))
            <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 text-warning-700 dark:text-warning-300 px-4 py-3 rounded-lg shadow-soft animate-slide-down flex items-center justify-between max-w-md"
                 role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
                <button @click="show = false" class="ml-2 text-warning-500 hover:text-warning-700 focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Info Messages --}}
        @if (session('info'))
            <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-700 text-info-700 dark:text-info-300 px-4 py-3 rounded-lg shadow-soft animate-slide-down flex items-center justify-between max-w-md"
                 role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
                <button @click="show = false" class="ml-2 text-info-500 hover:text-info-700 focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if (isset($errors) && $errors->any())
            <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-700 text-danger-700 dark:text-danger-300 px-4 py-3 rounded-lg shadow-soft animate-slide-down max-w-md"
                 role="alert" x-data="{ show: true }" x-show="show">
                <div class="flex items-start justify-between">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-medium">Validation Errors:</p>
                            <ul class="mt-1 text-sm list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button @click="show = false" class="ml-2 text-danger-500 hover:text-danger-700 focus:outline-none">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
@endif
