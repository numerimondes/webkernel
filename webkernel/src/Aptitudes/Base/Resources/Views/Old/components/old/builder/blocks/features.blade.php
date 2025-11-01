
{{-- 
    resources/views/builder/blocks/features.blade.php
    @meta name: Features Grid
    @meta icon: ⭐
    @meta category: Content
    @meta preview: 3-column features layout with icons
    @meta description: Showcase your key features with icons and descriptions in a responsive grid
--}}

@php
$blockId = $blockId ?? 'features-default';
$title = $title ?? 'Nos fonctionnalités';
$description = $description ?? 'Découvrez ce qui nous rend uniques';
$alignment = $alignment ?? 'center';
$variant = $variant ?? 'default';

$features = [
    [
        'icon' => 'heroicon-o-bolt',
        'title' => 'Performance',
        'description' => 'Des solutions ultra-rapides pour vos besoins'
    ],
    [
        'icon' => 'heroicon-o-shield-check',
        'title' => 'Sécurité',
        'description' => 'Protection avancée de vos données'
    ],
    [
        'icon' => 'heroicon-o-heart',
        'title' => 'Support',
        'description' => 'Une équipe dédiée à votre réussite'
    ]
];
@endphp

<section class="py-16 {{ $variant === 'dark' ? 'bg-gray-900' : 'bg-white dark:bg-gray-800' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-{{ $alignment }} mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $title }}
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl {{ $alignment === 'center' ? 'mx-auto' : '' }}">
                {{ $description }}
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($features as $feature)
            <div class="text-center p-6 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-100 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-lg mb-4">
                    <x-filament::icon icon="{{ $feature['icon'] }}" class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $feature['title'] }}
                </h3>
                <p class="text-gray-600 dark:text-gray-300">
                    {{ $feature['description'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>
