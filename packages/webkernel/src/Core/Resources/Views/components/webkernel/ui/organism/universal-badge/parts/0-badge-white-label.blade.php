@php
    $show_this_badge = false;
    
    $text_font_weight = '900';
    $spacing_between_elements = '8px';
    
    // Variables nécessaires depuis le parent
    $brand = $brand ?? corePlatformInfos('brandName');
    $logo = $logo ?? platformAbsoluteUrlAnyPrivatetoPublic(corePlatformInfos('logoLink'));
    $poweredByText = $poweredByText ?? lang('powered_by');
    $poweredLinkBrand = $poweredLinkBrand ?? "https://numerimondes.com";
    $brandLink = $brandLink ?? "Numerimondes";
    $BADGE_SENSITIVITY = $BADGE_SENSITIVITY ?? 'nothing';
    $isRTL = $isRTL ?? false;
@endphp

@if(isset($show_this_badge) && $show_this_badge)
<div class="credit-badge-ui-main group"
    style="
        padding: 6px 12px; font-size: 14px;
        font-family: sans-serif; background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        cursor: {{ $BADGE_SENSITIVITY === 'clic' ? 'pointer' : 'default' }};
        transition: all 0.2s ease;
        height: 32px;
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
    "
    class="whitespace-nowrap gap-2 @if($isRTL) flex-row-reverse @endif hover:bg-white/20 hover:shadow-lg hover:scale-105">

    @if ($isRTL)
        @if ($BADGE_SENSITIVITY !== 'nothing')
            <span class="powered-by-link flex items-center gap-1 group-hover:text-blue-200 transition-colors">
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    class="text-white no-underline flex items-center hover:text-blue-200 transition-colors">
                    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square"
                        class="w-4 h-4 flex-shrink-0" />
                </a>
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    class="text-white no-underline flex items-center hover:text-blue-200 transition-colors">
                    <span class="text-sm" style="font-weight: {{ $text_font_weight }};">
                        {{ $brandLink }}
                    </span>
                </a>
            </span>
            <span class="powered-by-text" style="color: currentColor; font-size: 12px; font-weight: {{ $text_font_weight }}; display: inline-block; vertical-align: middle; margin-right: {{ $spacing_between_elements }};">
                {{ $poweredByText }}
            </span>
        @endif
        <span class="brand-text" style="color: currentColor; font-size: 14px; font-weight: {{ $text_font_weight }}; display: inline-block; vertical-align: middle; margin-right: {{ $spacing_between_elements }};">
            {{ $brand }} </span>
        <img src="{{ $logo }}" alt="Logo"
            style="width: 20px !important; height: 20px !important; display: inline-block !important; vertical-align: middle !important; margin-right: {{ $spacing_between_elements }};"
            class="object-contain flex-shrink-0 group-hover:scale-110 transition-transform">
    @else
        <img src="{{ $logo }}" alt="Logo"
            style="width: 20px !important; height: 20px !important; margin-right: {{ $spacing_between_elements }};"
            class="object-contain flex-shrink-0 group-hover:scale-110 transition-transform">
        <span class="brand-text text-base flex items-center" style="font-weight: {{ $text_font_weight }}; color: currentColor; margin-right: {{ $spacing_between_elements }};">
            {{ $brand }} </span>
        @if ($BADGE_SENSITIVITY !== 'nothing')
            <span class="powered-by-text text-sm flex items-center" style="font-weight: {{ $text_font_weight }}; color: currentColor; margin-right: {{ $spacing_between_elements }};">
                {{ $poweredByText }}
            </span>
            <span class="powered-by-link flex items-center gap-1 group-hover:text-blue-200 transition-colors">
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    class="text-white no-underline flex items-center hover:text-blue-200 transition-colors">
                    <span class="text-sm" style="font-weight: {{ $text_font_weight }};">
                        {{ $brandLink }}
                    </span>
                </a>
                <a href="{{ $poweredLinkBrand }}" target="_blank" rel="noopener noreferrer"
                    class="text-white no-underline flex items-center hover:text-blue-200 transition-colors">
                    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square"
                        class="w-4 h-4 flex-shrink-0" />
                </a>
            </span>
        @endif
    @endif
</div>
@else
@endif