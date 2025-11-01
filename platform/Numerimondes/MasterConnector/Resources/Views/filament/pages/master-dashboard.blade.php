<x-filament-panels::page>
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
        {{-- Total Licenses --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Licenses</p>
                    <p class="text-2xl font-semibold">{{ $licenseStats['total'] }}</p>
                </div>
                <x-heroicon-o-key class="w-10 h-10 text-primary-500" />
            </div>
        </x-filament::card>

        {{-- Active Licenses --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Licenses</p>
                    <p class="text-2xl font-semibold text-success-600">{{ $licenseStats['active'] }}</p>
                </div>
                <x-heroicon-o-check-circle class="w-10 h-10 text-success-500" />
            </div>
        </x-filament::card>

        {{-- Total Modules --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Modules</p>
                    <p class="text-2xl font-semibold">{{ $moduleStats['total'] }}</p>
                </div>
                <x-heroicon-o-cube class="w-10 h-10 text-primary-500" />
            </div>
        </x-filament::card>

        {{-- Storage Used --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Storage Used</p>
                    <p class="text-2xl font-semibold">
                        {{ number_format($moduleStats['total_size'] / 1024 / 1024, 2) }} MB
                    </p>
                </div>
                <x-heroicon-o-circle-stack class="w-10 h-10 text-primary-500" />
            </div>
        </x-filament::card>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        {{-- Recent Downloads --}}
        <x-filament::card>
            <h3 class="text-lg font-semibold mb-4">Recent Downloads (24h)</h3>

            @if($recentDownloads->isEmpty())
                <p class="text-sm text-gray-500">No recent downloads</p>
            @else
                <div class="space-y-3">
                    @foreach($recentDownloads as $download)
                        <div class="flex items-center justify-between border-b pb-2">
                            <div class="flex-1">
                                <p class="text-sm font-medium">{{ $download->license?->domain ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $download->module?->name ?? 'Multiple modules' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <x-filament::badge :color="$download->success ? 'success' : 'danger'">
                                    {{ $download->success ? 'Success' : 'Failed' }}
                                </x-filament::badge>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $download->downloaded_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::card>

        {{-- Top Modules --}}
        <x-filament::card>
            <h3 class="text-lg font-semibold mb-4">Most Downloaded Modules</h3>

            @if(empty($topModules))
                <p class="text-sm text-gray-500">No download data available</p>
            @else
                <div class="space-y-3">
                    @foreach($topModules as $module)
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium">{{ $module['name'] }}</p>
                            <x-filament::badge color="primary">
                                {{ $module['downloads'] }} downloads
                            </x-filament::badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::card>
    </div>

    {{-- Recent License Activity --}}
    <x-filament::card class="mt-4">
        <h3 class="text-lg font-semibold mb-4">Recent License Activity</h3>

        @if(empty($recentActivity))
            <p class="text-sm text-gray-500">No recent activity</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Domain</th>
                            <th class="text-left py-2">Status</th>
                            <th class="text-left py-2">Last Validated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivity as $activity)
                            <tr class="border-b">
                                <td class="py-2">{{ $activity['domain'] }}</td>
                                <td class="py-2">
                                    <x-filament::badge
                                        :color="$activity['status'] === 'active' ? 'success' : 'danger'">
                                        {{ ucfirst($activity['status']) }}
                                    </x-filament::badge>
                                </td>
                                <td class="py-2 text-gray-500">{{ $activity['last_validated'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::card>
</x-filament-panels::page>
