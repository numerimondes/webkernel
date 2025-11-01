{{-- Alpine.js --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Performance Monitoring --}}
@if(isset($enablePerformanceMonitoring) && $enablePerformanceMonitoring)
    <script>
        // Performance monitoring
        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType("navigation")[0];
            console.log('Page Load Time:', perfData.loadEventEnd - perfData.fetchStart, 'ms');
        });
    </script>
@endif

{{-- Google Analytics --}}
@if(isset($googleAnalyticsId))
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $googleAnalyticsId }}', {
            page_title: '{{ $pageTitle ?? $title ?? config('app.name') }}',
            custom_map: {'dimension1': 'user_type'}
        });
    </script>
@endif

{{-- Google Tag Manager --}}
@if(isset($googleTagManagerId))
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $googleTagManagerId }}');
    </script>
@endif

{{-- Facebook Pixel --}}
@if(isset($facebookPixelId))
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $facebookPixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $facebookPixelId }}&ev=PageView&noscript=1"
    /></noscript>
@endif

{{-- Hotjar --}}
@if(isset($hotjarId))
    <!-- Hotjar Tracking Code -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:{{ $hotjarId }},hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
@endif

{{-- Microsoft Clarity --}}
@if(isset($clarityId))
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "{{ $clarityId }}");
    </script>
@endif

{{-- Cookie Consent --}}
@if(isset($enableCookieConsent) && $enableCookieConsent)
    <script>
        window.cookieConsentConfig = {
            autoShow: true,
            position: 'bottom-right',
            theme: 'classic',
            palette: {
                popup: {
                    background: '#1f2937',
                    text: '#ffffff'
                },
                button: {
                    background: '#3b82f6',
                    text: '#ffffff'
                }
            },
            content: {
                message: '{{ $cookieMessage ?? "This website uses cookies to ensure you get the best experience." }}',
                dismiss: '{{ $cookieDismiss ?? "Got it!" }}',
                link: '{{ $cookieLink ?? "Learn more" }}',
                href: '{{ $cookieHref ?? "/privacy-policy" }}'
            }
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.1/cookieconsent.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.1/cookieconsent.min.css" />
@endif

{{-- Service Worker Registration --}}
@if(isset($enableServiceWorker) && $enableServiceWorker)
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
@endif

{{-- Theme Detection Script - Simplified --}}
<script>
    // Theme detection before page load
    (function() {
        const theme = localStorage.getItem('theme') || '{{ $defaultTheme ?? 'dark' }}';
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

{{-- Custom Head Scripts Stack --}}
@stack('head-scripts')
