<div style="background-color: var(--primary-900); color: white; text-align: center; padding: 6px 16px; position: relative;">
    <p class="fi-text-color-950"
    style="font-size: 0.875rem; font-weight: 600; margin: 0; padding:3px; padding-left:15px;padding-right:15px;">
       Bienvenue dans le panneau system de {{ corePlatformInfos('brandName')}} v.{{ corePlatformInfos('version')}}. Ce panneau est dedié aux proprietaire de l'application et ses gestionnaires.
    </p>
    <button
        type="button"
        aria-label="Fermer"
        onclick="this.parentElement.style.display='none'"
        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: white; cursor: pointer; padding: 0;">
        {{-- Heroicon X (Close) --}}
        <svg xmlns="http://www.w3.org/2000/svg" style="height: 16px; width: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
