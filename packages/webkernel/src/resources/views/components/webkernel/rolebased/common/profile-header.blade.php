@php
    $user = auth()->user();
    echo $user ? $user->name : 'Aucun utilisateur connecté';
@endphp


<div x-data="{
    openSection: null,
    showButtons: true
}" class="bg-gray-50 py-8 antialiased dark:bg-gray-900 md:py-16">
    <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">

        <!-- Ligne 1 : Titre + bouton toggle -->
        <div class="mb-4 flex items-center justify-between gap-4 md:mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
                {{ __('In which area do you need help ?') }}
            </h2>

            <button type="button" @click="showButtons = !showButtons; openSection = null"
                class="flex items-center text-base font-medium text-blue-600 hover:underline dark:text-blue-400">
                <!-- Lien ou texte dynamique en fonction de showButtons -->
                <template x-if="showButtons">
                    <a href="{{ url()->current() }}" class="text-blue-600 hover:underline">
                        {{ __('See more categories') }}
                    </a>
                </template>
                <template x-if="!showButtons">
                    <span>{{ __('Back to Help') }}</span>
                </template>
                <template x-if="!showButtons">
                    <svg class="ms-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <!-- Heroicons: arrow-left -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </template>
            </button>
        </div>

        <!-- Ligne 2 : Boutons -->
        <div x-show="showButtons" x-transition class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <!-- Bouton 1 -->
            <button type="button" @click="openSection = 1; showButtons = false"
                class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 w-full">
                <svg class="me-2 h-5 w-5 shrink-0 text-gray-900 dark:text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <!-- Heroicons: user-group -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-6a4 4 0 11-8 0 4 4 0 018 0zM5 8a4 4 0 108 0 4 4 0 00-8 0z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">
                    {{ __('Our Staff Directory') }}
                </span>
            </button>

            <!-- Bouton 2 -->
            <button type="button" @click="openSection = 2; showButtons = false"
                class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 w-full">
                <svg class="me-2 h-5 w-5 shrink-0 text-gray-900 dark:text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <!-- Heroicons: credit-card -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 10H3m18-4H3a2 2 0 00-2 2v10a2 2 0 002 2h18a2 2 0 002-2V8a2 2 0 00-2-2z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">
                    {{ __('My Wallet') }}
                </span>
            </button>

            <!-- Bouton 3 -->
            <button type="button" @click="openSection = 3; showButtons = false"
                class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 w-full">
                <svg class="me-2 h-5 w-5 shrink-0 text-gray-900 dark:text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <!-- Heroicons: eye -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">
                    {{ __('Watches') }}
                </span>
            </button>
        </div>

        <!-- Ligne 3 : Sections -->
        <div class="mt-8">
            <div x-show="openSection === 1" x-transition class="p-4 bg-white dark:bg-gray-800 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Meet Our Leadership') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Information about staff members and departments, including contact informations.') }}
                </p>
                @includeWhen(0, 'in_app_sections.team')
            </div>

            <div x-show="openSection === 2" x-transition class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('My Wallet Section') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Here is your wallet history, balances, and transaction data.') }}
                </p>
            </div>

            <div x-show="openSection === 3" x-transition class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Watches Section') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Manage your watchlist and notifications for updates.') }}
                </p>
            </div>
        </div>
    </div>
</div>



@includeWhen(0, 'in_app_sections.brouillon')
@includeWhen(0, 'components.datetime-tz')
