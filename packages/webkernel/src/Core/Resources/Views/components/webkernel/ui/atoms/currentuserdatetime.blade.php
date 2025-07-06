@php
    $inFooter = $inFooter ?? false;
    $fontSize = $fontSize ?? '98%';
@endphp


@if ($inFooter)
    <span x-data="{
        time: new Date().toLocaleString('fr-FR', {
            timeZone: '{{ \App\Models\User::getUserTimezone() }}',
            hour12: false,
            timeStyle: 'medium',
            dateStyle: 'short'
        }),
        init() {
            setInterval(() => {
                this.time = new Date().toLocaleString('fr-FR', {
                    timeZone: '{{ \App\Models\User::getUserTimezone() }}',
                    hour12: false,
                    timeStyle: 'medium',
                    dateStyle: 'short'
                });
            }, 1000);
        }
    }" style="font-size: {{ $fontSize }};">
        <span x-text="time"></span>
    </span>
@else
    <div x-data="{
        time: new Date().toLocaleString('fr-FR', {
            timeZone: '{{ \App\Models\User::getUserTimezone() }}',
            hour12: false,
            timeStyle: 'medium',
            dateStyle: 'short'
        }),
        init() {
            setInterval(() => {
                this.time = new Date().toLocaleString('fr-FR', {
                    timeZone: '{{ \App\Models\User::getUserTimezone() }}',
                    hour12: false,
                    timeStyle: 'medium',
                    dateStyle: 'short'
                });
            }, 1000);
        }
    }" class="text-gray-700 bold dark:text-white force-inter-ltr" style="margin-right:9px; font-size: {{ $fontSize }};">
        <span x-text="time" style="margin-right:9px;"></span>{{ CurrentUserTimezone() }}
    </div>
@endif
