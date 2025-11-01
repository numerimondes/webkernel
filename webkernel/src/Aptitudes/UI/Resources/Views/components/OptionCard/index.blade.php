@php
use Webkernel\Aptitudes\UI\Resources\Views\components\OptionCard\OptionCard;
$option_card = new OptionCard($attributes->getAttributes());
$cardsToRender = $option_card->isMultiple && $option_card->cards
    ? $option_card->cards
    : [$attributes->getAttributes()];

// Calculate responsive breakpoints based on minimum width and gap
$minWidth = 168;
$gap = (int)$option_card->get('gap', '16');
$columns = (int)$option_card->get('columns', 2);

// Calculate breakpoints for responsive behavior
$breakpoints = [];
for ($i = $columns; $i >= 1; $i--) {
    $totalWidth = ($minWidth * $i) + ($gap * ($i - 1));
    $breakpoints[$i] = $totalWidth;
}
@endphp

<style>
.option-cards-grid {
    display: grid;
    gap: {{ $gap }}px;
    width: 100%;
    grid-template-columns: repeat(auto-fill, minmax(max({{ $minWidth }}px, 100%/{{ $columns }} - {{ $gap * ($columns - 1) / $columns }}px), 1fr));
}

/* Enable container queries */
.option-cards-container {
    /* container-type: inline-size; - No longer needed with this approach */
}

.option-card {
    position: relative;
    display: flex;
    flex-direction: column;
    border: none;
    border-radius: 8px;
    box-shadow: var(--shadow-sm, 0 1px 2px 0 rgba(0, 0, 0, 0.05)), inset 0 0 0 0.5px var(--gray-200, #e5e7eb);
    transition: all 0.2s ease;
    text-decoration: none;
    width: 100%;
    min-width: 0; /* Allow cards to shrink below minimum when necessary */
    box-sizing: border-box;
}

.option-card:hover:not([disabled]) {
    box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1)), inset 0 0 0 0.5px var(--gray-300, #d1d5db);
    transform: translateY(-2px);
}

.option-card[disabled] {
    cursor: not-allowed;
    opacity: 0.5;
}

.option-card:not([disabled]) {
    cursor: pointer;
}

.option-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
}

.option-card-title-section {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
}

.option-card-icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.option-card-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin: 0;
    font-size: 16px;
    line-height: 1.2;
    font-weight: 500;
}

.option-card-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.option-card-badge {
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 9999px;
    white-space: nowrap;
}

.option-card-notification {
    flex-shrink: 0;
}

.option-card-notification-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.option-card-notification-count {
    min-width: 16px;
    height: 16px;
    padding: 0 6px;
    color: white;
    font-size: 12px;
    font-weight: 500;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.option-card-description {
    font-size: 12px;
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-wrap: break-word;
    hyphens: auto;
}

/* Responsive Typography */
@@media (max-width: 768px) {
    .option-card-title {
        font-size: 15px;
    }

    .option-card-badge {
        font-size: 11px;
        padding: 3px 6px;
    }

    .option-card-description {
        font-size: 11px;
    }
}

@@media (max-width: 480px) {
    .option-card-title {
        font-size: 14px;
    }

    .option-card-badge {
        font-size: 10px;
        padding: 2px 4px;
    }

    .option-card-description {
        font-size: 10px;
    }

    .option-card-icon {
        width: 14px;
        height: 14px;
    }
}
</style>

<div class="option-cards-container" style="
    padding-top: {{ $option_card->get('padding-top', $option_card->get('container-padding', '0')) }};
    padding-bottom: {{ $option_card->get('padding-bottom', $option_card->get('container-padding', '0')) }};
    padding-left: {{ $option_card->get('padding-left', $option_card->get('container-padding', '0')) }};
    padding-right: {{ $option_card->get('padding-right', $option_card->get('container-padding', '0')) }};
">
    <div class="{{ $option_card->isMultiple ? 'option-cards-grid' : '' }}">
        @foreach($cardsToRender as $cardData)
            @php
            $cardConfig = $option_card->isMultiple
                ? array_merge($attributes->getAttributes(), $cardData)
                : $cardData;
            $card = new OptionCard($cardConfig);
            @endphp

            <{{ $card->tag }}
                @if($card->href)
                    href="{{ $card->href }}" target="_blank" rel="noopener noreferrer"
                @endif
                @if($card->target) target="{{ $card->target }}" @endif
                @if($card->disabled) disabled @endif
                class="option-card"
                style="
                    padding-top: {{ $card->get('card-padding-top', $card->get('padding', '12')) }}px;
                    padding-bottom: {{ $card->get('card-padding-bottom', $card->get('padding', '12')) }}px;
                    padding-left: {{ $card->get('card-padding-left', $card->get('padding', '12')) }}px;
                    padding-right: {{ $card->get('card-padding-right', $card->get('padding', '12')) }}px;
                    box-shadow: var(--shadow-sm, 0 1px 2px 0 rgba(0, 0, 0, 0.05)), inset 0 0 0 0.5px var(--{{ $card->color }}-200, #e5e7eb);
                "
                onmouseover="if (!this.disabled) { this.style.boxShadow='var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1)), inset 0 0 0 0.5px var(--{{ $card->color }}-300, #d1d5db)'; this.style.transform='translateY(-2px)'; }"
                onmouseout="this.style.boxShadow='var(--shadow-sm, 0 1px 2px 0 rgba(0, 0, 0, 0.05)), inset 0 0 0 0.5px var(--{{ $card->color }}-200, #e5e7eb)'; this.style.transform='translateY(0)';"
            >
                <div class="option-card-header">
                    <div class="option-card-title-section">
                        @if($card->icon)
                        <x-dynamic-component
                            :component="'lucide-' . $card->icon"
                            class="option-card-icon"
                            style="color: {{ $card->iconColor }};"
                        />
                        @endif
                        <h3 class="option-card-title">
                            {{ $card->title }}
                        </h3>
                    </div>

                    <div class="option-card-actions">
                        @if($card->badge)
                        <span class="option-card-badge" style="{{ $card->badgeStyle }}">
                            {{ $card->badge }}
                        </span>
                        @endif

                        @if($card->notification)
                        <div class="option-card-notification">
                            @if($card->notification === true || $card->notification == 1)
                            <span class="option-card-notification-dot" style="background-color: {{ $card->notificationBg }};"></span>
                            @else
                            <span class="option-card-notification-count" style="background-color: {{ $card->notificationBg }};">
                                {{ $card->notification }}
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                @if($card->description)
                <p class="option-card-description">
                    {{ $card->description }}
                </p>
                @endif
            </{{ $card->tag }}>
        @endforeach
    </div>
</div>
