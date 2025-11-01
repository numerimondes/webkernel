@props([
    'inFooter' => false,
    'fontSize' => '98%',
    'type_user_datetime' => 'icon_time',
])
@php
$userTimezone = CurrentUserTimezoneName();
$userTimezoneDisplay = CurrentUserTimezone();
$modes = [
    'icon_only' => ['date' => false, 'time' => false, 'icon' => true, 'timezone' => false],
    'icon_time' => ['date' => false, 'time' => true, 'icon' => true, 'timezone' => false],
    'time_only' => ['date' => false, 'time' => true, 'icon' => false, 'timezone' => false],
    'time_date' => ['date' => true, 'time' => true, 'icon' => false, 'timezone' => false],
    'full' => ['date' => true, 'time' => true, 'icon' => false, 'timezone' => true],
];
$type_user_datetime = is_string($type_user_datetime) ? $type_user_datetime : 'icon_time';
$display = $modes[$type_user_datetime] ?? $modes['icon_time'];
$dateOpt = $display['date'] ? ", dateStyle: 'short'" : '';
$timeOpt = $display['time'] ? ", timeStyle: 'medium'" : '';
$formatOpts = trim($timeOpt . $dateOpt, ', ');
@endphp
<div x-data="{
    time: new Date().toLocaleString('fr-FR', { timeZone: '{{ $userTimezone }}', hour12: false{{ $formatOpts ? ', ' . $formatOpts : '' }} }),
    init() {
        setInterval(() => {
            this.time = new Date().toLocaleString('fr-FR', { timeZone: '{{ $userTimezone }}', hour12: false{{ $formatOpts ? ', ' . $formatOpts : '' }} });
        }, 1000);
    }
}"
class="whitespace-nowrap {{ !$inFooter ? ($display['icon'] ? 'inline-flex items-center gap-1.5' : 'text-gray-700 dark:text-white force-inter-ltr') : '' }}"
style="{{ $inFooter ? '' : 'margin-right:9px; ' }}font-size: {{ $fontSize }}; font-variant-numeric: tabular-nums;"
@if ($display['icon'])
    x-tooltip="'{{ $userTimezoneDisplay }} - ' + time"
@elseif(!$display['timezone'])
    x-tooltip="'{{ $userTimezoneDisplay }}'"
@endif>
    @if ($display['icon'])
        <x-filament::icon-button
            color="gray"
            :icon="\Filament\Support\Icons\Heroicon::OutlinedClock"
            icon-size="lg"
            badge=""
            class="fi-topbar-database-notifications-btn"
            style="display: inline-block !important; vertical-align: middle;" />
    @endif
    @if ($display['time'])
        <span x-text="time" class="inline-block" style="display: inline-block !important; vertical-align: middle;"></span>
    @endif
    @if ($display['timezone'] && !$inFooter)
        <span class="inline-block">{{ $userTimezoneDisplay }}</span>
    @endif
</div>

