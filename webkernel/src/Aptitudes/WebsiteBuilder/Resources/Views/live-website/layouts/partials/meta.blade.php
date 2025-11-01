{{-- Basic Meta Tags --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- SEO Meta Tags --}}
<title>{{ $pageTitle ?? ($title ?? 'Numerimondes') }}</title>
<meta name="description" content="{{ $pageDescription ?? ($description ?? 'Professional website builder with dynamic themes and modern design capabilities') }}">
<meta name="keywords" content="{{ $keywords ?? 'website builder, themes, design, web development, responsive' }}">
<meta name="author" content="{{ $author ?? config('app.name') }}">
<meta name="robots" content="{{ $robots ?? 'index, follow' }}">

{{-- Canonical URL --}}
@if(isset($canonical))
    <link rel="canonical" href="{{ $canonical }}">
@else
    <link rel="canonical" href="{{ url()->current() }}">
@endif

{{-- Open Graph Meta Tags --}}
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:title" content="{{ $ogTitle ?? ($pageTitle ?? ($title ?? config('app.name'))) }}">
<meta property="og:description" content="{{ $ogDescription ?? ($pageDescription ?? ($description ?? 'Professional website builder with dynamic themes')) }}">
<meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
<meta property="og:site_name" content="{{ $ogSiteName ?? config('app.name') }}">
<meta property="og:locale" content="{{ $ogLocale ?? str_replace('-', '_', app()->getLocale()) }}">

{{-- Open Graph Images --}}
@if(isset($ogImage))
    <meta property="og:image" content="{{ $ogImage }}">
    @if(isset($ogImageWidth))
        <meta property="og:image:width" content="{{ $ogImageWidth }}">
    @endif
    @if(isset($ogImageHeight))
        <meta property="og:image:height" content="{{ $ogImageHeight }}">
    @endif
    @if(isset($ogImageAlt))
        <meta property="og:image:alt" content="{{ $ogImageAlt }}">
    @endif
@else
    <meta property="og:image" content="{{ asset('images/default-og-image.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ config('app.name') }} - Website Builder">
@endif

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="{{ $twitterCard ?? 'summary_large_image' }}">
<meta name="twitter:title" content="{{ $twitterTitle ?? ($ogTitle ?? ($pageTitle ?? $title ?? config('app.name'))) }}">
<meta name="twitter:description" content="{{ $twitterDescription ?? ($ogDescription ?? ($pageDescription ?? ($description ?? 'Professional website builder with dynamic themes'))) }}">
<meta name="twitter:image" content="{{ $twitterImage ?? ($ogImage ?? asset('images/default-og-image.jpg')) }}">
@if(isset($twitterSite))
    <meta name="twitter:site" content="{{ $twitterSite }}">
@endif
@if(isset($twitterCreator))
    <meta name="twitter:creator" content="{{ $twitterCreator }}">
@endif

{{-- Article Meta Tags (for blog posts) --}}
@if(isset($articleAuthor))
    <meta property="article:author" content="{{ $articleAuthor }}">
@endif
@if(isset($articlePublishedTime))
    <meta property="article:published_time" content="{{ $articlePublishedTime }}">
@endif
@if(isset($articleModifiedTime))
    <meta property="article:modified_time" content="{{ $articleModifiedTime }}">
@endif
@if(isset($articleSection))
    <meta property="article:section" content="{{ $articleSection }}">
@endif
@if(isset($articleTags))
    @foreach($articleTags as $tag)
        <meta property="article:tag" content="{{ $tag }}">
    @endforeach
@endif

{{-- Favicon and App Icons --}}
@if(isset($favicon))
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
@else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
@endif

{{-- Apple Touch Icons --}}
<link rel="apple-touch-icon" sizes="57x57" href="{{ $appleTouchIcon57 ?? asset('images/icons/apple-touch-icon-57x57.png') }}">
<link rel="apple-touch-icon" sizes="114x114" href="{{ $appleTouchIcon114 ?? asset('images/icons/apple-touch-icon-114x114.png') }}">
<link rel="apple-touch-icon" sizes="72x72" href="{{ $appleTouchIcon72 ?? asset('images/icons/apple-touch-icon-72x72.png') }}">
<link rel="apple-touch-icon" sizes="144x144" href="{{ $appleTouchIcon144 ?? asset('images/icons/apple-touch-icon-144x144.png') }}">
<link rel="apple-touch-icon" sizes="60x60" href="{{ $appleTouchIcon60 ?? asset('images/icons/apple-touch-icon-60x60.png') }}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ $appleTouchIcon120 ?? asset('images/icons/apple-touch-icon-120x120.png') }}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ $appleTouchIcon76 ?? asset('images/icons/apple-touch-icon-76x76.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ $appleTouchIcon152 ?? asset('images/icons/apple-touch-icon-152x152.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $appleTouchIcon180 ?? asset('images/icons/apple-touch-icon-180x180.png') }}">

{{-- Android Icons --}}
<link rel="icon" type="image/png" sizes="192x192" href="{{ $androidIcon192 ?? asset('images/icons/android-icon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconIcon32 ?? asset('images/icons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ $faviconIcon96 ?? asset('images/icons/favicon-96x96.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconIcon16 ?? asset('images/icons/favicon-16x16.png') }}">

{{-- PWA Manifest --}}
@if(isset($manifest) || file_exists(public_path('manifest.json')))
    <link rel="manifest" href="{{ $manifest ?? asset('manifest.json') }}">
@endif

{{-- Theme Color --}}
<meta name="theme-color" content="{{ $themeColor ?? '#3b82f6' }}">
<meta name="msapplication-TileColor" content="{{ $tileColor ?? '#3b82f6' }}">
<meta name="msapplication-TileImage" content="{{ $tileImage ?? asset('images/icons/ms-icon-144x144.png') }}">

{{-- Safari Pinned Tab --}}
@if(isset($safariPinnedTab))
    <link rel="mask-icon" href="{{ $safariPinnedTab }}" color="{{ $safariPinnedTabColor ?? '#3b82f6' }}">
@endif

{{-- DNS Prefetch --}}
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//unpkg.com">

{{-- Preconnect --}}
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

{{-- Language Alternatives --}}
@if(isset($alternateLanguages))
    @foreach($alternateLanguages as $lang => $url)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
    @endforeach
@endif

{{-- RSS Feed --}}
@if(isset($rssFeed))
    <link rel="alternate" type="application/rss+xml" title="{{ $rssFeedTitle ?? config('app.name') . ' RSS' }}" href="{{ $rssFeed }}">
@endif

{{-- JSON-LD Structured Data --}}
@if(isset($structuredData))
    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif

{{-- Additional Custom Meta --}}
@if(isset($customMeta))
    @foreach($customMeta as $meta)
        @if(isset($meta['property']))
            <meta property="{{ $meta['property'] }}" content="{{ $meta['content'] }}">
        @elseif(isset($meta['name']))
            <meta name="{{ $meta['name'] }}" content="{{ $meta['content'] }}">
        @elseif(isset($meta['http-equiv']))
            <meta http-equiv="{{ $meta['http-equiv'] }}" content="{{ $meta['content'] }}">
        @endif
    @endforeach
@endif
