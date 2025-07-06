{!! getPlatformMetaTagsHtml() !!}
{!! getPlatformOpenGraphTagsHtml() !!}
{!! getPlatformTwitterTagsHtml() !!}
{!! getPlatformFaviconHtml() !!}


@push('scripts')
<script>
(function() {
    // Function to check favicon and apply fallback if needed
    function checkFavicon() {
        const faviconLink = document.querySelector('link[rel="icon"]');
        if (!faviconLink) {
            console.debug('No favicon link found');
            return;
        }

        const faviconUrl = faviconLink.getAttribute('href');
        const fallbackUrl = faviconLink.getAttribute('data-fallback');
        if (!faviconUrl || !fallbackUrl) {
            console.debug('Missing favicon or fallback URL', { faviconUrl, fallbackUrl });
            return;
        }

        // Skip if already checked this URL
        if (faviconLink.dataset.lastChecked === faviconUrl) {
            return;
        }
        faviconLink.dataset.lastChecked = faviconUrl;

        // Use Image object for favicon testing (more reliable than <link>)
        const img = new Image();
        img.src = faviconUrl;

        // Timeout for slow or hanging requests (3 seconds)
        const timeout = 3000;
        let timedOut = false;

        const timeoutId = setTimeout(() => {
            timedOut = true;
            applyFallback(faviconLink, fallbackUrl);
            console.debug('Favicon load timed out:', faviconUrl);
        }, timeout);

        img.onload = () => {
            if (!timedOut) {
                clearTimeout(timeoutId);
                console.debug('Favicon loaded successfully:', faviconUrl);
            }
        };

        img.onerror = () => {
            if (!timedOut) {
                clearTimeout(timeoutId);
                applyFallback(faviconLink, fallbackUrl);
                console.debug('Favicon failed to load:', faviconUrl);
            }
        };
    }

    // Function to apply fallback URL and update MIME type
    function applyFallback(faviconLink, fallbackUrl) {
        faviconLink.setAttribute('href', fallbackUrl);
        const extension = fallbackUrl.split('.').pop().toLowerCase();
        const mimeTypes = {
            'ico': 'image/x-icon',
            'png': 'image/png',
            'jpg': 'image/jpeg',
            'jpeg': 'image/jpeg',
            'gif': 'image/gif',
            'svg': 'image/svg+xml',
            'webp': 'image/webp'
        };
        const mimeType = mimeTypes[extension] || 'image/x-icon';
        faviconLink.setAttribute('type', mimeType);
        console.debug('Applied fallback favicon:', fallbackUrl);
    }

    // Initial check
    document.addEventListener('DOMContentLoaded', checkFavicon);

    // Observe <head> for changes (SPA or tenant switching)
    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                const faviconLink = document.querySelector('link[rel="icon"]');
                if (faviconLink && faviconLink.getAttribute('href') !== faviconLink.dataset.lastChecked) {
                    checkFavicon();
                }
            }
        }
    });

    observer.observe(document.head, { childList: true, attributes: true, subtree: true });

    // Handle Inertia.js navigation (for Filament SPA panels)
    if (typeof Inertia !== 'undefined') {
        Inertia.on('navigate', () => {
            setTimeout(checkFavicon, 100); // Delay to ensure DOM is updated
        });
    }
})();
</script>

<script>
const element = document.querySelector('.fi-modal-close-overlay');
if (element) {
    element.remove();
}
</script>

@endpush
