@includeIf('base::filament.pages.documentation-hero-bg')

<style>
    .main-container {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .main-container {
            grid-template-columns: 1fr;
        }

        .aside-content {
            position: static;
        }
    }

    .aside-content {
        height: fit-content;
        position: sticky;
        top: 5rem;
    }

    .documentation-page {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .fi-main.fi-width-7xl,
    .fi-main.fi-width-7xl .fi-main-content {
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .team-photo {
        position: absolute;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: saturate(180%);
        object-fit: cover;
        transition: all 0.2s ease;
        cursor: pointer;
        opacity: 1 !important;
    }

    .team-photo:hover {
        transform: scale(1.15);
        border-color: rgba(255, 255, 255, 0.6);
        z-index: 40;
    }

    .card-hover {
        transition: all 0.2s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
    }
    .hero-bg {
        background-size: cover;
        position: relative;
    }

    .hero-bg:dir(ltr) {
        border-radius: 0 0 0 12px;
    }

    .hero-bg:dir(rtl) {
        border-radius: 0 0 12px 0;
    }


    .hero-bg::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .search-pulse {
        animation: pulse-search 2s infinite;
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    }

    @keyframes pulse-search {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    .bg-blur {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .gradient-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .content-card {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    .popular-section {
        background: linear-gradient(135deg, #fef7ff, #f3e8ff);
    }

    .contact-section {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    }

    .highlight-h2 {
        position: relative;
        padding-left: 12px;
    }

    .highlight-h2 {
        position: relative;
        padding-left: 12px;
        padding-right: 12px;
    }

    .highlight-h2:dir(ltr)::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #8b5cf6;
    }

    .highlight-h2:dir(rtl)::before {
        content: "";
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #8b5cf6;
    }
</style>

@php
    $commonTopics = [
        ['url' => 'https://help.elements.numerimondes.com/hc/en-us/articles/360000621523', 'text' => 'invoice'],
        ['url' => 'https://help.elements.numerimondes.com/hc/en-us/articles/360000629326', 'text' => 'subscribe'],
        ['url' => 'https://help.elements.numerimondes.com/hc/en-us/articles/4411385939225', 'text' => 'payments'],
        ['url' => 'https://help.elements.numerimondes.com/hc/en-us/articles/360000629166', 'text' => 'item support'],
        ['url' => 'https://help.elements.numerimondes.com/hc/en-us/articles/360000629346', 'text' => 'license faq'],
    ];

    $recentArticles = [
        [
            'url' => '/hc/en-us/articles/360000629326-Subscribe-to-Numerimondes-Elements',
            'text' => 'Subscribe to Numerimondes Elements',
        ],
        ['url' => '/hc/en-us/articles/360000629166-Item-Support', 'text' => 'Item Support'],
        ['url' => '/hc/en-us/articles/360000621663-About-Numerimondes-Subscription', 'text' => 'About Numerimondes'],
    ];

    $categories = [
        [
            'icon' => 'heroicon-o-squares-2x2',
            'title' => 'Numerimondes subscription',
            'url' => '/hc/en-us/sections/360000117063-What-is-Numerimondes-subscription',
            'articles' => [
                [
                    'url' => '/hc/en-us/articles/360000621663-About-Numerimondes-Subscription',
                    'text' => 'About Numerimondes Subscription',
                ],
                ['url' => '/hc/en-us/articles/360035501651-Charities-Plan', 'text' => 'Charities Plan'],
                ['url' => '/hc/en-us/articles/360031520972-Student-Plan', 'text' => 'Student Plan'],
            ],
            'seeAllUrl' => '/hc/en-us/sections/360000117063-What-is-Numerimondes-subscription',
            'seeAllText' => 'See all 7 articles',
        ],
        [
            'icon' => 'heroicon-o-cog-6-tooth',
            'title' => 'Using Numerimondes subscription',
            'url' => '/hc/en-us/sections/360000117083-Using-Numerimondes-subscription',
            'articles' => [
                ['url' => '/hc/en-us/articles/48162991952025-Workspaces', 'text' => 'Workspaces'],
                ['url' => '/hc/en-us/articles/41122532047257-About-ImageEdit', 'text' => 'About ImageEdit'],
                ['url' => '/hc/en-us/articles/40718802421657-My-Projects', 'text' => 'My Projects'],
            ],
            'seeAllUrl' => '/hc/en-us/sections/360000117083-Using-Numerimondes-subscription',
            'seeAllText' => 'See all 10 articles',
        ],
    ];

    $popularArticles = [
        [
            'url' => '/hc/en-us/articles/360000621663-About-Numerimondes-Subscription',
            'title' => 'About Numerimondes',
            'description' =>
                'Numerimondes, your one-stop creative asset destination, is a game-changer for creatives worldwide. With...',
        ],
        [
            'url' => '/hc/en-us/articles/360035501651-Charities-Plan',
            'title' => 'Charities Plan',
            'description' =>
                'We\'re here to lend a hand to those making a positive impact in the world. That\'s why Numerimondes Eleme...',
        ],
        [
            'url' => '/hc/en-us/articles/360031520972-Student-Plan',
            'title' => 'Student Plan',
            'description' =>
                'Students, teachers, and faculty members at educational institutions can take advantage of our Env...',
        ],
    ];

    $recentActivities = [
        [
            'sectionUrl' => '/hc/en-us/sections/360000117083-Using-Numerimondes-subscription',
            'sectionTitle' => 'Using Numerimondes subscription',
            'articleUrl' => '/hc/en-us/articles/48162991952025-Workspaces',
            'articleTitle' => 'Workspaces',
            'time' => '1 month ago',
            'commentCount' => 0,
        ],
        [
            'sectionUrl' => '/hc/en-us/sections/360000117183-Frequently-Asked-Questions',
            'sectionTitle' => 'Frequently Asked Questions',
            'articleUrl' => '/hc/en-us/articles/44159275048729-How-do-I-change-the-language-settings',
            'articleTitle' => 'How do I change the language settings?',
            'time' => '5 months ago',
            'commentCount' => 0,
        ],
    ];

    $footerCards = [
        [
            'title' => 'Ask in the forums',
            'description' =>
                'Join the conversation! We think you would love our community and it\'s a great place to find Numerimondes announcements or general help.',
            'url' => 'https://forums.numerimondes.com/',
            'linkText' => 'Join in',
        ],
        [
            'title' => 'Visit our blog',
            'description' =>
                'We love to share ideas! Visit our blog if you\'re looking for great articles or inspiration to get you going.',
            'url' => 'https://numerimondes.com/blog/',
            'linkText' => 'Visit',
        ],
    ];

    $sites = [
        [
            'url' => 'https://chatgpt.com/',

            'title' => 'chatgpt',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://chatgpt.com/&size=256',
        ],

        [
            'url' => 'https://numerimondes.com/',
            'title' => 'numerimondes',
            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://numerimondes.com/&size=256',
            'image_double' => 'https://exemple.com/large-banner.jpg',
            'is_featured' => true,
            'position' => 3,
            'type' => 'sponsored',
        ],

        [
            'url' => 'https://claude.ai/',

            'title' => 'claude',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://claude.com/&size=256',
        ],

        [
            'url' =>
                'https://www.cpasfini.me/seriestreaming/comedie-s/1672-bienvenue-chez-les-huang/2-saison/21-episode.html',

            'title' => 'cpasfini',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://cpsfini.com/&size=256',
        ],

        [
            'url' => 'https://chat.mistral.ai/chat',

            'title' => 'chat.mistral',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://mistral.com/&size=256',
        ],

        [
            'url' => 'https://web.whatsapp.com/',

            'title' => 'web.whatsapp',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://whatsapp.com/&size=256',
        ],

        [
            'url' => 'https://x.com/i/grok',

            'title' => 'x',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://x.com/&size=256',
        ],

        [
            'url' => 'https://www.lexilogos.com/clavier/araby.htm',

            'title' => 'lexilogos',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://lexilogos.com/&size=256',
        ],

        [
            'url' => 'https://www.instagram.com/direct/',

            'title' => 'instagram',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://instagram.com/&size=256',
        ],

        [
            'url' => 'https://gemini.google.com/',

            'title' => 'gemini.google',

            'icon' =>
                'https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://gemini.google.com/&size=256',
        ],
    ];

    $activities = [
        [
            'url' =>
                'https://webmail.numerimondes.com/?_task=mail&_mbox=INBOX&_uid=1181&_part=2&_download=1&_action=get&_token=34qKV5twDlTwVBNG9Lg5TKewQ0FbmmlP',

            'host' => 'webmail.numerimondes',

            'title' => 'Présentation2.pptx',

            'size' => '931 Ko',

            'context' => 'Téléchargé',
        ],

        [
            'url' => 'https://codepen.io/havardob/pen/ExvwGBr',

            'host' => 'codepen',

            'title' => 'Dark UI - Bank dashboard concept',

            'description' =>
                'A recreation of this Dribbble shot: https://dribbble.com/shots/16755073-Almeria-Neobank-Dashboard/attachments/11802960?mode=media ...',

            'context' => 'Visité',
        ],

        [
            'url' => 'https://docs.cursor.com/en/guides/tutorials/web-development',

            'host' => 'docs.cursor',

            'title' => 'Cursor – Web Development',

            'description' => 'How to set up Cursor for web development',

            'context' => 'Visité',
        ],

        [
            'url' => 'https://cursor.com/loginDeepPage',

            'host' => 'cursor',

            'title' => 'Auth | Cursor - The AI Code Editor',

            'description' => 'Built to make you extraordinarily productive, Cursor is the best way to code with AI.',

            'context' => 'Visité',
        ],
    ];
@endphp

<div class="documentation-page"
    style="width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important;">
    <div style="margin-bottom: 12rem;">
        {{-- Hero Section --}}
        <section class="hero-bg" style="color: white; text-align: center; padding: 4rem 2rem; position: relative; overflow: hidden;">
            <div class="hero-content" style="max-width: 1200px; margin: 0 auto; position: relative;">
                <div dir="ltr">
                    <h1
                        style="font-size: 2.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: center; gap: 1rem; flex-wrap: wrap; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                        <span>Hi! How can we</span>
                        <div
                            style="position: relative; display: inline-flex; align-items: center; justify-content: center; width: 144px; height: 80px;">
                            <img src="https://t2.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=https://numerimondes.com/&size=256"
                                alt="Team member 1" class="team-photo"
                                style="transform: translateX(-48px); z-index: 21;">
                            <img src="https://img.freepik.com/premium-photo/people-female-business-portrait-concept-happy-smiling-young-woman-face_380164-119772.jpg"
                                alt="Team member 2" class="team-photo" style="z-index: 20;">
                            <img src="https://img.freepik.com/premium-photo/portrait-black-man_67651-2373.jpg"
                                alt="Team member 3" class="team-photo"
                                style="transform: translateX(48px); z-index: 10;">
                        </div>
                        <span>Help ?</span>
                    </h1>
                </div>
                <div style="max-width: 500px; margin: 0 auto 2rem;">
                    <div class="gradient-card bg-blur card-hover search-pulse"
                        style="border-radius: 12px; padding: 12px; position: relative;">
                        <form role="search" method="get" action="/hc/en-us/search"
                            style="display: flex; align-items: center;">
                            <svg style="position: absolute; left: 16px; width: 16px; height: 16px; color: rgba(255,255,255,0.8);"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z" />
                            </svg>
                            <input type="search" name="query" placeholder="Ask us a question"
                                style="width: 100%; background: transparent; border: none; outline: none; color: white; padding: 12px 16px 12px 48px; font-size: 16px;"
                                autocomplete="off">
                        </form>
                    </div>
                </div>
                <div
                    style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; justify-content: center; margin-bottom: 1rem;">
                    <p style="font-weight: 600; color: white; margin: 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                        Common topics:</p>
                    @foreach ($commonTopics as $topic)
                        <a href="{{ $topic['url'] }}" class="card-hover"
                            style="padding: 8px 20px; color:white !important; border: 1px solid rgba(255,255,255,0.3); border-radius: 25px; color: #374151; font-weight: 500; text-decoration: none; backdrop-filter: blur(8px); box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            {{ $topic['text'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        <section style="padding-right: calc(var(--spacing) * 4) !important;">
            <div style="padding: 2rem 0; margin-top: 2rem;">
                <div style="flex: 1;">
                    <div class="main-container">
                        <main class="main-content">
                            <div style="max-width: 1200px; margin: 0 auto;">
                                {{-- Recently viewed articles --}}
                                <section style="margin-bottom: 3rem;">
                                    <h2 class="highlight-h2"
                                        style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem; position: relative; padding-left: 12px;">
                                        Recently viewed articles
                                    </h2>
                                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                                        @foreach ($recentArticles as $article)
                                            <a href="{{ $article['url'] }}" style="text-decoration: none;">
                                                <x-filament::section
                                                    class="!p-1 !px-3 !text-sm !font-medium !rounded-full !shadow-none !border !border-gray-300 hover:!bg-primary-500 hover:!text-white transition-colors duration-200 card-hover"
                                                >
                                                    {{ $article['text'] }}
                                                </x-filament::section>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                                <section style="margin-bottom: 3rem;">
                                    <div class="body-wrapper" style="padding: 1rem;">
                                        <div class="discovery-stream ds-layout" style="width: 100%;">
                                            <div class="ds-column ds-column-12" style="width: 100%;">
                                                <div class="ds-column-grid"
                                                    style="display: grid; grid-template-columns: repeat(12, 1fr); gap: 1rem;">
                                                    <div style="grid-column: span 12;">
                                                        <div class="ds-top-sites">
                                                            <section class="collapsible-section top-sites"
                                                                data-section-id="topsites" style="margin-bottom: 1rem;">
                                                                <div class="section-top-bar">
                                                                    <h3 class="section-title-container"
                                                                        style="visibility: hidden; margin: 0;">
                                                                        <span class="section-title">
                                                                            <span>Sites les plus visités</span>
                                                                        </span>
                                                                        <span class="learn-more-link-wrapper"></span>
                                                                    </h3>
                                                                </div>
                                                                <div>
                                                                    @php $minSpacing = 0.25; @endphp
                                                                    <ul class="top-sites-list"
                                                                        style="display: grid; grid-template-columns: repeat(6, 1fr); gap: {{ $minSpacing }}rem; row-gap: 1.5rem; list-style: none; padding: 0; margin: 0;">
                                                                        @php
                                                                            $sortedSites = collect($sites)->sortBy(
                                                                                function ($site) {
                                                                                    $isFeatured =
                                                                                        isset($site['is_featured']) &&
                                                                                        $site['is_featured'];
                                                                                    $position = isset($site['position'])
                                                                                        ? $site['position']
                                                                                        : 999;

                                                                                    if ($isFeatured) {
                                                                                        return $position; // Featured en premier, trié par position
                                                                                    } else {
                                                                                        return 1000 + $position; // Non-featured après, trié par position
                                                                                    }
                                                                                },
                                                                            );
                                                                        @endphp
                                                                        @foreach ($sortedSites as $site)
                                                                            @php
                                                                                $isFeatured =
                                                                                    isset($site['is_featured']) &&
                                                                                    $site['is_featured'];
                                                                                $sectionClass = $isFeatured
                                                                                    ? 'featured-site'
                                                                                    : '';
                                                                                $imageUrl = $site['icon'];
                                                                                $sectionStyle = $isFeatured
                                                                                    ? 'border: 2px solid #ffd700; box-shadow: 0 0 20px rgba(255, 215, 0, 0.6), inset 0 0 20px rgba(255, 215, 0, 0.2); position: relative;'
                                                                                    : '';
                                                                            @endphp
                                                                            <li
                                                                                style="display: flex; flex-direction: column; align-items: center;">
                                                                                <x-filament::section
                                                                                    class="{{ $sectionClass }}"
                                                                                    style="{{ $sectionStyle }}">
                                                                                    <a href="{{ $site['url'] }}"
                                                                                        tabindex="0" draggable="true"
                                                                                        data-is-sponsored-link="false"
                                                                                        style="display: flex; align-items: center; justify-content: center; width: 100%; aspect-ratio: 1 / 1; border-radius: 12px; background-color: #f1f5f9; overflow: hidden; position: relative;">
                                                                                        <div class="top-site-icon rich-icon"
                                                                                            style="width: 48px; height: 48px; background-image: url('{{ $imageUrl }}'); background-size: cover; background-position: center;">
                                                                                        </div>
                                                                                        @if ($isFeatured)
                                                                                            <x-filament::icon
                                                                                                name="heroicon-m-sparkles"
                                                                                                class="h-4 w-4"
                                                                                                style="position: absolute; top: 8px; right: 8px; color: #ffd700; z-index: 10;" />
                                                                                        @endif
                                                                                    </a>
                                                                                </x-filament::section>
                                                                                <span
                                                                                    style="margin-top: 0.5rem; font-size: 0.875rem; text-align: center;">{{ $site['title'] ?? 'Link Name' }}</span>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </section>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                {{-- Categories --}}
                                <section style="margin-bottom: 3rem;">
                                    <h2 class="highlight-h2"
                                        style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem;">
                                        Categories
                                    </h2>
                                    <div
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                                        @foreach ($categories as $category)
                                            <x-filament::section class="card-hover">
                                                <div
                                                    style="display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                                                    <svg style="width: 24px; height: 24px; opacity: 0.8;" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                    </svg>
                                                    <a href="{{ $category['url'] }}"
                                                        style="font-size: 1.125rem; font-weight: 600; text-decoration: none;">
                                                        {{ $category['title'] }}
                                                    </a>
                                                </div>
                                                <hr style="margin: 12px 0; border: 1px solid #e5e7eb;">
                                                <ul class="fi-dropdown-list max-h-60 overflow-y-auto fi-dropdown-list"
                                                    style="margin: 0 0 1rem 0; padding: 0; list-style: none;">
                                                    @foreach ($category['articles'] as $article)
                                                        <a href="{{ $article['url'] }}"
                                                            style="font-size: 14px; text-decoration: none;">
                                                            <li
                                                                class="fi-dropdown-list-item fi-ac-grouped-action cursor-pointer whitespace-nowrap flex items-center gap-2 bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600">
                                                                {{ $article['text'] }}
                                                            </li>
                                                        </a>
                                                    @endforeach
                                                </ul>
                                                <a href="{{ $category['seeAllUrl'] }}"
                                                    style="color: #8b5cf6; font-size: 14px; font-weight: 500; text-decoration: none;">
                                                    {{ $category['seeAllText'] }}
                                                </a>
                                            </x-filament::section>
                                        @endforeach
                                    </div>
                                </section>
                                {{-- Popular Articles --}}
                                <section class="custom-gradient" style="margin-bottom: 3rem; border-radius: 16px; padding: 2rem; position: relative; overflow: hidden;">
                                    <style>
                                        @keyframes breathe {
                                            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.8; }
                                            50% { transform: scale(1.02) rotate(0.5deg); opacity: 0.9; }
                                        }
                                        .custom-gradient::before {
                                            content: ''; position: absolute;
                                            top: 0; left: 0; right: 0; bottom: 0;
                                            background: radial-gradient(circle at 30% 20%, hsl(from var(--primary-500) h s l / 0.6) 0%, transparent 70%),
                                            radial-gradient(circle at 80% 60%, hsl(from var(--primary-400) h s l / 0.4) 0%, transparent 60%),
                                            radial-gradient(circle at 20% 80%, hsl(from var(--primary-600) h s l / 0.3) 0%, transparent 50%),
                                            linear-gradient(135deg, hsl(from var(--primary-600) h s l / 0.1) 0%, hsl(from var(--primary-500) h s l / 0.2) 50%, hsl(from var(--primary-400) h s l / 0.1) 100% );
                                            animation: breathe 8s ease-in-out infinite;
                                            backdrop-filter: blur(0.5px);
                                            border-radius: inherit;
                                            z-index: -1;
                                        }
                                    </style>
                                    <h2
                                        style="font-size: 1.5rem; font-weight: bold; margin-bottom: 2rem; text-align: center;">
                                        Popular Articles
                                    </h2>
                                    <div
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; align-items: stretch;">
                                        @foreach ($popularArticles as $article)
                                            <a href="{{ $article['url'] }}" class="card-hover"
                                                style="text-decoration: none; display: flex;">
                                                <x-filament::section
                                                    style="
                                                    ">
                                                    <div
                                                        style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                                        <h4>
                                                            <strong>
                                                                {{ Str::limit($article['title'], 50) }}
                                                            </strong>
                                                        </h4>
                                                    </div>
                                                    <p
                                                        style="color: secondary; font-size: 14px; margin: 0 0 1rem 0; flex-grow: 1; line-height: 1.5;">
                                                        {{ Str::limit($article['description'], 120) }}
                                                    </p>
                                                    <div style="margin-top: auto;">
                                                        <span
                                                            style="display: inline-flex; align-items: center; gap: 8px; background: #f3f4f6; color: #374151; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500;">
                                                            <svg style="width: 16px; height: 16px;" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                            </svg>
                                                            Read it
                                                        </span>
                                                    </div>
                                                </x-filament::section>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                                {{-- Recent activity --}}
                                <section style="margin-bottom: 3rem;">
                                    <h2 class="highlight-h2"
                                        style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem; position: relative; padding-left: 12px;">
                                        Recent activity
                                    </h2>
                                    <div
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                                        @foreach ($recentActivities as $activity)
                                            <x-filament::section class="card-hover">
                                                <h3 style="margin: 0 0 8px 0;">
                                                    <a href="{{ $activity['sectionUrl'] }}"
                                                        style=" font-weight: 600; text-decoration: none;">
                                                        {{ $activity['sectionTitle'] }}
                                                    </a>
                                                </h3>
                                                <a href="{{ $activity['articleUrl'] }}"
                                                    style="display: block; margin-bottom: 12px; text-decoration: none;">
                                                    {{ $activity['articleTitle'] }}
                                                </a>
                                                <div
                                                    style="display: flex; align-items: center; justify-content: space-between; font-size: 12px;">
                                                    <div>Article created {{ $activity['time'] }}</div>
                                                    <div style="display: flex; align-items: center; gap: 4px;">
                                                        <svg style="width: 12px; height: 12px;" fill="none"
                                                            stroke="currentColor" viewBox="0 0 12 12">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M1 .5h10c.3 0 .5.2.5.5v7c0 .3-.2.5-.5.5H6l-2.6 2.6c-.3.3-.9.1-.9-.4V8.5H1C.7 8.5.5 8.3.5 8V1C.5.7.7.5 1 .5z">
                                                            </path>
                                                        </svg>
                                                        <span>{{ $activity['commentCount'] }}</span>
                                                    </div>
                                                </div>
                                            </x-filament::section>
                                        @endforeach
                                    </div>
                                    <a href="#"
                                        style="color: #8b5cf6; font-size: 14px; font-weight: 500; text-decoration: none; margin-top: 1rem; display: inline-block;">See
                                        more</a>
                                </section>
                                {{-- Contact Section --}}
                                <x-filament::section class=""
                                    style="border-radius: 16px; padding: 2rem; text-align: center;">
                                    <div
                                        style="width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6, #a855f7); border-radius: 50%; padding: 12px; margin: 0 auto 1rem;">
                                        <svg style="width: 24px; height: 24px; color: white;" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                    <h4 style="font-size: 1.25rem; font-weight: 600; margin: 0 0 8px 0;">
                                        Haven't found
                                        what you need?</h4>
                                    <p style="opacity: 0.8; margin: 0 0 1.5rem 0;">Get in touch with our
                                        friendly
                                        support team.</p>
                                    <x-filament::button href="#" tag="a">
                                        Contact us
                                    </x-filament::button>
                                </x-filament::section>
                                {{-- Footer Cards --}}
                                <div
                                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 3rem;">
                                    @foreach ($footerCards as $card)
                                        <x-filament::section class="card-hover" style="border-radius: 12px;">
                                            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 12px 0;">
                                                {{ $card['title'] }}</h3>
                                            <p style="color: #6b7280; font-size: 14px; margin: 0 0 1rem 0;">
                                                {{ $card['description'] }}
                                            </p>
                                            <a href="{{ $card['url'] }}"
                                                style="color: #8b5cf6; font-size: 14px; font-weight: 500; text-decoration: none;">{{ $card['linkText'] }}</a>
                                        </x-filament::section>
                                    @endforeach
                                </div>
                        </main>
                        {{-- Aside Sidebar --}}
                        <aside class="aside-content">
                            @includeIf('webkernel-users::widgets.app-store-card')
                            <x-filament::section style="visibility: hidden;">
                                <div>
                                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                                    {{-- Navigation rapide --}}
                                    <div class="mb-6">
                                        <h4 class="font-medium mb-2 text-gray-700">Popular Sections</h4>
                                        <ul class="space-y-2">
                                            @foreach ($categories as $category)
                                                <li>
                                                    <a href="{{ $category['url'] }}"
                                                        class="text-sm text-gray-600 hover:text-blue-600">
                                                        {{ $category['title'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    {{-- Articles récents --}}
                                    <div class="mb-6">
                                        <h4 class="font-medium mb-2 text-gray-700">Recent Articles</h4>
                                        <ul class="space-y-2">
                                            @foreach ($recentArticles as $article)
                                                <li>
                                                    <a href="{{ $article['url'] }}"
                                                        class="text-sm text-gray-600 hover:text-blue-600">
                                                        {{ Str::limit($article['text'], 40) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </x-filament::section>
                        </aside>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const images = document.querySelectorAll('img');
        const urls = Array.from(images)
            .map(img => img.getAttribute('src'))
            .filter(src => src);
        urls.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            link.as = 'image';
            document.head.appendChild(link);
        });
    });
</script>
