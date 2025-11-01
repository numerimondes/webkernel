<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Store</title>
    @filamentStyles
    @filamentScripts
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    {{ filament()->getTheme()->getHtml() }}
    {{ filament()->getFontHtml() }}
    {{ filament()->getMonoFontHtml() }}
    {{ filament()->getSerifFontHtml() }}
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        @font-face {
            font-family: havana;
            src: url(https://clerk.com/_next/static/media/dc9f467134c6ab5e-s.p.woff) format("woff");
            font-display: block
        }

        @font-face {
            font-family: havana Fallback;
            src: local("Arial");
            ascent-override: 107.06%;
            descent-override: 29.03%;
            line-gap-override: 0.00%;
            size-adjust: 88.18%
        }

        @font-face {
            font-family: "SF Pro Display";
            src: url("https://cdn.fontcdn.ir/Fonts/SFProDisplay/5bc1142d5fc993d2ec21a8fa93a17718818e8172dffc649b7d8a3ab459cfbf9c.woff2") format("woff2");
            font-weight: 400;
            font-style: normal;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-sm">P</span>
                        </div>
                        <span class="text-xl font-medium">Play Store</span>
                    </div>
                </div>

                <div class="flex-1 max-w-2xl mx-8">
                    <div class="relative">
                        <input type="text" placeholder="Search for apps & games"
                            class="w-full px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-5 h-5 absolute left-3 top-2.5" />
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                        <span class="text-sm font-medium">U</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Navigation Tabs -->
        <div class="mb-8" x-data="{ activeTab: 'apps' }">
            <div class="flex space-x-8 border-b border-gray-200">
                <button @click="activeTab = 'apps'"
                    :class="activeTab === 'apps' ? 'border-green-500 text-green-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-1 border-b-2 font-medium text-sm">
                    Apps
                </button>
                <button @click="activeTab = 'games'"
                    :class="activeTab === 'games' ? 'border-green-500 text-green-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-1 border-b-2 font-medium text-sm">
                    Games
                </button>
                <button @click="activeTab = 'books'"
                    :class="activeTab === 'books' ? 'border-green-500 text-green-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-1 border-b-2 font-medium text-sm">
                    Books
                </button>
                <button @click="activeTab = 'movies'"
                    :class="activeTab === 'movies' ? 'border-green-500 text-green-600' :
                        'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-1 border-b-2 font-medium text-sm">
                    Movies & TV
                </button>
            </div>
        </div>
        <div class="overflow-hidden pb-48">
            <div class="pointer-events-none relative select-none" aria-hidden="true">
                <div class="absolute inset-x-0 h-px origin-left bg-gradient-to-r from-gray-600/0 via-gray-600/10 to-gray-600/0 transition duration-[2s]"
                    style="top: -17.1429%;"></div>
                <div
                    class="absolute inset-x-0 top-1/2 mt-[-0.5px] h-px origin-left bg-gradient-to-r from-gray-600/0 via-gray-600/10 to-gray-600/0 transition delay-500 duration-[2s]">
                </div>
                <div class="absolute inset-x-0 z-10 bg-gradient-to-b from-gray-50/0 to-gray-50 to-[calc(170/640*100%)]"
                    style="top: 38.9286%; height: 228.571%;"></div>
                <div class="mx-auto w-[17.5rem] max-w-[calc(100%-8rem)]">
                    <div class="relative aspect-square w-full" style="margin-top: 42.8571%;">
                        <div class="absolute left-1/2 top-1/2 z-0 transition delay-[1.3s] duration-[2s]"
                            style="margin-top: -200.357%; margin-left: -200.357%; width: 400.714%; height: 400.714%;">
                            <style>
                                @keyframes hero-ring {
                                    0% {
                                        transform: scale(0.8);
                                        opacity: 0;
                                    }

                                    10% {
                                        opacity: 0.06;
                                    }

                                    100% {
                                        transform: scale(2.5);
                                        opacity: 0;
                                    }
                                }
                            </style>
                            <div class="absolute inset-0 opacity-0 motion-reduce:![animation-delay:-1.2s] motion-reduce:![animation-play-state:paused]"
                                style="background: radial-gradient(100% 100%, rgba(0, 0, 0, 0) 12.8342%, rgba(0, 0, 0, 0.01) 13.1807%, rgba(0, 0, 0, 0.049) 13.4973%, rgba(0, 0, 0, 0.104) 13.7968%, rgba(0, 0, 0, 0.175) 14.0749%, rgba(0, 0, 0, 0.26) 14.3444%, rgba(0, 0, 0, 0.352) 14.5968%, rgba(0, 0, 0, 0.45) 14.8492%, rgba(0, 0, 0, 0.55) 15.0973%, rgba(0, 0, 0, 0.648) 15.3497%, rgba(0, 0, 0, 0.74) 15.6021%, rgba(0, 0, 0, 0.825) 15.8717%, rgba(0, 0, 0, 0.896) 16.1497%, rgba(0, 0, 0, 0.951) 16.4492%, rgba(0, 0, 0, 0.99) 16.7658%, rgb(0, 0, 0) 17.1123%, rgb(0, 0, 0) 17.1123%, rgba(0, 0, 0, 0.99) 17.4588%, rgba(0, 0, 0, 0.951) 17.7754%, rgba(0, 0, 0, 0.896) 18.0749%, rgba(0, 0, 0, 0.825) 18.3529%, rgba(0, 0, 0, 0.74) 18.6225%, rgba(0, 0, 0, 0.648) 18.8749%, rgba(0, 0, 0, 0.55) 19.1273%, rgba(0, 0, 0, 0.45) 19.3754%, rgba(0, 0, 0, 0.352) 19.6278%, rgba(0, 0, 0, 0.26) 19.8802%, rgba(0, 0, 0, 0.175) 20.1497%, rgba(0, 0, 0, 0.104) 20.4278%, rgba(0, 0, 0, 0.049) 20.7273%, rgba(0, 0, 0, 0.01) 21.0439%, rgba(0, 0, 0, 0) 21.3904%); animation: 12s linear -2.6s infinite hero-ring;">
                            </div>
                            <div class="absolute inset-0 opacity-0 motion-reduce:![animation-delay:-5s] motion-reduce:![animation-play-state:paused]"
                                style="background: radial-gradient(100% 100%, rgba(0, 0, 0, 0) 12.8342%, rgba(0, 0, 0, 0.01) 13.1807%, rgba(0, 0, 0, 0.049) 13.4973%, rgba(0, 0, 0, 0.104) 13.7968%, rgba(0, 0, 0, 0.175) 14.0749%, rgba(0, 0, 0, 0.26) 14.3444%, rgba(0, 0, 0, 0.352) 14.5968%, rgba(0, 0, 0, 0.45) 14.8492%, rgba(0, 0, 0, 0.55) 15.0973%, rgba(0, 0, 0, 0.648) 15.3497%, rgba(0, 0, 0, 0.74) 15.6021%, rgba(0, 0, 0, 0.825) 15.8717%, rgba(0, 0, 0, 0.896) 16.1497%, rgba(0, 0, 0, 0.951) 16.4492%, rgba(0, 0, 0, 0.99) 16.7658%, rgb(0, 0, 0) 17.1123%, rgb(0, 0, 0) 17.1123%, rgba(0, 0, 0, 0.99) 17.4588%, rgba(0, 0, 0, 0.951) 17.7754%, rgba(0, 0, 0, 0.896) 18.0749%, rgba(0, 0, 0, 0.825) 18.3529%, rgba(0, 0, 0, 0.74) 18.6225%, rgba(0, 0, 0, 0.648) 18.8749%, rgba(0, 0, 0, 0.55) 19.1273%, rgba(0, 0, 0, 0.45) 19.3754%, rgba(0, 0, 0, 0.352) 19.6278%, rgba(0, 0, 0, 0.26) 19.8802%, rgba(0, 0, 0, 0.175) 20.1497%, rgba(0, 0, 0, 0.104) 20.4278%, rgba(0, 0, 0, 0.049) 20.7273%, rgba(0, 0, 0, 0.01) 21.0439%, rgba(0, 0, 0, 0) 21.3904%); animation: 12s linear -6.6s infinite hero-ring;">
                            </div>
                            <div class="absolute inset-0 opacity-0 motion-reduce:![animation-delay:-8.8s] motion-reduce:![animation-play-state:paused]"
                                style="background: radial-gradient(100% 100%, rgba(0, 0, 0, 0) 12.8342%, rgba(0, 0, 0, 0.01) 13.1807%, rgba(0, 0, 0, 0.049) 13.4973%, rgba(0, 0, 0, 0.104) 13.7968%, rgba(0, 0, 0, 0.175) 14.0749%, rgba(0, 0, 0, 0.26) 14.3444%, rgba(0, 0, 0, 0.352) 14.5968%, rgba(0, 0, 0, 0.45) 14.8492%, rgba(0, 0, 0, 0.55) 15.0973%, rgba(0, 0, 0, 0.648) 15.3497%, rgba(0, 0, 0, 0.74) 15.6021%, rgba(0, 0, 0, 0.825) 15.8717%, rgba(0, 0, 0, 0.896) 16.1497%, rgba(0, 0, 0, 0.951) 16.4492%, rgba(0, 0, 0, 0.99) 16.7658%, rgb(0, 0, 0) 17.1123%, rgb(0, 0, 0) 17.1123%, rgba(0, 0, 0, 0.99) 17.4588%, rgba(0, 0, 0, 0.951) 17.7754%, rgba(0, 0, 0, 0.896) 18.0749%, rgba(0, 0, 0, 0.825) 18.3529%, rgba(0, 0, 0, 0.74) 18.6225%, rgba(0, 0, 0, 0.648) 18.8749%, rgba(0, 0, 0, 0.55) 19.1273%, rgba(0, 0, 0, 0.45) 19.3754%, rgba(0, 0, 0, 0.352) 19.6278%, rgba(0, 0, 0, 0.26) 19.8802%, rgba(0, 0, 0, 0.175) 20.1497%, rgba(0, 0, 0, 0.104) 20.4278%, rgba(0, 0, 0, 0.049) 20.7273%, rgba(0, 0, 0, 0.01) 21.0439%, rgba(0, 0, 0, 0) 21.3904%); animation: 12s linear -10.6s infinite hero-ring;">
                            </div>
                        </div>
                        <div class="absolute h-px origin-left rotate-45 bg-gray-600/10 transition duration-[2s]"
                            style="left: -42.8571%; top: -42.8571%; width: 262.857%;"></div>
                        <div class="absolute h-px origin-right -rotate-45 bg-gray-600/10 transition delay-500 duration-[2s]"
                            style="right: -42.8571%; top: -42.8571%; width: 262.857%;"></div>
                        <div class="absolute w-px origin-top bg-gray-600/10 transition duration-[2s]"
                            style="top: -42.8571%; bottom: -42.8571%; left: -17.1429%;"></div>
                        <div class="absolute left-1/2 -ml-px w-px origin-top bg-gray-600/10 transition delay-500 duration-[2s]"
                            style="top: -42.8571%; bottom: -42.8571%;"></div>
                        <div class="absolute w-px origin-top bg-gray-600/10 transition delay-1000 duration-[2s]"
                            style="top: -42.8571%; bottom: -42.8571%; right: -17.1429%;"></div><svg
                            viewBox="0 0 376 376" fill="none" stroke-width=".75" class="absolute stroke-gray-200"
                            style="inset: -17.1429%; width: 134.286%; height: 134.286%;">
                            <rect width="375.25" height="375.25" x="0.375" y="0.375" rx="64"
                                class="transition-opacity delay-1000 duration-[3s]" style="opacity: 1;"></rect>
                            <circle cx="188" cy="188" r="69.5" transform="rotate(-30 188 188)"
                                class="transition-all delay-200 duration-[2s]"
                                style="stroke-dasharray: 436.681px; stroke-dashoffset: 0px;"></circle>
                            <circle cx="188" cy="188" r="78.5" transform="rotate(-60 188 188)"
                                class="transition-all duration-[2s]"
                                style="stroke-dasharray: 493.23px; stroke-dashoffset: 0px;"></circle>
                            <circle cx="188" cy="188" r="140.5" transform="rotate(-120 188 188)"
                                class="transition-all delay-200 duration-[2s]"
                                style="stroke-dasharray: 882.788px; stroke-dashoffset: 0px;"></circle>
                            <circle cx="188" cy="188" r="155.5" transform="rotate(-150 188 188)"
                                class="transition-all duration-[2s]"
                                style="stroke-dasharray: 977.035px; stroke-dashoffset: 0px;"></circle>
                        </svg>
                        <div class="absolute"
                            style="mask: radial-gradient(100% 100%, transparent 22.4359%, black 22.4359%, black 25%, transparent 25%, transparent 45.1923%, black 45.1923%, black 49.6795%, transparent 49.6795%); inset: -5.71429%;">
                            <canvas class="absolute inset-0 h-full w-full" aria-hidden="true" width="312"
                                height="312"></canvas></div><img alt="" width="1260" height="1500"
                            decoding="async" data-nimg="1"
                            class="absolute -left-1/4 z-10 w-[150%] max-w-none mix-blend-multiply transition delay-1000 duration-1000"
                            style="color: transparent; top: -27.1429%; transform-origin: 50% 43.2% 0px;"
                            src="https://clerk.com/_next/static/media/logomark-1@3xq75.5bc48870.jpg"><img
                            alt="" width="1260" height="1500" decoding="async" data-nimg="1"
                            class="absolute -left-1/4 z-10 w-[150%] max-w-none mix-blend-multiply transition delay-[1.3s] duration-1000"
                            style="color: transparent; top: -27.1429%; transform-origin: 50% 43.2% 0px;"
                            src="https://clerk.com/_next/static/media/logomark-2@3xq75.01e69934.jpg"><img
                            alt="" width="1260" height="1500" decoding="async" data-nimg="1"
                            class="absolute -left-1/4 z-10 w-[150%] max-w-none mix-blend-multiply transition delay-[1.5s] duration-1000"
                            style="color: transparent; top: -27.1429%; transform-origin: 50% 43.2% 0px;"
                            src="https://clerk.com/_next/static/media/logomark-3@3xq75.08c7c2eb.jpg">
                        <div class="absolute -left-1/4 z-10 aspect-[420/500] w-[150%] transition-opacity delay-1000 duration-1000"
                            style="top: -27.1429%;"><svg viewBox="0 0 420 500" fill="none"
                                class="absolute inset-0 size-full">
                                <g filter="url(#filter0_ii_1087_25)">
                                    <path fill="url(#paint2_linear_1087_25)"
                                        d="M287.811 99.599c4.374 2.929 4.747 9.092 1.025 12.814l-31.972 31.972c-2.89 2.89-7.372 3.346-11.009 1.483-10.755-5.51-22.943-8.618-35.857-8.618-43.492 0-78.749 35.258-78.749 78.75 0 12.914 3.108 25.102 8.618 35.857 1.863 3.637 1.406 8.119-1.483 11.009l-31.972 31.972c-3.722 3.722-9.885 3.349-12.814-1.025C78.692 271.56 69.999 244.795 69.999 216c0-77.32 62.68-140 139.999-140 28.795 0 55.56 8.693 77.813 23.599Z">
                                    </path>
                                </g>
                                <defs>
                                    <linearGradient id="paint2_linear_1087_25" x1="70" x2="317.5"
                                        y1="76" y2="323.5" gradientUnits="userSpaceOnUse">
                                        <stop offset=".162" stop-color="#fff"></stop>
                                        <stop offset=".57" stop-color="#fff" stop-opacity=".2"></stop>
                                        <stop offset=".925" stop-color="#fff"></stop>
                                    </linearGradient>
                                    <filter id="filter0_ii_1087_25" width="221.383" height="282.999" x="69.999"
                                        y="75" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                                        <feBlend in="SourceGraphic" in2="BackgroundImageFix" result="shape">
                                        </feBlend>
                                        <feColorMatrix in="SourceAlpha" result="hardAlpha"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix>
                                        <feOffset dy="2"></feOffset>
                                        <feGaussianBlur stdDeviation="1.5"></feGaussianBlur>
                                        <feComposite in2="hardAlpha" k2="-1" k3="1"
                                            operator="arithmetic"></feComposite>
                                        <feColorMatrix values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 1 0">
                                        </feColorMatrix>
                                        <feBlend in2="shape" result="effect1_innerShadow_1087_25"></feBlend>
                                        <feColorMatrix in="SourceAlpha" result="hardAlpha"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix>
                                        <feOffset dy="-1"></feOffset>
                                        <feGaussianBlur stdDeviation="1.5"></feGaussianBlur>
                                        <feComposite in2="hardAlpha" k2="-1" k3="1"
                                            operator="arithmetic"></feComposite>
                                        <feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0">
                                        </feColorMatrix>
                                        <feBlend in2="effect1_innerShadow_1087_25"
                                            result="effect2_innerShadow_1087_25"></feBlend>
                                    </filter>
                                </defs>
                            </svg></div>
                        <div class="absolute -left-1/4 z-10 aspect-[420/500] w-[150%] transition-opacity delay-[1.3s] duration-1000"
                            style="top: -27.1429%;"><svg viewBox="0 0 420 500" fill="none"
                                class="absolute inset-0 size-full">
                                <g filter="url(#filter0_ii_1087_25)">
                                    <path fill="url(#paint1_linear_1087_25)"
                                        d="M287.805 332.4c4.373-2.929 4.747-9.092 1.025-12.814l-31.972-31.972c-2.89-2.89-7.372-3.346-11.009-1.483-10.755 5.51-22.943 8.618-35.857 8.618-12.914 0-25.102-3.108-35.856-8.618-3.637-1.863-8.12-1.407-11.01 1.483l-31.972 31.972c-3.722 3.722-3.348 9.885 1.025 12.814 22.253 14.906 49.018 23.599 77.813 23.599s55.56-8.693 77.813-23.599Z">
                                    </path>
                                </g>
                                <defs>
                                    <linearGradient id="paint1_linear_1087_25" x1="70" x2="317.5"
                                        y1="76" y2="323.5" gradientUnits="userSpaceOnUse">
                                        <stop offset=".162" stop-color="#fff"></stop>
                                        <stop offset=".57" stop-color="#fff" stop-opacity=".2"></stop>
                                        <stop offset=".925" stop-color="#fff"></stop>
                                    </linearGradient>
                                </defs>
                            </svg></div>
                        <div class="absolute -left-1/4 z-10 aspect-[420/500] w-[150%] transition-opacity delay-[1.5s] duration-1000"
                            style="top: -27.1429%;"><svg viewBox="0 0 420 500" fill="none"
                                class="absolute inset-0 size-full">
                                <g filter="url(#filter0_ii_1087_25)">
                                    <path fill="url(#paint0_linear_1087_25)"
                                        d="M210.003 259.75c24.162 0 43.75-19.588 43.75-43.75s-19.588-43.75-43.75-43.75-43.75 19.588-43.75 43.75 19.588 43.75 43.75 43.75Z">
                                    </path>
                                </g>
                                <defs>
                                    <linearGradient id="paint0_linear_1087_25" x1="70" x2="317.5"
                                        y1="76" y2="323.5" gradientUnits="userSpaceOnUse">
                                        <stop offset=".162" stop-color="#fff"></stop>
                                        <stop offset=".57" stop-color="#fff" stop-opacity=".2"></stop>
                                        <stop offset=".925" stop-color="#fff"></stop>
                                    </linearGradient>
                                </defs>
                            </svg></div>
                        <picture>
                            <source srcset="https://clerk.com/_next/static/media/glow-hero@q40.da0460a3.avif"
                                type="image/avif">
                            <source srcset="https://clerk.com/_next/static/media/glow-hero@q75.56a5515b.webp"
                                type="image/webp"><img alt="" width="746" height="960"
                                decoding="async" data-nimg="1"
                                class="absolute z-10 max-w-none mix-blend-overlay transition-opacity delay-[1.2s] duration-1000"
                                style="color: transparent; top: -121.071%; left: -93.2143%; width: 266.429%;"
                                src="https://clerk.com/_next/static/media/glow-hero@q40.da0460a3.avif">
                        </picture>
                    </div>
                </div>
                <div
                    class="absolute left-1/2 top-[-27rem] z-10 ml-[-45rem] h-[70.125rem] w-[90rem] bg-[linear-gradient(117deg,#00E7FF_22.96%,#6A4AFF_64.4%)] mix-blend-overlay [mask:radial-gradient(50%_50%,black,transparent)]">
                </div>
            </div>
            <div
                class="relative z-10 mt-12 mx-auto w-full px-6 sm:max-w-[40rem] md:max-w-[48rem] md:px-8 lg:max-w-[64rem] xl:max-w-[80rem]">
                <h1 class="sr-only">Careers at Clerk</h1>
                <p class="mx-auto max-w-lg text-balance text-center text-4.5xl font-semibold leading-none text-gray-950 sm:text-5.5xl sm:font-semibold sm:leading-none"
                    style="font-size: 3.5rem;font-family: "SF Pro Display", -apple-system,
                    BlinkMacSystemFont, "Helvetica Neue" , Helvetica, Arial, sans-serif;">We’re solving user management
                    <span
                        class="relative block bg-[radial-gradient(80%_100%_at_27.82%_35.44%,#00E7FF_6.16%,#6A4AFF_100%)] bg-clip-text pb-7 text-[3.25rem]/[2.75rem] font-normal tracking-normal text-transparent [text-shadow:0_1px_3px_rgba(97,157,251,0.14)] sm:mt-0 sm:text-[4.25rem]/[3.875rem] __className_916469"
                        style="font-family: havana,havana Fallback;font-size: 4.25rem;">once and for all.</span></p>
                <div class="flex justify-center"><a href="#open-roles"
                        class="group relative isolate inline-flex items-center justify-center overflow-hidden text-left font-medium transition duration-300 ease-[cubic-bezier(0.4,0.36,0,1)] before:duration-300 before:ease-[cubic-bezier(0.4,0.36,0,1)] before:transtion-opacity rounded-md shadow-[0_1px_theme(colors.white/0.07)_inset,0_1px_3px_theme(colors.gray.900/0.2)] before:pointer-events-none before:absolute before:inset-0 before:-z-10 before:rounded-md before:bg-gradient-to-b before:from-white/20 before:opacity-50 hover:before:opacity-100 after:pointer-events-none after:absolute after:inset-0 after:-z-10 after:rounded-md after:bg-gradient-to-b after:from-white/10 after:from-[46%] after:to-[54%] after:mix-blend-overlay text-sm h-[1.875rem] px-3 ring-1 bg-purple-500 text-white ring-purple-500"
                        target="">See open positions<svg viewBox="0 0 10 10" aria-hidden="true"
                            class="ml-2 h-2.5 w-2.5 flex-none opacity-60 group-hover:translate-x-6 group-hover:opacity-0 transition duration-300 ease-[cubic-bezier(0.4,0.36,0,1)] before:duration-300 before:ease-[cubic-bezier(0.4,0.36,0,1)] before:transtion-opacity">
                            <path fill="currentColor" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="1.5" d="m7.25 5-3.5-2.25v4.5L7.25 5Z"></path>
                        </svg><svg viewBox="0 0 10 10" aria-hidden="true"
                            class="-ml-2.5 h-2.5 w-2.5 flex-none -translate-x-2 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition duration-300 ease-[cubic-bezier(0.4,0.36,0,1)] before:duration-300 before:ease-[cubic-bezier(0.4,0.36,0,1)] before:transtion-opacity">
                            <path fill="currentColor" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="1.5" d="m7.25 5-3.5-2.25v4.5L7.25 5Z"></path>
                        </svg></a></div>
            </div>
        </div>
        <!-- Header avec frise et mascottes -->
        <div class="dark:border-polar-800 flex flex-col items-center justify-center gap-12 overflow-hidden rounded-3xl py-16 text-center md:py-24 dark:border relative z-10"
            style="opacity: 1;"><img alt="" fetchpriority="high" decoding="async" data-nimg="fill"
                class="absolute inset-0 -z-10 h-full w-full object-cover object-[50%_70%]"
                style="position:absolute;height:100%;width:100%;left:0;top:0;right:0;bottom:0;color:transparent"
                sizes="(max-width: 768px) 100vw, (max-width: 1280px) 100vw, 1280px"
                srcset="https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=640&amp;q=75 640w, https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=750&amp;q=75 750w, https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=828&amp;q=75 828w, https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=1080&amp;q=75 1080w, https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=1200&amp;q=75 1200w, https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=1920&amp;q=75 1920w, https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=2048&amp;q=75 2048w, https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=3840&amp;q=75 3840w"
                src="https://polar.sh/_next/image?url=%2Fassets%2Flanding%2Fhero.jpg&amp;w=3840&amp;q=75">
            <h1 class="text-balance text-5xl !leading-tight tracking-tight text-white md:px-0 md:text-7xl dark:text-white"
                style="opacity: 1;">Payment infrastructure for the 21st century</h1>
            <p class="text-pretty text-2xl !leading-tight text-white md:px-0 md:text-3xl" style="opacity: 1;">The
                modern
                way to sell your SaaS and digital products</p>
            <div class="flex flex-row items-center gap-x-4" style="opacity: 1;"><button
                    class="gap-2 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 relative inline-flex items-center select-none justify-center ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 whitespace-nowrap hover:opacity-85 transition-opacity duration-100 border border-white/10 h-12 px-5 py-4 text-sm rounded-full bg-white font-medium text-black hover:bg-gray-100 dark:bg-white dark:text-black"
                    type="button">
                    <div class="flex flex-row items-center gap-x-2">
                        <div>Get Started</div> <x-filament::icon icon="heroicon-s-arrow-right" class="text-lg" />
                    </div>
                </button></div>
        </div>


        <div class="mt-16 pt-16 mx-auto w-full sm:max-w-[40rem] md:max-w-[48rem]  lg:max-w-[64rem] xl:max-w-[80rem]"
            id="b2b-saas">
            <div class="">
                <h2 class="text-sm font-medium text-purple-500">B2B SaaS Suite</h2>
                <p class="mt-4 text-balance text-3xl font-semibold tracking-[-0.015em] text-gray-950">The easy solution
                    to multi-tenancy</p>
                <p class="mb-4 mt-4 max-w-md text-pretty text-base/6 text-gray-600">Clerk has all the features you need
                    to onboard and manage the users and organizations of your multi-tenant SaaS application.</p><a
                    class="group relative isolate inline-flex items-center justify-center overflow-hidden text-left font-medium transition duration-300 ease-[cubic-bezier(0.4,0.36,0,1)] before:duration-300 before:ease-[cubic-bezier(0.4,0.36,0,1)] before:transtion-opacity text-gray-950 text-sm text-gray-950"
                    target="" href="/b2b-saas">Explore B2B features <x-filament::icon
                        icon="heroicon-s-arrow-right"
                        class="ml-2 h-2.5 w-2.5 flex-none opacity-60 group-hover:translate-x-6 group-hover:opacity-0 transition duration-300 ease-[cubic-bezier(0.4,0.36,0,1)] before:duration-300 before:ease-[cubic-bezier(0.4,0.36,0,1)] before:transtion-opacity" />
                    <x-filament::icon icon="heroicon-s-arrow-right"
                        class="-ml-2.5 h-2.5 w-2.5 flex-none -translate-x-2 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition duration-300 ease-[cubic-bezier(0.4,0.36,0,1)] before:duration-300 before:ease-[cubic-bezier(0.4,0.36,0,1)] before:transtion-opacity" /></a>
            </div>
            <div
                class="mt-12 grid grid-flow-col grid-cols-1 grid-rows-6 gap-2 md:grid-cols-2 md:grid-rows-3 xl:grid-cols-3 xl:grid-rows-2">
                <div
                    class="group isolate flex flex-col rounded-2xl bg-white shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)] overflow-hidden row-span-2">
                    <div class="relative z-10 flex-none px-6 pt-6">
                        <h3 class="text-sm font-medium text-gray-950">Custom roles and permissions</h3>
                        <p class="mt-2 text-pretty text-sm/5 text-gray-600">Powerful primitives to fully customize your
                            app's authorization story.</p>
                    </div>
                    <div class="pointer-events-none relative flex-auto select-none" style="min-height:10.25rem"
                        aria-hidden="true"><svg width="0" height="0" aria-hidden="true">
                            <defs>
                                <pattern id="wave" width="12" height="3" patternUnits="userSpaceOnUse">
                                    <path fill="none" stroke="white" stroke-opacity="0.1"
                                        d="M-6 0c3 2 6 0 6 0s3-2 6 0 6 0 6 0 3-2 6 0M-6 3c3 2 6 0 6 0s3-2 6 0 6 0 6 0 3-2 6 0">
                                    </path>
                                </pattern>
                            </defs>
                        </svg>
                        <div class="flex h-full flex-col items-center justify-center">
                            <div class="grid w-max grid-cols-3 grid-rows-3 gap-2">
                                <div class="rounded-lg ring-1 ring-gray-900/7.5"></div>
                                <div
                                    class="rounded-lg p-1 ring-1 ring-gray-900/5 transition duration-1000 shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)]">
                                    <div class="relative overflow-hidden rounded bg-gray-50 transition duration-1000 opacity-70 mix-blend-luminosity"
                                        style="background:radial-gradient(241.22% 160.71% at 49.27% -9.52%, rgb(108 71 255 / 0.3), rgb(255 249 99 / 0.24) 41.24%, rgb(56 218 253 / 0.18) 62.34%, rgb(98 72 246 / 0) 91.89%)">
                                        <img alt="" loading="lazy" width="72" height="84"
                                            decoding="async" data-nimg="1"
                                            class="h-[5.25rem] w-[4.5rem] object-cover mix-blend-multiply"
                                            style="color:transparent"
                                            srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-1%403x.7731fb48.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-1%403x.7731fb48.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                            src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-1%403x.7731fb48.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina"><svg
                                            class="absolute inset-0 size-full" aria-hidden="true">
                                            <rect width="100%" height="100%" fill="url(#wave)"></rect>
                                        </svg>
                                        <div class="absolute inset-0 rounded ring-1 ring-inset ring-black/5"></div>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-gray-800/7.5 ring-1 ring-gray-900/12.5"></div>
                                <div
                                    class="rounded-lg p-1 ring-1 ring-gray-900/5 transition duration-1000 shadow-[0_1px_1px_rgba(0,0,0,0.05),0_5px_11px_rgba(34,42,53,0.14),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)]">
                                    <div class="relative overflow-hidden rounded bg-gray-50 transition duration-1000"
                                        style="background:radial-gradient(241.22% 160.71% at 49.27% -9.52%, rgb(108 71 255 / 0.3), rgb(255 249 99 / 0.24) 41.24%, rgb(56 218 253 / 0.18) 62.34%, rgb(98 72 246 / 0) 91.89%)">
                                        <img alt="" loading="lazy" width="72" height="84"
                                            decoding="async" data-nimg="1"
                                            class="h-[5.25rem] w-[4.5rem] object-cover mix-blend-multiply"
                                            style="color:transparent"
                                            srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-2%403x.b3a59918.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-2%403x.b3a59918.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                            src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-2%403x.b3a59918.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina"><svg
                                            class="absolute inset-0 size-full" aria-hidden="true">
                                            <rect width="100%" height="100%" fill="url(#wave)"></rect>
                                        </svg>
                                        <div class="absolute inset-0 rounded ring-1 ring-inset ring-black/5"></div>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-gray-800/2.5 ring-1 ring-gray-900/7.5"></div>
                                <div
                                    class="rounded-lg p-1 ring-1 ring-gray-900/5 transition duration-1000 shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)]">
                                    <div class="relative overflow-hidden rounded bg-gray-50 transition duration-1000 opacity-70 mix-blend-luminosity"
                                        style="background:radial-gradient(241.22% 160.71% at 49.27% -9.52%, rgb(108 71 255 / 0.3), rgb(255 249 99 / 0.24) 41.24%, rgb(56 218 253 / 0.18) 62.34%, rgb(98 72 246 / 0) 91.89%)">
                                        <img alt="" loading="lazy" width="72" height="84"
                                            decoding="async" data-nimg="1"
                                            class="h-[5.25rem] w-[4.5rem] object-cover mix-blend-multiply"
                                            style="color:transparent"
                                            srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-3%403x.ad6cd713.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-3%403x.ad6cd713.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                            src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-3%403x.ad6cd713.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina"><svg
                                            class="absolute inset-0 size-full" aria-hidden="true">
                                            <rect width="100%" height="100%" fill="url(#wave)"></rect>
                                        </svg>
                                        <div class="absolute inset-0 rounded ring-1 ring-inset ring-black/5"></div>
                                    </div>
                                </div>
                                <div class="rounded-lg ring-1 ring-gray-900/7.5"></div>
                                <div
                                    class="rounded-lg p-1 ring-1 ring-gray-900/5 transition duration-1000 shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)]">
                                    <div class="relative overflow-hidden rounded bg-gray-50 transition duration-1000 opacity-70 mix-blend-luminosity"
                                        style="background:radial-gradient(241.22% 160.71% at 49.27% -9.52%, rgb(108 71 255 / 0.3), rgb(255 249 99 / 0.24) 41.24%, rgb(56 218 253 / 0.18) 62.34%, rgb(98 72 246 / 0) 91.89%)">
                                        <img alt="" loading="lazy" width="72" height="84"
                                            decoding="async" data-nimg="1"
                                            class="h-[5.25rem] w-[4.5rem] object-cover mix-blend-multiply"
                                            style="color:transparent"
                                            srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-4%403x.a02540ea.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-4%403x.a02540ea.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                            src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-4%403x.a02540ea.png&amp;w=256&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina"><svg
                                            class="absolute inset-0 size-full" aria-hidden="true">
                                            <rect width="100%" height="100%" fill="url(#wave)"></rect>
                                        </svg>
                                        <div class="absolute inset-0 rounded ring-1 ring-inset ring-black/5"></div>
                                    </div>
                                </div>
                                <div class="rounded-lg ring-1 ring-gray-900/7.5"></div>
                            </div>
                            <div class="relative mt-10 w-full">
                                <div class="flex gap-x-3" style="transform: translateX(-242.092px);">
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span class="relative z-10">Product Member</span>
                                    </div>
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span class="relative z-10">Administrator</span>
                                    </div>
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span class="relative z-10">Editor</span>
                                    </div>
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span class="relative z-10">QA Tester</span>
                                    </div>
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span class="relative z-10">Owner</span>
                                    </div>
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span
                                            class="absolute inset-0 z-20 bg-gray-25 mix-blend-exclusion ring-1 ring-gray-800/10"
                                            style="border-radius: 9999px; transform: none; transform-origin: 50% 50% 0px;"></span><span
                                            class="relative z-10">Engineer</span>
                                    </div>
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span class="relative z-10">Marketing</span>
                                    </div>
                                    <div
                                        class="relative whitespace-nowrap rounded-full px-2 py-1 text-2xs font-medium text-gray-300 transition duration-500">
                                        <span class="relative z-10">Human Resources</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="group isolate flex flex-col rounded-2xl bg-white shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)] overflow-hidden order-first xl:order-none">
                    <div class="relative z-10 flex-none px-6 order-last pb-6">
                        <h3 class="text-sm font-medium text-gray-950">Auto-join</h3>
                        <p class="mt-2 text-pretty text-sm/5 text-gray-600">Let your users discover and join
                            organizations based on their email domain.</p>
                    </div>
                    <div class="pointer-events-none relative flex-auto select-none" style="min-height:10.25rem"
                        aria-hidden="true">
                        <div class="relative flex h-full flex-col items-center justify-center">
                            <div class="absolute -z-10 mt-[calc(-108/16*1rem)] blur-[1px]">
                                <div class="absolute left-1/2 top-1/2 ml-[calc(-216/2/16*1rem)] mt-[calc(-216/2/16*1rem)] size-[calc(216/16*1rem)] rounded-full border border-gray-400 opacity-15"
                                    style="transform: scale(1);"></div>
                                <div class="absolute left-1/2 top-1/2 ml-[calc(-280/2/16*1rem)] mt-[calc(-280/2/16*1rem)] size-[calc(280/16*1rem)] rounded-full border border-gray-400 opacity-12.5"
                                    style="transform: scale(1);"></div>
                                <div class="absolute left-1/2 top-1/2 ml-[calc(-344/2/16*1rem)] mt-[calc(-344/2/16*1rem)] size-[calc(344/16*1rem)] rounded-full border border-gray-400 opacity-10"
                                    style="transform:scale(1)"></div>
                                <div class="absolute left-1/2 top-1/2 ml-[calc(-408/2/16*1rem)] mt-[calc(-408/2/16*1rem)] size-[calc(408/16*1rem)] rounded-full border border-gray-400 opacity-7.5"
                                    style="transform:scale(1)"></div>
                            </div>
                            <div class="flex gap-4">
                                <div class="transition duration-1000 opacity-25">
                                    <div
                                        class="size-10 rounded-full border-2 border-white bg-gray-50 shadow-[0_2px_3px_rgba(0,0,0,0.04),0_24px_68px_rgba(47,48,55,0.05),0_4px_6px_rgba(34,42,53,0.04),0_1px_1px_rgba(0,0,0,0.05)] ring-1 ring-gray-950/5">
                                        <img alt="" loading="lazy" width="36" height="36"
                                            decoding="async" data-nimg="1" class="rounded-full"
                                            style="color:transparent"
                                            srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-1%403x.cdc8ffca.png&amp;w=48&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-1%403x.cdc8ffca.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                            src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-1%403x.cdc8ffca.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina">
                                    </div>
                                </div>
                                <div class="transition duration-1000 opacity-25">
                                    <div
                                        class="size-10 rounded-full border-2 border-white bg-gray-50 shadow-[0_2px_3px_rgba(0,0,0,0.04),0_24px_68px_rgba(47,48,55,0.05),0_4px_6px_rgba(34,42,53,0.04),0_1px_1px_rgba(0,0,0,0.05)] ring-1 ring-gray-950/5">
                                        <img alt="" loading="lazy" width="36" height="36"
                                            decoding="async" data-nimg="1" class="rounded-full"
                                            style="color:transparent"
                                            srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-2%403x.7451cd97.png&amp;w=48&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-2%403x.7451cd97.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                            src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-2%403x.7451cd97.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina">
                                    </div>
                                </div>
                                <div class="transition duration-1000 opacity-1">
                                    <div
                                        class="size-10 rounded-full border-2 border-white bg-gray-50 shadow-[0_2px_3px_rgba(0,0,0,0.04),0_24px_68px_rgba(47,48,55,0.05),0_4px_6px_rgba(34,42,53,0.04),0_1px_1px_rgba(0,0,0,0.05)] ring-1 ring-gray-950/5">
                                        <img alt="" loading="lazy" width="36" height="36"
                                            decoding="async" data-nimg="1" class="rounded-full"
                                            style="color:transparent"
                                            srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-3%403x.10103d7a.png&amp;w=48&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-3%403x.10103d7a.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                            src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-sm-3%403x.10103d7a.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina">
                                    </div>
                                </div>
                            </div>
                            <div class="relative aspect-[128/55] w-32"><svg viewBox="0 0 128 55" fill="none"
                                    aria-hidden="true" class="absolute inset-0 size-full stroke-gray-950/10">
                                    <path
                                        d="M64 0v25M8 0v8c0 8.837 7.163 16 16 16h24c8.837 0 16 7.163 16 16v15M120 0v8c0 8.837-7.163 16-16 16H80c-5.922 0-11.093 3.218-13.86 8">
                                    </path>
                                </svg></div>
                            <div
                                class="relative mt-px flex items-center gap-1.5 rounded-lg bg-white py-1 pl-1.5 pr-2 text-2xs font-medium text-gray-950 shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)] ring-1 ring-gray-950/5">
                                <x-filament::icon icon="heroicon-o-plus-circle" class="size-4" />Auto-join<div
                                    class="absolute -bottom-1.5 left-1/2 -z-10 -ml-10 h-6 w-20 transform-gpu rounded-[50%] bg-gradient-to-r from-purple-500 from-25% to-sky-300 to-75% blur-sm"
                                    style="opacity: 0.25;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="group isolate flex flex-col rounded-2xl bg-white shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)] overflow-hidden">
                    <div class="relative z-10 flex-none px-6 order-last pb-6">
                        <h3 class="text-sm font-medium text-gray-950">Invitations</h3>
                        <p class="mt-2 text-pretty text-sm/5 text-gray-600">Fuel your application's growth by making it
                            simple for your customers to invite their team.</p>
                    </div>
                    <div class="pointer-events-none relative flex-auto select-none" style="min-height:10.25rem"
                        aria-hidden="true">
                        <div
                            class="flex h-full items-center justify-center [mask:linear-gradient(black_66%,transparent)]">
                            <div
                                class="relative flex items-center gap-1.5 rounded-full bg-gray-800 px-2 py-1 text-2xs font-medium text-white shadow-[0_2px_13px_rgba(0,0,0,0.2),0_2px_4px_rgba(47,48,55,0.3)] ring-1 ring-gray-800">
                                <x-filament::icon icon="heroicon-o-envelope" class="size-4" />Invite this person<div
                                    class="absolute inset-0 -z-10 rounded-full bg-gray-950/5"
                                    style="transform:scaleX(1) scaleY(1);opacity:0"></div>
                                <div class="absolute inset-0 -z-10 rounded-full bg-gray-950/5"
                                    style="transform:scaleX(1) scaleY(1);opacity:0"></div>
                                <div class="absolute inset-0 -z-10 rounded-full bg-gray-950/5"
                                    style="transform:scaleX(1) scaleY(1);opacity:0"></div>
                                <div class="absolute left-1/2 top-1/2 -z-10 -ml-36 -mt-32 aspect-[288/256] w-72"><svg
                                        viewBox="0 0 288 256" fill="none" aria-hidden="true"
                                        class="absolute inset-0 size-full stroke-gray-950/10">
                                        <path d="M4 0v112c0 8.837 7.163 16 16 16h248c8.837 0 16 7.163 16 16v112"></path>
                                    </svg></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="group isolate flex flex-col rounded-2xl bg-white shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)] overflow-hidden row-span-2">
                    <div class="relative z-10 flex-none px-6 pt-6">
                        <h3 class="text-sm font-medium text-gray-950">Organization UI Components</h3>
                        <p class="mt-2 text-pretty text-sm/5 text-gray-600">Clerk's UI components add turn-key
                            simplicity to complex Organization management tasks.</p>
                    </div>
                    <div class="pointer-events-none relative flex-auto select-none" style="min-height:10.25rem"
                        aria-hidden="true">
                        <div class="flex h-full flex-col items-center justify-center px-12">
                            <div
                                class="flex items-center rounded-lg px-2 py-1 text-2xs font-medium text-gray-950 shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)] ring-1 ring-gray-900/5">
                                <x-filament::icon icon="heroicon-o-user-group"
                                    class="mr-1.5 size-4" />Clerk<x-filament::icon icon="heroicon-s-check"
                                    class="ml-3 size-4" />
                            </div>
                            <div class="relative mt-4 w-full">
                                <div
                                    class="absolute inset-0 rounded-xl border border-dashed border-gray-150 bg-gray-25/50">
                                </div>
                                <div class="relative w-full"
                                    style="opacity: 0; transform: scale(0.95); filter: blur(8px);">
                                    <div
                                        class="absolute -bottom-1 left-[calc((304-264)/304*50%)] -z-10 h-6 w-[calc(264/304*100%)] rounded-[50%] bg-gradient-to-r from-purple-500 from-25% to-sky-300 to-75% opacity-25 blur-sm">
                                    </div>
                                    <div
                                        class="overflow-hidden rounded-xl bg-gray-50/80 shadow-[0_2px_13px_rgba(0,0,0,0.08),0_15px_35px_-5px_rgba(25,28,33,0.12)] ring-1 ring-gray-950/5 backdrop-blur-[10px]">
                                        <div
                                            class="rounded-b-xl bg-white shadow-[0_1px_1px_rgba(0,0,0,0.05),0_4px_6px_rgba(34,42,53,0.04),0_24px_68px_rgba(47,48,55,0.05),0_2px_3px_rgba(0,0,0,0.04)] ring-1 ring-gray-900/5">
                                            <div
                                                class="flex items-center gap-3 px-4 py-3 [&amp;:not(:first-child)]:border-t [&amp;:not(:first-child)]:border-gray-400/10">
                                                <div
                                                    class="flex-none rounded p-0.5 shadow-[0_2px_3px_rgba(0,0,0,0.04),0_24px_68px_rgba(47,48,55,0.05),0_4px_6px_rgba(34,42,53,0.04),0_1px_1px_rgba(0,0,0,0.05)] ring-1 ring-gray-900/5">
                                                    <x-filament::icon icon="heroicon-o-check-circle" class="size-9" />
                                                </div>
                                                <div class="flex-auto text-2xs">
                                                    <div class="font-book text-gray-950">Bluth Company</div>
                                                    <div class="text-gray-400">Mr. Manager</div>
                                                </div>
                                                <div
                                                    class="flex-none rounded-md shadow-[0_2px_3px_-1px_rgba(0,0,0,0.08),0_1px_rgba(25,28,33,0.02)] ring-1 ring-gray-950/7.5">
                                                    <x-filament::icon icon="heroicon-o-cog-6-tooth" class="size-6" />
                                                </div>
                                            </div>
                                            <div
                                                class="flex items-center gap-3 px-4 py-3 [&amp;:not(:first-child)]:border-t [&amp;:not(:first-child)]:border-gray-400/10">
                                                <div
                                                    class="flex-none rounded p-0.5 shadow-[0_2px_3px_rgba(0,0,0,0.04),0_24px_68px_rgba(47,48,55,0.05),0_4px_6px_rgba(34,42,53,0.04),0_1px_1px_rgba(0,0,0,0.05)] ring-1 ring-gray-900/5">
                                                    <x-filament::icon icon="heroicon-o-building-office"
                                                        class="size-9 text-gray-600" />
                                                </div>
                                                <div class="flex-auto text-2xs">
                                                    <div class="font-book text-gray-950">Dunder Mifflin</div>
                                                    <div class="text-gray-400">Asst (to the) Regional Manager</div>
                                                </div>
                                            </div>
                                            <div
                                                class="flex items-center gap-3 px-4 py-3 [&amp;:not(:first-child)]:border-t [&amp;:not(:first-child)]:border-gray-400/10">
                                                <div
                                                    class="flex-none rounded p-0.5 shadow-[0_2px_3px_rgba(0,0,0,0.04),0_24px_68px_rgba(47,48,55,0.05),0_4px_6px_rgba(34,42,53,0.04),0_1px_1px_rgba(0,0,0,0.05)] ring-1 ring-gray-900/5">
                                                    <img alt="" loading="lazy" width="36" height="36"
                                                        decoding="async" data-nimg="1" class="size-9 rounded-sm"
                                                        style="color:transparent"
                                                        srcset="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-5%403x.cc9bd73d.png&amp;w=48&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 1x, /_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-5%403x.cc9bd73d.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina 2x"
                                                        src="/_next/image?url=%2F_next%2Fstatic%2Fmedia%2Fperson-5%403x.cc9bd73d.png&amp;w=96&amp;q=75&amp;dpl=dpl_4bUfyZeMnDxtmz6h7DX1mCacnina">
                                                </div>
                                                <div class="flex-auto text-2xs">
                                                    <div class="font-book text-gray-950">Personal account</div>
                                                </div>
                                            </div>
                                            <div
                                                class="flex items-center gap-3 px-4 py-3 [&amp;:not(:first-child)]:border-t [&amp;:not(:first-child)]:border-gray-400/10">
                                                <x-filament::icon icon="heroicon-o-plus" class="size-10 flex-none" />
                                                <div class="flex-auto text-2xs">
                                                    <div class="font-book text-gray-950">Create organization</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-center justify-center gap-2 p-3 text-2xs font-medium text-gray-400">
                                            Secured by <x-filament::icon icon="heroicon-o-shield-check"
                                                class="h-3" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Categories -->
        <section class="mt-16 pt-16">
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Top Categories</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @php
                        $categories = [
                            ['name' => 'Social', 'color' => 'bg-blue-500'],
                            ['name' => 'Entertainment', 'color' => 'bg-red-500'],
                            ['name' => 'Photography', 'color' => 'bg-green-500'],
                            ['name' => 'Gaming', 'color' => 'bg-purple-500'],
                            ['name' => 'Productivity', 'color' => 'bg-yellow-500'],
                            ['name' => 'Education', 'color' => 'bg-indigo-500'],
                        ];
                    @endphp

                    @foreach ($categories as $category)
                        <div class="text-center">
                            <div
                                class="w-16 h-16 {{ $category['color'] }} rounded-full mx-auto mb-2 flex items-center justify-center">
                                <x-filament::icon icon="heroicon-o-star" class="w-8 h-8" />
                            </div>
                            <span class="text-sm font-medium">{{ $category['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </section>
        <!-- Recommended Apps -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Recommended for you</h3>
                <button class="text-green-600 font-medium text-sm hover:underline">See more</button>
            </div>
            <div class="flex space-x-4 overflow-x-auto scrollbar-hide pb-2">
                @php
                    $apps = [
                        [
                            'name' => 'WhatsApp',
                            'category' => 'Social',
                            'rating' => '4.8',
                            'downloads' => '5B+',
                            'color' => 'bg-green-500',
                        ],
                        [
                            'name' => 'Instagram',
                            'category' => 'Social',
                            'rating' => '4.6',
                            'downloads' => '2B+',
                            'color' => 'bg-pink-500',
                        ],
                        [
                            'name' => 'Netflix',
                            'category' => 'Entertainment',
                            'rating' => '4.4',
                            'downloads' => '1B+',
                            'color' => 'bg-red-600',
                        ],
                        [
                            'name' => 'Spotify',
                            'category' => 'Music',
                            'rating' => '4.7',
                            'downloads' => '1B+',
                            'color' => 'bg-green-400',
                        ],
                        [
                            'name' => 'YouTube',
                            'category' => 'Video',
                            'rating' => '4.3',
                            'downloads' => '10B+',
                            'color' => 'bg-red-500',
                        ],
                        [
                            'name' => 'TikTok',
                            'category' => 'Entertainment',
                            'rating' => '4.5',
                            'downloads' => '3B+',
                            'color' => 'bg-gray-800',
                        ],
                    ];
                @endphp

                @foreach ($apps as $app)
                    <div class="flex-none w-72 bg-white rounded-lg p-4 shadow-sm border">
                        <div class="flex items-start space-x-3">
                            <div class="w-12 h-12 {{ $app['color'] }} rounded-lg flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">{{ $app['name'] }}</h4>
                                <p class="text-sm text-gray-500">{{ $app['category'] }}</p>
                                <div class="flex items-center mt-2 space-x-2">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium">{{ $app['rating'] }}</span>
                                        <x-filament::icon icon="heroicon-o-star" class="w-4 h-4 ml-1" />
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $app['downloads'] }}</span>
                                </div>
                            </div>
                            <button
                                class="bg-green-600 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-green-700 transition-colors">
                                Install
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Charts -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Top Charts</h3>
                <button class="text-green-600 font-medium text-sm hover:underline">See all</button>
            </div>
            <div class="bg-white rounded-lg shadow-sm border">
                @php
                    $topApps = [
                        [
                            'name' => 'Chrome',
                            'category' => 'Tools',
                            'rating' => '4.2',
                            'rank' => '1',
                            'color' => 'bg-blue-500',
                        ],
                        [
                            'name' => 'Gmail',
                            'category' => 'Communication',
                            'rating' => '4.3',
                            'rank' => '2',
                            'color' => 'bg-red-500',
                        ],
                        [
                            'name' => 'Google Maps',
                            'category' => 'Travel',
                            'rating' => '4.4',
                            'rank' => '3',
                            'color' => 'bg-green-500',
                        ],
                        [
                            'name' => 'Facebook',
                            'category' => 'Social',
                            'rating' => '4.1',
                            'rank' => '4',
                            'color' => 'bg-blue-600',
                        ],
                        [
                            'name' => 'Amazon',
                            'category' => 'Shopping',
                            'rating' => '4.5',
                            'rank' => '5',
                            'color' => 'bg-yellow-500',
                        ],
                    ];
                @endphp

                @foreach ($topApps as $index => $app)
                    <div
                        class="flex items-center p-4 {{ $index < count($topApps) - 1 ? 'border-b border-gray-100' : '' }}">
                        <div class="text-lg font-bold text-gray-400 w-6 mr-4">{{ $app['rank'] }}</div>
                        <div class="w-10 h-10 {{ $app['color'] }} rounded-lg mr-3"></div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $app['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $app['category'] }}</p>
                        </div>
                        <div class="flex items-center mr-4">
                            <span class="text-sm font-medium mr-1">{{ $app['rating'] }}</span>
                            <x-filament::icon icon="heroicon-o-star" class="w-4 h-4" />
                        </div>
                        <button
                            class="bg-gray-100 text-gray-700 px-4 py-1 rounded-full text-sm font-medium hover:bg-gray-200 transition-colors">
                            Install
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- New & Updated -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">New & Updated</h3>
                <button class="text-green-600 font-medium text-sm hover:underline">See more</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $newApps = [
                        [
                            'name' => 'Signal',
                            'category' => 'Communication',
                            'rating' => '4.6',
                            'size' => '45MB',
                            'color' => 'bg-blue-600',
                        ],
                        [
                            'name' => 'Zoom',
                            'category' => 'Business',
                            'rating' => '4.4',
                            'size' => '78MB',
                            'color' => 'bg-blue-500',
                        ],
                        [
                            'name' => 'Discord',
                            'category' => 'Communication',
                            'rating' => '4.3',
                            'size' => '65MB',
                            'color' => 'bg-indigo-600',
                        ],
                        [
                            'name' => 'Adobe Photoshop',
                            'category' => 'Photography',
                            'rating' => '4.1',
                            'size' => '120MB',
                            'color' => 'bg-blue-700',
                        ],
                        [
                            'name' => 'Microsoft Teams',
                            'category' => 'Business',
                            'rating' => '4.2',
                            'size' => '95MB',
                            'color' => 'bg-purple-600',
                        ],
                        [
                            'name' => 'Canva',
                            'category' => 'Art & Design',
                            'rating' => '4.7',
                            'size' => '55MB',
                            'color' => 'bg-cyan-500',
                        ],
                    ];
                @endphp

                @foreach ($newApps as $app)
                    <div class="bg-white rounded-lg p-4 shadow-sm border">
                        <div class="flex items-start space-x-3">
                            <div class="w-12 h-12 {{ $app['color'] }} rounded-lg flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">{{ $app['name'] }}</h4>
                                <p class="text-sm text-gray-500">{{ $app['category'] }}</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium">{{ $app['rating'] }}</span>
                                        <x-filament::icon icon="heroicon-o-star" class="w-4 h-4 ml-1" />
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $app['size'] }}</span>
                                </div>
                                <button
                                    class="mt-3 w-full bg-green-600 text-white py-2 rounded-full text-sm font-medium hover:bg-green-700 transition-colors">
                                    Install
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Google Play</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Play Pass</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Play Points</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Gift cards</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Kids & Family</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Parent Guide</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Family Library</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Developers</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Android Studio</a>
                        </li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Google Play
                                Console</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Help Center</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-gray-900">Contact us</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-8 pt-8">
                <p class="text-sm text-gray-500">&copy; 2024 Google LLC. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
