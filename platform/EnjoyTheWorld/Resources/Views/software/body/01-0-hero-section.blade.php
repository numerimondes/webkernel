<!-- resources/views/components/hero.blade.php -->
<!-- Single Blade file, Tailwind only, slider works even if OwlCarousel is not loaded.
     All code and comments are in English only. -->
<section
    class="relative min-h-[520px] w-full overflow-hidden hero-section"
    @php
        // Slider images with alt text defined here (no controller needed)
        $sliderImages = [
            ['src' => 'assets/img/banner/banner-01.jpg', 'alt' => 'Banner one overview'],
            ['src' => 'assets/img/banner/banner-02.jpg', 'alt' => 'Mountain landscape tour'],
            ['src' => 'assets/img/banner/banner-03.jpg', 'alt' => 'City night skyline'],
            ['src' => 'assets/img/banner/banner-04.jpg', 'alt' => 'Beach travel scene'],
        ];

        // Provide a safe first image URL for the section background inline style
        $firstImage = $sliderImages[0]['src'] ?? '';
    @endphp
    style="background-image: url('{{ asset($firstImage) }}'); background-size: cover; background-position: center center;"
>

    <!-- Slider DOM: kept as-is for OwlCarousel; also used by the fallback vanilla slider -->
    <div
        id="hero-slider"
        class="banner-slider banner-sec owl-carousel absolute inset-0 z-0"
        role="region"
        aria-roledescription="carousel"
        aria-label="Hero background images"
    >
        @foreach ($sliderImages as $index => $slide)
            <div
                class="slider-img w-full h-full"
                data-slide-index="{{ $index }}"
                aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
            >
                <img
                    src="{{ asset($slide['src']) }}"
                    alt="{{ $slide['alt'] }}"
                    class="w-full h-full object-cover block"
                    loading="lazy"
                />
            </div>
        @endforeach
    </div>

    <!-- Dark overlay for readability -->
    <div class="absolute inset-0 bg-black/40 z-10 pointer-events-none" aria-hidden="true"></div>

    <!-- Content wrapper -->
    <div class="relative z-20 w-full px-4 py-16">

        <!-- Text Block -->
        <div class="text-center mx-auto max-w-3xl mb-10">
            <h1 class="text-white text-4xl md:text-5xl font-semibold mb-3">
                Get Closer to the Dream:
                <span class="font-bold">Your Tour</span> Essentials Await
            </h1>

            <h6 class="text-gray-200 text-base md:text-lg max-w-2xl mx-auto">
                Your ultimate destination for all things that help you celebrate and remember your tour experience.
            </h6>
        </div>

        <!-- Card (centered, medium width) -->
        <div class="mx-auto max-w-5xl w-full">
            @include('enjoy-the-world::software.body.01-1-hero-section-card')
        </div>

    </div>

</section>

<!-- Inline scripts: initialize OwlCarousel if available, otherwise use a lightweight vanilla fallback -->
@once
    @push('scripts')
        <script>
            (function () {
                'use strict';

                const sliderEl = document.getElementById('hero-slider');
                if (!sliderEl) return;

                // Try to initialize OwlCarousel if jQuery + plugin exist
                try {
                    if (window.jQuery && typeof window.jQuery.fn.owlCarousel === 'function') {
                        // eslint-disable-next-line no-undef
                        window.jQuery('#hero-slider').owlCarousel({
                            items: 1,
                            loop: true,
                            autoplay: true,
                            autoplayTimeout: 5000,
                            autoplayHoverPause: true,
                            animateOut: 'fadeOut',
                            nav: false,
                            dots: true,
                            navText: [],
                            smartSpeed: 600,
                            singleItem: true,
                        });
                        return;
                    }
                } catch (e) {
                    // If Owl init fails, fallback to vanilla slider below
                    console.warn('OwlCarousel init failed, falling back to vanilla slider.', e);
                }

                // Vanilla JS fallback slider
                (function vanillaHeroSlider() {
                    const slides = Array.from(sliderEl.querySelectorAll('.slider-img'));
                    if (!slides.length) return;

                    // Ensure slides are positioned absolutely and stacked
                    sliderEl.style.position = 'absolute';
                    sliderEl.style.inset = '0';
                    sliderEl.style.overflow = 'hidden';

                    slides.forEach((slide, i) => {
                        slide.style.position = 'absolute';
                        slide.style.inset = '0';
                        slide.style.transition = 'opacity 700ms ease';
                        slide.style.opacity = i === 0 ? '1' : '0';
                        slide.style.zIndex = i === 0 ? '0' : '-1';
                    });

                    let current = 0;
                    const interval = 5000;
                    let timer = null;
                    let isPaused = false;

                    // Accessibility: update aria-hidden attributes
                    function updateAria() {
                        slides.forEach((s, idx) => {
                            s.setAttribute('aria-hidden', idx === current ? 'false' : 'true');
                        });
                    }

                    function showSlide(next) {
                        if (next === current) return;
                        const prevSlide = slides[current];
                        const nextSlide = slides[next];

                        prevSlide.style.opacity = '0';
                        prevSlide.style.zIndex = '-1';

                        nextSlide.style.opacity = '1';
                        nextSlide.style.zIndex = '0';

                        current = next;
                        updateAria();

                        // Update section background-image to keep the CSS background in sync (improves perceived performance)
                        try {
                            const img = nextSlide.querySelector('img');
                            if (img && img.src) {
                                const section = sliderEl.closest('section');
                                if (section) {
                                    section.style.backgroundImage = 'url(' + img.src + ')';
                                }
                            }
                        } catch (e) {
                            // ignore
                        }
                    }

                    function start() {
                        stop();
                        timer = setInterval(() => {
                            if (isPaused) return;
                            const next = (current + 1) % slides.length;
                            showSlide(next);
                        }, interval);
                    }

                    function stop() {
                        if (timer) {
                            clearInterval(timer);
                            timer = null;
                        }
                    }

                    // Pause on hover/focus for better UX
                    sliderEl.addEventListener('mouseenter', () => { isPaused = true; });
                    sliderEl.addEventListener('mouseleave', () => { isPaused = false; });
                    sliderEl.addEventListener('focusin', () => { isPaused = true; });
                    sliderEl.addEventListener('focusout', () => { isPaused = false; });

                    // Touch support: simple swipe left/right
                    let startX = 0;
                    let isTouching = false;
                    sliderEl.addEventListener('touchstart', (e) => {
                        if (!e.touches || !e.touches.length) return;
                        startX = e.touches[0].clientX;
                        isTouching = true;
                    }, { passive: true });

                    sliderEl.addEventListener('touchmove', (e) => {
                        if (!isTouching || !e.touches || !e.touches.length) return;
                        const dx = e.touches[0].clientX - startX;
                        // threshold not applied to slide elements to keep it simple
                        if (Math.abs(dx) > 60) {
                            if (dx < 0) {
                                showSlide((current + 1) % slides.length);
                            } else {
                                showSlide((current - 1 + slides.length) % slides.length);
                            }
                            isTouching = false;
                        }
                    }, { passive: true });

                    sliderEl.addEventListener('touchend', () => { isTouching = false; });

                    // Create simple dots for navigation for accessibility and control
                    const dots = document.createElement('div');
                    dots.className = 'hero-dots absolute left-1/2 -translate-x-1/2 bottom-6 z-30 flex gap-2';
                    dots.setAttribute('role', 'tablist');

                    slides.forEach((_, idx) => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'w-3 h-3 rounded-full bg-white/60';
                        btn.setAttribute('aria-label', 'Go to slide ' + (idx + 1));
                        btn.setAttribute('role', 'tab');
                        btn.setAttribute('aria-selected', idx === 0 ? 'true' : 'false');
                        btn.addEventListener('click', () => {
                            showSlide(idx);
                            start(); // reset timer
                        });
                        dots.appendChild(btn);
                    });

                    sliderEl.appendChild(dots);

                    // Update dot states when slide changes
                    const dotButtons = Array.from(dots.children);
                    const updateDots = () => {
                        dotButtons.forEach((d, i) => {
                            d.setAttribute('aria-selected', i === current ? 'true' : 'false');
                            d.classList.toggle('bg-white', i === current);
                            d.classList.toggle('bg-white/60', i !== current);
                        });
                    };

                    // Hook into showSlide to update dots
                    const originalShowSlide = showSlide;
                    showSlide = (next) => {
                        originalShowSlide(next);
                        updateDots();
                    };

                    // Start autoplay
                    updateAria();
                    updateDots();
                    start();

                })();

            })();
        </script>
    @endpush
@endonce
