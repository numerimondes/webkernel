@php
    use App\Models\User;
    $inFooter = isset($inFooter) ? $inFooter : false; // Vérifie si une variable 'isFooter' est passée
@endphp

@if ($inFooter)
    <!-- Si le composant est utilisé dans un footer, on utilise des <span> au lieu de <div> -->
        <span x-data="{
            time: new Date().toLocaleString('fr-FR', {
                timeZone: '{{ User::getUserTimezone() }}',
                hour12: false,
                timeStyle: 'medium',
                dateStyle: 'short'
            }),
            init() {
                setInterval(() => {
                    this.time = new Date().toLocaleString('fr-FR', {
                        timeZone: '{{ User::getUserTimezone() }}',
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
    <!-- Sinon, on garde un <div> pour les autres cas -->
    <div x-data="{
        time: new Date().toLocaleString('fr-FR', {
            timeZone: '{{ User::getUserTimezone() }}',
            hour12: false,
            timeStyle: 'medium',
            dateStyle: 'short'
        }),
        init() {
            setInterval(() => {
                this.time = new Date().toLocaleString('fr-FR', {
                    timeZone: '{{ User::getUserTimezone() }}',
                    hour12: false,
                    timeStyle: 'medium',
                    dateStyle: 'short'
                });
            }, 1000);
        }
    }" class="text-sm text-gray-900 dark:text-white" style="margin-right:9px;">
        <span x-text="time" style="margin-right:9px;"></span>{{ CurrentUserTimezone() }}
    </div>
@endif
