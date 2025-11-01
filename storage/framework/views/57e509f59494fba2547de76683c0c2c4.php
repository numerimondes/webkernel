
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


<?php if(isset($enablePerformanceMonitoring) && $enablePerformanceMonitoring): ?>
    <script>
        // Performance monitoring
        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType("navigation")[0];
            console.log('Page Load Time:', perfData.loadEventEnd - perfData.fetchStart, 'ms');
        });
    </script>
<?php endif; ?>


<?php if(isset($googleAnalyticsId)): ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($googleAnalyticsId); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo e($googleAnalyticsId); ?>', {
            page_title: '<?php echo e($pageTitle ?? $title ?? config('app.name')); ?>',
            custom_map: {'dimension1': 'user_type'}
        });
    </script>
<?php endif; ?>


<?php if(isset($googleTagManagerId)): ?>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo e($googleTagManagerId); ?>');
    </script>
<?php endif; ?>


<?php if(isset($facebookPixelId)): ?>
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
        fbq('init', '<?php echo e($facebookPixelId); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?php echo e($facebookPixelId); ?>&ev=PageView&noscript=1"
    /></noscript>
<?php endif; ?>


<?php if(isset($hotjarId)): ?>
    <!-- Hotjar Tracking Code -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:<?php echo e($hotjarId); ?>,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
<?php endif; ?>


<?php if(isset($clarityId)): ?>
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "<?php echo e($clarityId); ?>");
    </script>
<?php endif; ?>


<?php if(isset($enableCookieConsent) && $enableCookieConsent): ?>
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
                message: '<?php echo e($cookieMessage ?? "This website uses cookies to ensure you get the best experience."); ?>',
                dismiss: '<?php echo e($cookieDismiss ?? "Got it!"); ?>',
                link: '<?php echo e($cookieLink ?? "Learn more"); ?>',
                href: '<?php echo e($cookieHref ?? "/privacy-policy"); ?>'
            }
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.1/cookieconsent.min.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.1/cookieconsent.min.css" />
<?php endif; ?>


<?php if(isset($enableServiceWorker) && $enableServiceWorker): ?>
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
<?php endif; ?>


<script>
    // Theme detection before page load
    (function() {
        const theme = localStorage.getItem('theme') || '<?php echo e($defaultTheme ?? 'dark'); ?>';
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>


<?php echo $__env->yieldPushContent('head-scripts'); ?>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/webkernel/src/Aptitudes/WebsiteBuilder/Resources/Views/live-website/layouts/partials/head-scripts.blade.php ENDPATH**/ ?>