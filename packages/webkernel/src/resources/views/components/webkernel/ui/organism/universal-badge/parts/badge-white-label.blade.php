<div class="credit-badge-ui-main"
    style="
        padding: 6px 12px; font-size: 11px;
        font-family: sans-serif; color: white; background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        display: flex; align-items: center; white-space: nowrap;
        gap: 5px; cursor: {{ $BADGE_SENSITIVITY === 'clic' ? 'pointer' : 'default' }}; transition: all 0.2s ease;
        height: 32px;
        @if ($isRTL) flex-direction: row-reverse; @endif
    ">
    @if ($isRTL)
        @if ($BADGE_SENSITIVITY !== 'nothing')
            <span class="powered-by-link">
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    style="color: currentColor; text-decoration: none;">
                    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="external-link-icon"
                        style="width: {{ $svg_width }}px; height: {{ $svg_width }}px; max-width: 100%; max-height: 100%;" />
                </a>
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    style="color: currentColor; text-decoration: none;">
                    <span class="text-sm font-bold leading-6 text-gray-950 dark:text-white">
                        {{ $brandLink }}
                    </span>
                </a>
            </span>
            <span class="powered-by-text text-sm font-bold leading-6 text-gray-950 dark:text-white">
                {{ $poweredByText }}
            </span>
        @endif
        <span class="brand-text text-sm font-bold leading-6 text-gray-950 dark:text-white">{{ $brand }}</span>
        <img src="{{ $logo }}" alt="Logo"
            style="width: {{ $img_width }}px; height: auto; display: inline-block; vertical-align: middle;">
    @else
        <img src="{{ $logo }}" alt="Logo"
            style="width: {{ $img_width }}px; height: auto; display: inline-block; vertical-align: middle;">
        <span class="brand-text text-sm font-bold leading-6 text-gray-950 dark:text-white">{{ $brand }}</span>
        @if ($BADGE_SENSITIVITY !== 'nothing')
            <span class="powered-by-text text-sm font-bold leading-6 text-gray-950 dark:text-white">
                {{ $poweredByText }}
            </span>
            <span class="powered-by-link">
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    style="color: currentColor; text-decoration: none;">
                    <span class="text-sm font-bold leading-6 text-gray-950 dark:text-white">
                        {{ $brandLink }}
                    </span>
                </a>
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    style="color: currentColor; text-decoration: none;">
                    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="external-link-icon"
                        style="width: {{ $svg_width }}px; height: {{ $svg_width }}px; max-width: 100%; max-height: 100%;" />
                </a>
            </span>
        @endif
    @endif
</div>

@include('webkernel::components.webkernel.ui.organism.universal-badge.parts.badge-help')

