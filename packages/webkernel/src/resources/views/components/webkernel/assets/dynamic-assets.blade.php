@php
    $settings = public_platform_settings();
    $pwaEnabled = $settings['PWA_ENABLED'] ?? true;
@endphp

<link rel="stylesheet" href="{{ route('dynamic.css') }}">
<script src="{{ route('dynamic.js') }}"></script>

@if($pwaEnabled)
    <link rel="manifest" href="{{ route('manifest.json') }}">
    <meta name="theme-color" content="{{ $settings['PWA_THEME_COLOR'] ?? '#3b82f6' }}">
    <link rel="apple-touch-icon" href="{{ $settings['PLATFORM_LOGO'] ?? '/images/logo.png' }}">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ route('service-worker.js') }}');
            });
        }
    </script>
@endif
