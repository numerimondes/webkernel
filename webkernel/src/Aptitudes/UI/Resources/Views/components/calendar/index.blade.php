{{-- Calendar Component - Website Builder Standardized Component

    Usage:
    <x-ui::calendar
        selected="2024-06-15"
        mode="single"
        debug="true"
        :config="$jsonConfig"
        :action-button="['text' => 'Book Now', 'href' => '/book?date={date}&time={time}']"
        :block-past="true"
        :enable-time="true"
    />
--}}

{{--
<x-ui::dynamic-ui ... loads the javascript and css for this element inline and once only!
--}}

<x-ui::dynamic-ui for="calendar"/>

@props([
    // Data API
    'selected' => null,
    'mode' => 'single', // single, multiple, range, multi-range
    'max' => null,
    'min' => null,
    'disabled' => null,
    'required' => false,
    'weekStart' => 1, // 0 = Sunday, 1 = Monday (EU standard)
    'blockPast' => false,
    'enableTime' => false,
    'timeStep' => 15, // minutes
    'minTime' => '09:00',
    'maxTime' => '18:00',

    // Style API - Standardized Website Builder Theme System
    'variant' => 'default', // default, outline, ghost, minimal
    'size' => 'md', // sm, md, lg
    'theme' => 'auto', // auto, light, dark
    'config' => null, // JSON configuration override

    // Layout API
    'width' => 'auto',
    'container' => true,
    'showHeader' => true,
    'showWeekdays' => true,

    // Action API
    'actionButton' => null, // ['text' => 'Book', 'href' => '/book?date={date}&time={time}']

    // Debug API
    'debug' => false,

    // Event API
    'onSelect' => null,
    'onChange' => null,
    'onNavigate' => null,
])

@php
    // Merge JSON config if provided
    if ($config && is_string($config)) {
        $config = json_decode($config, true) ?? [];
        foreach ($config as $key => $value) {
            if (!isset($$key)) {
                $$key = $value;
            }
        }
    }

    // Calculate calendar data in PHP
    $now = new DateTime();
    $currentMonth = $now->format('n') - 1; // 0-based for JS compatibility
    $currentYear = (int) $now->format('Y');

    // Generate day labels based on week start (EU standard = Monday first)
    $allDayLabels = [
        __('Dim'), __('Lun'), __('Mar'), __('Mer'), __('Jeu'), __('Ven'), __('Sam')
    ];
    $dayLabels = array_merge(
        array_slice($allDayLabels, $weekStart),
        array_slice($allDayLabels, 0, $weekStart)
    );

    // Month names (French by default)
    $monthNames = [
        __('Janvier'), __('Février'), __('Mars'), __('Avril'),
        __('Mai'), __('Juin'), __('Juillet'), __('Août'),
        __('Septembre'), __('Octobre'), __('Novembre'), __('Décembre')
    ];

    // Generate time options if enabled
    $timeOptions = [];
    if ($enableTime) {
        $start = new DateTime($minTime);
        $end = new DateTime($maxTime);
        $interval = new DateInterval('PT' . $timeStep . 'M');

        while ($start <= $end) {
            $timeOptions[] = $start->format('H:i');
            $start->add($interval);
        }
    }

    // Standardized Website Builder Style System
    $sizeClasses = match($size) {
        'sm' => 'p-3 text-xs min-h-[280px] max-w-[280px]',
        'md' => 'p-4 text-sm min-h-[320px] max-w-[320px]',
        'lg' => 'p-6 text-base min-h-[380px] max-w-[380px]',
        default => 'p-4 text-sm min-h-[320px] max-w-[320px]'
    };

    $variantClasses = match($variant) {
        'default' => 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm',
        'outline' => 'bg-transparent border-2 border-gray-300 dark:border-gray-600 rounded-xl',
        'ghost' => 'bg-transparent border-0 rounded-xl',
        'minimal' => 'bg-gray-50 dark:bg-gray-800 border-0 rounded-xl',
        default => 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm'
    };

    $baseClasses = $container ? "antialiased font-inter {$sizeClasses} {$variantClasses}" : 'p-0 font-inter';
    $calendarClasses = $attributes->twMerge([$baseClasses]);
    $debugMode = filter_var($debug, FILTER_VALIDATE_BOOLEAN);
    $blockPastDates = filter_var($blockPast, FILTER_VALIDATE_BOOLEAN);
@endphp

{{-- Standardized Website Builder Component Styles --}}

<div
    x-data="wbCalendarComponent({
        selected: {{ $selected ? json_encode($selected) : 'null' }},
        mode: '{{ $mode }}',
        disabled: {{ $disabled ? json_encode($disabled) : 'null' }},
        min: {{ $min ? json_encode($min) : 'null' }},
        max: {{ $max ? json_encode($max) : 'null' }},
        required: {{ $required ? 'true' : 'false' }},
        weekStart: {{ $weekStart }},
        debug: {{ $debugMode ? 'true' : 'false' }},
        blockPast: {{ $blockPastDates ? 'true' : 'false' }},
        enableTime: {{ $enableTime ? 'true' : 'false' }},
        timeOptions: {{ json_encode($timeOptions) }},
        actionButton: {{ $actionButton ? json_encode($actionButton) : 'null' }},
        monthNames: {{ json_encode($monthNames) }}
    })"
    @keydown.left.prevent="focusAdd(-1)"
    @keydown.right.prevent="focusAdd(1)"
    @keydown.up.prevent="focusAdd(-7)"
    @keydown.down.prevent="focusAdd(7)"
    @keydown.enter.prevent="activateFocused()"
    @keydown.space.prevent="activateFocused()"
    class="{{ $calendarClasses }} wb-calendar-component relative"
    x-modelable="value"
>
    {{-- Debug indicator --}}
    @if($debugMode)
    <div class="wb-debug">DEBUG</div>
    @endif

    {{-- Header with navigation --}}
    @if($showHeader)
    <div class="flex items-center justify-between mb-4">
        <button
            @click="debugLog('Navigation: Previous month clicked'); previousMonth()"
            type="button"
            class="wb-calendar-nav-button inline-flex items-center justify-center rounded-lg text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border h-9 w-9"
            style="border-color: rgb(var(--wb-border)); background-color: rgb(var(--wb-muted)); color: rgb(var(--wb-muted-foreground)); --tw-ring-color: rgb(var(--wb-ring));">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="font-semibold text-lg" style="color: rgb(var(--wb-secondary-foreground));">
            <span x-text="monthNames[month]"></span>
            <span x-text="year" class="ml-2 opacity-75"></span>
        </div>

        <button
            @click="debugLog('Navigation: Next month clicked'); nextMonth()"
            type="button"
            class="wb-calendar-nav-button inline-flex items-center justify-center rounded-lg text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border h-9 w-9"
            style="border-color: rgb(var(--wb-border)); background-color: rgb(var(--wb-muted)); color: rgb(var(--wb-muted-foreground)); --tw-ring-color: rgb(var(--wb-ring));">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- Week day headers --}}
    @if($showWeekdays)
    <div class="grid grid-cols-7 mb-3">
        @foreach($dayLabels as $day)
            <div class="px-1">
                <div class="text-xs font-semibold text-center uppercase tracking-wider py-2"
                     style="color: rgb(var(--wb-muted-foreground));">{{ $day }}</div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Calendar grid --}}
    <div class="grid grid-cols-7 gap-1">
        {{-- Previous month blank days --}}
        <template x-for="blankDay in preBlankDaysInMonth" :key="'pre-' + blankDay">
            <div class="flex items-center justify-center h-10 w-full">
                <span x-text="blankDay" class="text-xs opacity-30" style="color: rgb(var(--wb-muted-foreground));"></span>
            </div>
        </template>

        {{-- Current month days --}}
        <template x-for="(day, dayIndex) in daysInMonth" :key="'day-' + dayIndex">
            <button
                tabindex="-1"
                type="button"
                x-text="day"
                :disabled="isDisabled(new Date(year, month, day))"
                @click="dayClicked(day);"
                class="wb-calendar-day-button inline-flex items-center justify-center rounded-lg text-sm transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 disabled:pointer-events-none h-10 w-full font-medium"
                :class="{
                    'wb-calendar-day-selected': isSelectedDay(day),
                    'wb-calendar-day-range-start': isRangeStart(day),
                    'wb-calendar-day-range-end': isRangeEnd(day),
                    'wb-calendar-day-range-middle': isRangeMiddle(day),
                    'wb-calendar-day-today': !isSelectedDay(day) && !isRangeMiddle(day) && isToday(day),
                    'wb-calendar-day-hover': !isSelectedDay(day) && !isRangeMiddle(day) && !isDisabled(new Date(year, month, day)),
                    'wb-calendar-day-disabled': isDisabled(new Date(year, month, day))
                }"
                style="--tw-ring-color: rgb(var(--wb-ring));">
            </button>
        </template>

        {{-- Next month blank days --}}
        <template x-for="blankDay in postBlankDaysInMonth" :key="'post-' + blankDay">
            <div class="flex items-center justify-center h-10 w-full">
                <span x-text="blankDay" class="text-xs opacity-30" style="color: rgb(var(--wb-muted-foreground));"></span>
            </div>
        </template>
    </div>

    {{-- Time picker --}}
    <div x-show="enableTime && hasSelectedDate()" class="mt-4 border-t pt-4" style="border-color: rgb(var(--wb-border));">
        <label class="block text-sm font-medium mb-2" style="color: rgb(var(--wb-secondary-foreground));">
            Choisir l'heure
        </label>
        <div class="wb-time-picker border rounded-lg" style="border-color: rgb(var(--wb-border));">
            <template x-for="time in timeOptions" :key="time">
                <div
                    @click="selectTime(time)"
                    class="wb-time-option"
                    :class="{ 'selected': selectedTime === time }"
                    x-text="time">
                </div>
            </template>
        </div>
    </div>

    {{-- Action button --}}
    <div x-show="actionButton && hasSelectedDate() && (!enableTime || selectedTime)" class="mt-4">
        <a
            :href="generateActionHref()"
            class="wb-action-button w-full"
            x-text="actionButton ? actionButton.text : 'Action'">
        </a>
    </div>

    {{-- Debug info --}}
    <div x-show="debug" class="mt-4 p-3 bg-gray-100 rounded text-xs">
        <div><strong>Value:</strong> <span x-text="JSON.stringify(value)"></span></div>
        <div><strong>Selected Time:</strong> <span x-text="selectedTime"></span></div>
        <div><strong>Mode:</strong> <span x-text="mode"></span></div>
    </div>
</div>
