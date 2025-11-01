
{{-- 
    resources/views/builder/blocks/gallery.blade.php
    @meta name: Image Gallery
    @meta icon: 🖼️
    @meta category: Media
    @meta preview: Responsive image grid gallery
    @meta description: Beautiful image gallery with lightbox effect and responsive layout
--}}

@php
$blockId = $blockId ?? 'gallery-default';
$title = $title ?? 'Galerie d\'images';
$description = $description ?? 'Découvrez nos réalisations';
$alignment = $alignment ?? 'center';
$variant = $variant ?? 'default';

$images = [
    'https://picsum.photos/600/400?random=1',
    'https://picsum.photos/600/400?random=2',
    'https://picsum.photos/600/400?random=3',
    'https://picsum.photos/600/400?random=4',
    'https://picsum.photos/600/400?random=5',
    'https://picsum.photos/600/400?random=6'
];
@endphp

<section class="py-16 {{ $variant === 'dark' ? 'bg-gray-900' : 'bg-white dark:bg-gray-800' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-{{ $alignment }} mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $title }}
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300">
                {{ $description }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($images as $index => $image)
            <div class="group relative overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 aspect-[4/3]">
                <img src="{{ $image }}" 
                     alt="Image {{ $index + 1 }}" 
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 
                           flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <x-filament::button color="white" size="sm" class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                        <x-filament::icon icon="heroicon-o-magnifying-glass-plus" class="w-4 h-4 mr-1" />
                        Voir
                    </x-filament::button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <x-filament::button color="primary" variant="outlined">
                <x-filament::icon icon="heroicon-o-photo" class="w-4 h-4 mr-2" />
                Voir toute la galerie
            </x-filament::button>
        </div>
    </div>
</section>