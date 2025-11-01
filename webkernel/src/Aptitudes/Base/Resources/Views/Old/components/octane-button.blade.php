@php
use Illuminate\Support\Facades\Cache;

// Check Octane status
$octaneStatus = 'inactive';
$isScheduled = false;

try {
    // Check if Octane is responding on port 8000
    $context = stream_context_create([
        'http' => [
            'timeout' => 2,
            'method' => 'HEAD'
        ]
    ]);
    
    $headers = @get_headers('http://localhost:8000', 0, $context);
    
    if ($headers !== false) {
        $octaneStatus = 'active';
    } else {
        // Check if Octane process is scheduled but not running
        $isScheduled = Cache::get('octane_scheduled', false);
        
        if ($isScheduled) {
            $octaneStatus = 'scheduled';
        } else {
            $octaneStatus = 'inactive';
        }
    }
} catch (Exception $e) {
    // Fallback: check if we're running under FrankenPHP with Octane configuration
    if (isset($_SERVER['SERVER_SOFTWARE']) && 
        str_contains($_SERVER['SERVER_SOFTWARE'], 'frankenphp')) {
        
        // Additional check for Octane-specific environment variables
        if (isset($_ENV['OCTANE_SERVER']) || config('octane.server') === 'frankenphp') {
            $octaneStatus = 'scheduled';
        }
    } else {
        $octaneStatus = 'inactive';
    }
}

// Update display properties based on status
$lightClass = match($octaneStatus) {
    'active' => 'bg-green-500 shadow-green-500/50 shadow-lg',
    'scheduled' => 'bg-amber-500 shadow-amber-500/50 shadow-lg', 
    default => 'bg-red-500 shadow-red-500/50 shadow-lg'
};

$statusText = match($octaneStatus) {
    'active' => 'Octane Active',
    'scheduled' => 'Octane Scheduled',
    default => 'Octane Inactive'
};

$iconClass = match($octaneStatus) {
    'active' => 'text-green-600',
    'scheduled' => 'text-amber-600',
    default => 'text-red-600'
};
@endphp

<div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 mb-4">
    <div class="flex items-center justify-center">
        <div class="relative group">
            <x-filament::dropdown placement="bottom-start">
                <x-slot name="trigger">
                    <x-filament::button
                        color="gray"
                        size="lg"
                        class="relative flex items-center space-x-4 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200">
                        
                        <!-- Traffic Light with REAL colors -->
                        <div class="relative">
                            <div class="w-4 h-4 rounded-full {{ $lightClass }} animate-pulse"></div>
                            <div class="absolute inset-0 w-4 h-4 rounded-full {{ $lightClass }} blur-sm opacity-60"></div>
                        </div>
                        
                        <!-- Status Text -->
                        <span class="font-medium text-gray-700 dark:text-gray-300 text-sm">
                            {{ $statusText }}
                        </span>
                        
                        <!-- Status Icon -->
                        <x-heroicon-o-server class="w-4 h-4 {{ $iconClass }}" />
                    </x-filament::button>
                </x-slot>

                <x-filament::dropdown.list class="min-w-48">
                    
                    @if($octaneStatus !== 'active')
                        <x-filament::dropdown.list.item
                            onclick="startOctane()"
                            icon="heroicon-o-play"
                            color="success">
                            Start Octane Server
                        </x-filament::dropdown.list.item>
                    @endif

                    @if($octaneStatus === 'active')
                        <x-filament::dropdown.list.item
                            onclick="restartOctane()"
                            icon="heroicon-o-arrow-path"
                            color="warning">
                            Restart Octane Server
                        </x-filament::dropdown.list.item>

                        <x-filament::dropdown.list.item
                            onclick="stopOctane()"
                            icon="heroicon-o-stop"
                            color="danger">
                            Stop Octane Server
                        </x-filament::dropdown.list.item>
                    @endif

                    <x-filament::dropdown.list.item
                        onclick="window.location.reload()"
                        icon="heroicon-o-arrow-path">
                        Refresh Status
                    </x-filament::dropdown.list.item>

                    @if($octaneStatus === 'active')
                        <x-filament::dropdown.list.item
                            onclick="clearOctaneCache()"
                            icon="heroicon-o-trash">
                            Clear Octane Cache
                        </x-filament::dropdown.list.item>
                    @endif

                </x-filament::dropdown.list>
            </x-filament::dropdown>
            
            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-sm font-medium text-white bg-gray-900 dark:bg-gray-700 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50">
                Server: {{ config('octane.server', 'frankenphp') }} 
                @if($octaneStatus === 'active')
                    • Port: 8000 • <span class="text-green-400">Online</span>
                @elseif($octaneStatus === 'scheduled')
                    • <span class="text-amber-400">Ready to Start</span>
                @else
                    • <span class="text-red-400">Offline</span>
                @endif
                <!-- Tooltip arrow -->
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-gray-700 rotate-45"></div>
            </div>
        </div>
    </div>
</div>

<script>
function startOctane() {
    fetch('/octane/start', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to start Octane: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error occurred');
    });
}

function stopOctane() {
    fetch('/octane/stop', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to stop Octane: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error occurred');
    });
}

function restartOctane() {
    fetch('/octane/restart', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to restart Octane: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error occurred');
    });
}

function clearOctaneCache() {
    fetch('/octane/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Octane cache cleared successfully');
        } else {
            alert('Failed to clear cache: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error occurred');
    });
}
</script>

<style>
/* Enhanced traffic light animation */
.animate-pulse {
    animation: traffic-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes traffic-pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

/* Smooth transitions for all interactive elements */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>