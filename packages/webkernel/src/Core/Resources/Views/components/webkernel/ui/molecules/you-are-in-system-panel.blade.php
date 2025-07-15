@php
$showBanner = true;
@endphp

<div id="system-banner"
     style="display: none; background-color: var(--primary-900); color: white; text-align: center; padding: 6px 16px; position: relative;">
    <p class="fi-text-color-950"
       style="font-size: 0.875rem; font-weight: 600; margin: 0; padding: 3px 15px;">
        Bienvenue dans le panneau system de  {{ getCurrentApplication('name') }}{{ getCurrentApplication('version') }}.
        Ce panneau est dédié aux propriétaires de l'application et ses gestionnaires.
    </p>
    <button
        type="button"
        aria-label="Fermer"
        onclick="closeSystemBanner()"
        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: white; cursor: pointer; padding: 0;">
        <svg xmlns="http://www.w3.org/2000/svg" style="height: 16px; width: 16px;" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<script>
    const bannerKey = 'system_banner_closed_until';
    const banner = document.getElementById('system-banner');
    const now = new Date();
    const expiration = localStorage.getItem(bannerKey);

    if (!expiration || new Date(expiration) < now) {
        if (banner) banner.style.display = 'block';
    }

    function closeSystemBanner() {
        const expires = new Date();
        expires.setDate(expires.getDate() + 7);
        localStorage.setItem(bannerKey, expires.toISOString());
        if (banner) banner.style.display = 'none';
    }
</script>
