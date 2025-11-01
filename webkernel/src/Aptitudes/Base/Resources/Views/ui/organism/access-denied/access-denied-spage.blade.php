@php
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Facades\Filament;
@endphp

<x-filament-panels::page>
    <div class="min-h-[60vh] flex flex-col items-center justify-center space-y-6 text-center px-4">
        <x-heroicon-o-lock-closed class="w-20 h-20 text-red-600 mx-auto" />

        <h1 class="text-3xl font-bold text-gray-900">
            Access Denied
        </h1>

        <p class="text-lg text-gray-700 max-w-md mx-auto">
            You do not have permission to access this panel.
        </p>

        <a href="/admin"
           class="inline-block mt-4 px-6 py-3 rounded bg-primary-600 text-white hover:bg-primary-700 transition">
            Back to Dashboard
        </a>
    </div>
</x-filament-panels::page>
