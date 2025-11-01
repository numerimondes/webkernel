<x-filament-panels::page>
    <x-filament::card>
        <form wire:submit.prevent="save">
            {{ $this->form }}

            <div class="mt-4 flex gap-2">
                <x-filament::button type="submit">
                    Save Configuration
                </x-filament::button>

                <x-filament::button color="secondary" wire:click="sync">
                    Sync Now
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>

    @if($license = \Webkernel\Aptitudes\Platform\Core\Models\LocalLicense::first())
        <x-filament::card class="mt-4">
            <h3 class="text-lg font-semibold mb-4">License Status</h3>

            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Domain</dt>
                    <dd class="text-sm text-gray-900">{{ $license->domain }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd>
                        <x-filament::badge :color="$license->status === 'active' ? 'success' : 'danger'">
                            {{ ucfirst($license->status) }}
                        </x-filament::badge>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Synced</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $license->last_synced_at?->diffForHumans() ?? 'Never' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Expires</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $license->expires_at?->format('Y-m-d') ?? 'Never' }}
                    </dd>
                </div>
            </dl>
        </x-filament::card>
    @endif
</x-filament-panels::page>
