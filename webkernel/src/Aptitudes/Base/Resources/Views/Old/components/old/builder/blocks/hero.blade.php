{{-- 
    resources/views/builder/blocks/hero.blade.php
    @meta name: Hero Section
    @meta icon: 🎯
    @meta category: Headers
    @meta preview: Large hero with title and CTA button
    @meta description: Perfect for landing pages with compelling headlines and call-to-action buttons
--}}

@php
$blockId = $blockId ?? 'hero-default';
$title = $title ?? 'Bienvenue sur notre site';
$description = $description ?? 'Découvrez nos services exceptionnels';
$alignment = $alignment ?? 'center';
$variant = $variant ?? 'primary';
@endphp

<section class="py-20 {{ $variant === 'dark' ? 'bg-gray-900 text-white' : ($variant === 'primary' ? 'bg-gradient-to-br from-blue-600 to-purple-700 text-white' : 'bg-white text-gray-900') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-{{ $alignment }}">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                {{ $title }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 {{ $variant === 'primary' ? 'text-blue-100' : 'text-gray-600 dark:text-gray-300' }}">
                {{ $description }}
            </p>
            <div class="flex {{ $alignment === 'center' ? 'justify-center' : ($alignment === 'right' ? 'justify-end' : 'justify-start') }} space-x-4">
                <x-filament::button color="primary" size="lg">
                    <x-filament::icon icon="heroicon-o-rocket-launch" class="w-5 h-5 mr-2" />
                    Commencer
                </x-filament::button>
                <x-filament::button color="gray" variant="ghost" size="lg">
                    En savoir plus
                    <x-filament::icon icon="heroicon-o-arrow-right" class="w-5 h-5 ml-2" />
                </x-filament::button>
            </div>
        </div>
    </div>
</section>


