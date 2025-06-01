@php
    $inFooter = isset($inFooter) ? $inFooter : false;
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
    }">
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
    }" class="text-sm text-gray-900 dark:text-white force-inter-ltr" style="margin-right:9px;">
        <span x-text="time" style="margin-right:9px;"></span>{{ CurrentUserTimezone() }}
    </div>
@endif
