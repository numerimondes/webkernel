{{--
@block_config
{
    "identifier": "hero_carousel",
    "name": "Hero Image Carousel",
    "category": "hero",
    "version": "1.0.0",
    "description": "A responsive hero section with image carousel, text content, and action buttons",
    "config_schema": {
        "images": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "url": {"type": "string"},
                    "alt": {"type": "string"},
                    "title": {"type": "string"}
                }
            },
            "default": []
        },
        "title": {
            "type": "string",
            "default": "Welcome to Our Website"
        },
        "subtitle": {
            "type": "string",
            "default": "Discover amazing content and services"
        },
        "description": {
            "type": "string",
            "default": "This is a dynamic counter that shows visitor engagement"
        },
        "counter_label": {
            "type": "string",
            "default": "Happy Customers"
        },
        "counter_start": {
            "type": "integer",
            "default": 0
        },
        "counter_end": {
            "type": "integer",
            "default": 1000
        },
        "counter_duration": {
            "type": "integer",
            "default": 2000
        },
        "primary_button": {
            "type": "object",
            "properties": {
                "text": {"type": "string", "default": "Get Started"},
                "url": {"type": "string", "default": "#"},
                "style": {"type": "string", "default": "primary"}
            }
        },
        "secondary_button": {
            "type": "object",
            "properties": {
                "text": {"type": "string", "default": "Learn More"},
                "url": {"type": "string", "default": "#"},
                "style": {"type": "string", "default": "secondary"}
            }
        },
        "autoplay": {
            "type": "boolean",
            "default": true
        },
        "autoplay_delay": {
            "type": "integer",
            "default": 5000
        },
        "background_color": {
            "type": "string",
            "default": "bg-gradient-to-r from-blue-600 to-purple-700"
        },
        "text_color": {
            "type": "string",
            "default": "text-white"
        },
        "overlay_opacity": {
            "type": "string",
            "default": "bg-opacity-50"
        }
    }
}
@endblock_config
--}}

@php
    // Get configuration with defaults
    $config = array_merge([
        'images' => [
            ['url' => '/images/hero1.jpg', 'alt' => 'Hero Image 1', 'title' => 'Welcome'],
            ['url' => '/images/hero2.jpg', 'alt' => 'Hero Image 2', 'title' => 'Discover'],
            ['url' => '/images/hero3.jpg', 'alt' => 'Hero Image 3', 'title' => 'Explore']
        ],
        'title' => 'Welcome to Our Website',
        'subtitle' => 'Discover amazing content and services',
        'description' => 'This is a dynamic counter that shows visitor engagement',
        'counter_label' => 'Happy Customers',
        'counter_start' => 0,
        'counter_end' => 1000,
        'counter_duration' => 2000,
        'primary_button' => [
            'text' => 'Get Started',
            'url' => '#',
            'style' => 'primary'
        ],
        'secondary_button' => [
            'text' => 'Learn More',
            'url' => '#',
            'style' => 'secondary'
        ],
        'autoplay' => true,
        'autoplay_delay' => 5000,
        'background_color' => 'bg-gradient-to-r from-blue-600 to-purple-700',
        'text_color' => 'text-white',
        'overlay_opacity' => 'bg-opacity-50'
    ], $blockConfig ?? []);

    // Generate unique ID for this block instance
    $blockId = 'hero_carousel_' . uniqid();
@endphp

<div class="relative {{ $config['background_color'] }} min-h-screen flex items-center justify-center overflow-hidden"
     id="{{ $blockId }}"
     x-data="heroCarousel({
         images: {{ json_encode($config['images']) }},
         autoplay: {{ $config['autoplay'] ? 'true' : 'false' }},
         delay: {{ $config['autoplay_delay'] }},
         counterStart: {{ $config['counter_start'] }},
         counterEnd: {{ $config['counter_end'] }},
         counterDuration: {{ $config['counter_duration'] }}
     })"
     x-init="init()">

    {{-- Image Carousel Background --}}
    <div class="absolute inset-0 w-full h-full">
        @foreach($config['images'] as $index => $image)
            <div class="absolute inset-0 w-full h-full transition-opacity duration-1000"
                 x-show="currentSlide === {{ $index }}"
                 x-transition:enter="transition-opacity duration-1000"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-1000"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <img src="{{ $image['url'] }}"
                     alt="{{ $image['alt'] }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black {{ $config['overlay_opacity'] }}"></div>
            </div>
        @endforeach
    </div>

    {{-- Content Container --}}
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center {{ $config['text_color'] }}">

            {{-- Main Title --}}
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight"
                x-data="{ text: '{{ addslashes($config['title']) }}' }"
                x-init="$nextTick(() => { typeWriter($el, text, 50) })">
            </h1>

            {{-- Subtitle --}}
            <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-3xl mx-auto">
                {{ langHtml($config['subtitle']) }}
            </p>

            {{-- Description --}}
            <p class="text-lg mb-12 opacity-80 max-w-2xl mx-auto">
                {{ langHtml($config['description']) }}
            </p>

            {{-- Dynamic Counter --}}
            <div class="mb-12">
                <div class="inline-block bg-white bg-opacity-20 backdrop-blur-sm rounded-lg px-8 py-6">
                    <div class="text-5xl font-bold mb-2" x-text="Math.floor(counter)"></div>
                    <div class="text-sm uppercase tracking-wider opacity-75">
                        {{ langHtml($config['counter_label']) }}
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @if(!empty($config['primary_button']['text']))
                    <a href="{{ $config['primary_button']['url'] }}"
                       class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 transform hover:scale-105">
                        {{ langHtml($config['primary_button']['text']) }}
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                @endif

                @if(!empty($config['secondary_button']['text']))
                    <a href="{{ $config['secondary_button']['url'] }}"
                       class="inline-flex items-center px-8 py-4 border-2 border-white text-lg font-medium rounded-lg text-white hover:bg-white hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors duration-200">
                        {{ langHtml($config['secondary_button']['text']) }}
                    </a>
                @endif
            </div>

        </div>
    </div>

    {{-- Carousel Controls --}}
    @if(count($config['images']) > 1)
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
            <div class="flex space-x-3">
                @foreach($config['images'] as $index => $image)
                    <button @click="goToSlide({{ $index }})"
                            class="w-3 h-3 rounded-full transition-colors duration-200"
                            :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white bg-opacity-50'"
                            aria-label="Go to slide {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Navigation Arrows --}}
        <button @click="previousSlide()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 z-20 p-2 rounded-full bg-white bg-opacity-20 backdrop-blur-sm text-white hover:bg-opacity-30 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button @click="nextSlide()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 z-20 p-2 rounded-full bg-white bg-opacity-20 backdrop-blur-sm text-white hover:bg-opacity-30 transition-all duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    @endif

</div>

<script>
function heroCarousel(config) {
    return {
        currentSlide: 0,
        counter: config.counterStart,
        autoplayTimer: null,

        init() {
            this.startAutoplay();
            this.animateCounter();
        },

        startAutoplay() {
            if (config.autoplay && config.images.length > 1) {
                this.autoplayTimer = setInterval(() => {
                    this.nextSlide();
                }, config.delay);
            }
        },

        stopAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        },

        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % config.images.length;
        },

        previousSlide() {
            this.currentSlide = this.currentSlide === 0 ? config.images.length - 1 : this.currentSlide - 1;
        },

        goToSlide(index) {
            this.currentSlide = index;
            this.stopAutoplay();
            setTimeout(() => this.startAutoplay(), 3000);
        },

        animateCounter() {
            const start = config.counterStart;
            const end = config.counterEnd;
            const duration = config.counterDuration;
            const range = end - start;
            const increment = range / (duration / 16);

            const timer = setInterval(() => {
                this.counter += increment;
                if (this.counter >= end) {
                    this.counter = end;
                    clearInterval(timer);
                }
            }, 16);
        }
    }
}

function typeWriter(element, text, speed) {
    let i = 0;
    element.innerHTML = '';

    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }

    type();
}
</script>
