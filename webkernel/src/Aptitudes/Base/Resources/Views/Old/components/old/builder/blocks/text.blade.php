
{{-- 
    resources/views/builder/blocks/text.blade.php
    @meta name: Text Block
    @meta icon: 📝
    @meta category: Content
    @meta preview: Simple paragraph text block
    @meta description: Basic text content with rich formatting options
--}}

@php
$blockId = $blockId ?? 'text-default';
$title = $title ?? 'Titre du texte';
$description = $description ?? 'Contenu de votre paragraphe...';
$alignment = $alignment ?? 'left';
$variant = $variant ?? 'default';
@endphp

<div class="py-12 {{ $variant === 'dark' ? 'bg-gray-900' : 'bg-gray-50 dark:bg-gray-800/50' }} rounded-lg">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-{{ $alignment }}">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $title }}
            </h2>
            <div class="prose prose-lg {{ $variant === 'dark' ? 'prose-invert' : 'dark:prose-invert' }} max-w-none">
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ $description }}
                </p>
            </div>
        </div>
    </div>
</div>