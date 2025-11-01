<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Updates Manager</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Include Filament styles and fonts --}}
    @filamentStyles
    @filamentScripts
    {{ filament()->getTheme()->getHtml() }}
    {{ filament()->getFontHtml() }}
    {{ filament()->getMonoFontHtml() }}
    {{ filament()->getSerifFontHtml() }}

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<style>
    /**
     * Responsive Adjustments
     * Removes shadows on mobile for cleaner appearance
     */
    @media (max-width: 640px) {
        .fi-simple-main {
            --tw-ring-shadow: none;
        }
    }

    /**
     * Base Background and Layout System
     * Creates full-screen background with smooth transitions
     */
    html,
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1920&h=1080&fit=crop&q=95');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        transition: background-image 0.8s ease-in-out;

font-family: "SF Pro Display", -apple-system, BlinkMacSystemFont, "Helvetica Neue", Helvetica, Arial, sans-serif;
        z-index: 1000;
    }

    /**
     * Custom Font Loading
     * SF Pro Display for modern Apple-like appearance
     */
    @font-face {
        font-family: "SF Pro Display";
        src: url("https://cdn.fontcdn.ir/Fonts/SFProDisplay/5bc1142d5fc993d2ec21a8fa93a17718818e8172dffc649b7d8a3ab459cfbf9c.woff2") format("woff2");
        font-weight: 400;
        font-style: normal;
    }

    /**
     * Gradient Overlay System
     * Creates depth and improves text readability
     */
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: -1;
        pointer-events: none;
    }

    /**
     * Grain Effect Implementation
     * Adds subtle texture when enabled in effects array
     */
        body::after {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: -1;
        background-image: radial-gradient(circle, transparent 1px, rgba(255, 255, 255, 0.15) 1px);
        background-size: 4px 4px;
        pointer-events: none;
        opacity: 0.3;
    }


/**
         * Main Layout Structure
         * Centers content with proper spacing
         */
    .fi-simple-layout {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .fi-simple-main-ctn {
        width: 100%;
        max-width: 1024px;
    }

    /**
     * Glass Morphism Card Design
     * Creates modern frosted glass effect
     */
    .fi-simple-main {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: fadeIn 0.6s ease-out;
        padding: 2rem !important;
    }

    /**
     * Animation Definitions
     * Smooth entry animations for content elements
     */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-up {
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /**
     * Status Badge Styling
     * Color-coded status indicators
     */
    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.7rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background: rgba(34, 197, 94, 0.2);
        color: rgb(34, 197, 94);
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .status-registry-not-found {
        background: rgba(251, 191, 36, 0.2);
        color: rgb(251, 191, 36);
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    /**
     * Section and Component Overrides
     * Custom styling for Filament components
     */
    x-filament\:\:section {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        margin: 1.5rem 0;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: none !important;
    }

    /**
     * Module Logo Styling
     * Responsive logo with proper spacing
     */
    .module-logo {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .module-logo {
            width: 48px;
            height: 48px;
            margin-right: 0.75rem;
        }
    }

    /**
     * Updated Grid Layout
     * Gives more space to title/description column (2/3) vs status column (1/3)
     */
    .pure-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    @media (min-width: 768px) {
        .pure-grid {
            grid-template-columns: 2fr 1fr;
        }
    }

    /**
     * Flex Layout Utilities
     * Reusable flex containers for consistent alignment
     */
    .flex-container {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .flex-column {
        display: flex;
        flex-direction: column;
    }

    .spaced-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /**
     * Right-aligned Status Container
     * Aligns status and module ID to the right
     */
    .status-container {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 1rem;
        text-align: right;
    }

    /**
     * Title with Version Styling
     * Allows version to be displayed inline with title
     */
    .title-version-container {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /**
     * Compact Module Info Grid
     * Reduces spacing for module information display
     */
    .module-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .module-info-item {
        display: flex;
        flex-direction: column;
    }

    .module-info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .module-info-value {
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* Scrollable list area to avoid full-page fill when there are many modules */
    .updater-scroll {
        position: relative;
        max-height: min(70vh, 900px);
        overflow-y: auto;
        overscroll-behavior: contain;
        padding-right: 0.25rem;
        margin-top: 0.5rem;

        /* Add a mask for a fade-out effect on scroll */
        -webkit-mask-image: linear-gradient(to bottom, transparent 0, black 1rem, black calc(100% - 1rem), transparent 100%);
        mask-image: linear-gradient(to bottom, transparent 0, black 1rem, black calc(100% - 1rem), transparent 100%);
    }
</style>
<body class="fi fi-simple-layout min-h-screen h-full antialiased text-gray-900">
<div class="fi-simple-main-ctn">
    <div class="fi-simple-main fi-width-lg">
        <div class="relative">
    <!-- Decorative gradient background -->
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(1200px_600px_at_50%_-10%,rgba(99,102,241,0.35),transparent),radial-gradient(1000px_400px_at_20%_120%,rgba(16,185,129,0.35),transparent)]"></div>
    <!-- Subtle noise overlay -->
    <div class="pointer-events-none absolute inset-0 opacity-[0.05] mix-blend-soft-light" style="background-image: url(''); background-size: 280px 280px;"></div>

    <main class="relative fi-simple">
        <div class="mx-auto w-full max-w-7xl py-12">
            <!-- Page header -->
            <header class="mb-10 text-center">
                <div class="inline-flex items-center justify-center rounded-2xl bg-white/30 backdrop-blur-xl ring-1 ring-white/30 px-4 py-2 shadow-sm">
                    <span class="text-xs font-medium tracking-wide text-gray-700">Updates</span>
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-semibold tracking-tight text-gray-900">
                    Module Updates Manager
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Manage checks and scheduling for module updates
                </p>
            </header>

            <!-- Toolbar -->
            <section class="mb-6">
                <div id="status"></div>
                <div class="mx-auto flex flex-wrap items-center justify-center gap-3">
                    <button id="btn-check-all"
                            class="fi-btn inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-inset ring-indigo-500/30 hover:bg-indigo-700 active:bg-indigo-800 transition">
                        <x-heroicon-o-arrow-path class="h-4 w-4" />
                        Check all
                    </button>
                    <button id="btn-schedule-all"
                            class="fi-btn inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-inset ring-emerald-500/30 hover:bg-emerald-700 active:bg-emerald-800 transition">
                        <x-heroicon-o-calendar-days class="h-4 w-4" />
                        Schedule all
                    </button>
                </div>
            </section>

            @php
                // Ensure $modules is defined; fallback to discovery.
                if (!isset($modules)) {
                    try {
                        /** @var \Webkernel\Arcanes\Assemble\ArcaneBuildModule $arc */
                        $arc = app()->make(\Webkernel\Arcanes\Assemble\ArcaneBuildModule::class);
                        $discovered = $arc->discoverAndRegisterModules();
                        $modules = collect($discovered)->map(function ($m) {
                            $id = method_exists($m, 'getModuleId') ? $m->getModuleId() : (method_exists($m, 'getId') ? $m->getId() : class_basename($m));
                            $version = method_exists($m, 'getVersion') ? $m->getVersion() : (property_exists($m, 'version') ? $m->version : '0.0.0');
                            $installPath = method_exists($m, 'getBasePath') ? $m->getBasePath() : null;
                            $updatedAt = now();
                            return (object)[
                                'module_id' => (string) $id,
                                'version' => (string) $version,
                                'install_path' => $installPath,
                                'updated_at' => $updatedAt,
                            ];
                        });
                    } catch (\Throwable $e) {
                        $modules = collect();
                    }
                } else {
                    $modules = collect($modules);
                }

                // Deduplicate by module_id
                $modules = $modules->filter(fn($m) => !empty($m->module_id))
                                   ->unique('module_id')
                                   ->values();
            @endphp

                <!-- Modules list -->
            <div class="updater-scroll">
                <div class="grid grid-cols-1 gap-6 lg:gap-8">
                @forelse($modules as $module)
                    <x-filament::section
                        id="module-{{ $module->module_id }}"
                        class="!bg-transparent !p-0 !shadow-none "
                    >

                            <div class="flex items-start justify-between gap-6">
                                <div class="space-y-2">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600/10 ring-1 ring-indigo-500/20 text-indigo-600">
                                <x-heroicon-o-puzzle-piece class="h-4 w-4" />
                            </span>
                                        {{ $module->module_id }}
                                    </h3>
                                    <p class="text-sm text-gray-700">
                                        Current Version:
                                        <span class="font-medium text-gray-900">{{ $module->version }}</span>
                                    </p>
                                    <p class="text-xs text-gray-600 flex items-center gap-1.5">
                                        <x-heroicon-o-folder class="h-4 w-4 text-gray-500" />
                                        Install Path: {{ $module->install_path ?? 'Not specified' }}
                                    </p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1.5">
                                        <x-heroicon-o-clock class="h-4 w-4 text-gray-400" />
                                        Last Updated: {{ $module->updated_at }}
                                    </p>
                                </div>

                                <div class="shrink-0 flex items-center gap-2">
                                    <button class="fi-btn inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-inset ring-blue-500/30 hover:bg-blue-700 active:bg-blue-800 transition"
                                            onclick="checkUpdates('{{ $module->module_id }}', '{{ $module->version }}')">
                                        <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                                        Check Updates
                                    </button>
                                </div>
                            </div>

                            <div id="updates-{{ $module->module_id }}" class="updates-list mt-4 text-sm text-gray-800"></div>
                            <progress id="progress-{{ $module->module_id }}" class="w-full h-2 mt-3 accent-indigo-600" value="0" max="100" style="display: none;"></progress>
                    </x-filament::section>
                @empty
                    <x-filament::section class="!bg-transparent !p-0 !shadow-none !ring-0">
                        <div class="rounded-2xl border border-dashed border-white/30 bg-white/60 backdrop-blur-xl p-10 text-center text-gray-700 ring-1 ring-white/30 shadow-md">
                            <p class="mb-2 text-base">No modules discovered locally.</p>
                            <p class="text-sm">Use the global actions above. When modules become available, they will appear here.</p>
                        </div>
                    </x-filament::section>
                @endforelse
                </div>
            </div>


            <!-- Scheduled updates -->
            <section class="mt-12">

                @php
                    // Work even if the model does not exist or DB not ready.
                    $scheduledUpdates = collect();
                    try {
                        if (class_exists(\App\Models\ScheduledUpdate::class)) {
                            $scheduledUpdates = \App\Models\ScheduledUpdate::query()->get();
                        }
                    } catch (\Throwable $e) {
                        $scheduledUpdates = collect();
                    }
                @endphp
                @if($scheduledUpdates->isEmpty())
                    <p class="text-center text-gray-700">No updates scheduled.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($scheduledUpdates as $update)
                            <li class="rounded-xl bg-white/70 backdrop-blur-xl ring-1 ring-white/30 border border-white/20 px-4 py-3 shadow-sm text-sm text-gray-900">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="flex-1">
                                        <span class="text-gray-500">Module:</span> <span class="font-medium">{{ $update->module_id }}</span>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <span class="text-gray-500">Version:</span> <span class="font-medium">{{ $update->version }}</span>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <span class="text-gray-500">Scheduled:</span> <span class="font-medium">{{ $update->scheduled_at }}</span>
                                        <span class="mx-2 text-gray-300">|</span>
                                        <span class="text-gray-500">Status:</span> <span class="font-medium">{{ $update->status }}</span>
                                        @if($update->error_message)
                                            <br><span class="text-red-600">Error: {{ $update->error_message }}</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </main>
    <h2 class="mt-12 mb-4 text-xl font-semibold text-gray-900 text-center flex items-center justify-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600/10 ring-1 ring-indigo-500/20 text-indigo-600">
                            <x-heroicon-o-queue-list class="h-5 w-5" />
                        </span>
        Scheduled Updates
    </h2>
</div>
</div>
<script>
    // Cache updates per module so we can "Schedule all"
    const updatesCache = new Map();

    // Blade exposes modules list for "Check all" and "Schedule all"
    const modulesList = @json($modules->pluck('module_id')->values());

    // UI helpers
    function showStatus(message, type = 'loading') {
        const statusDiv = document.getElementById('status');
        const styles = {
            base: 'rounded-xl p-3 mb-4 text-sm text-center ring-1 shadow-sm backdrop-blur-xl',
            loading: 'bg-sky-50/80 text-sky-800 ring-sky-200/70',
            success: 'bg-emerald-50/80 text-emerald-800 ring-emerald-200/70',
            error: 'bg-red-50/80 text-red-800 ring-red-200/70',
        };
        const klass = `${styles.base} ${styles[type] || styles.loading}`;
        statusDiv.innerHTML = `<div class="${klass}">${message}</div>`;
        if (type !== 'loading') {
            setTimeout(() => statusDiv.innerHTML = '', 5000);
        }
    }

    // Compare semantic versions (basic)
    function compareSemver(a, b) {
        const pa = String(a ?? '0.0.0').split('.').map(n => parseInt(n, 10) || 0);
        const pb = String(b ?? '0.0.0').split('.').map(n => parseInt(n, 10) || 0);
        for (let i = 0; i < Math.max(pa.length, pb.length); i++) {
            const x = pa[i] || 0, y = pb[i] || 0;
            if (x > y) return 1;
            if (x < y) return -1;
        }
        return 0;
    }

    // Single module: check updates
    async function checkUpdates(moduleId, currentVersion) {
        showStatus(`Checking updates for ${moduleId}...`);
        const progress = document.getElementById(`progress-${moduleId}`);
        if (progress) {
            progress.style.display = 'block';
            progress.value = 0;
        }

        try {
            const response = await fetch('/updates/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    module_id: moduleId,
                    current_version: currentVersion
                })
            });

            if (progress) progress.value = 50;
            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                const updates = Array.isArray(data.updates) ? data.updates : [];
                // Sort updates by version desc (best effort)
                updates.sort((u1, u2) => compareSemver(u2?.version, u1?.version));
                updatesCache.set(moduleId, updates);
                displayUpdates(moduleId, updates);
                showStatus(`Updates checked successfully for ${moduleId}`, 'success');
            } else {
                showStatus(`Error ${data.code ?? response.status}: ${data.error ?? 'Unknown error'}`, 'error');
            }
        } catch (error) {
            showStatus(`Network error: ${error.message}`, 'error');
        } finally {
            if (progress) {
                progress.value = 100;
                setTimeout(() => progress.style.display = 'none', 600);
            }
        }
    }

    // Render updates list for module
    function displayUpdates(moduleId, updates) {
        const updatesDiv = document.getElementById(`updates-${moduleId}`);
        if (!updatesDiv) return;

        if (!Array.isArray(updates) || updates.length === 0) {
            updatesDiv.innerHTML = '<p class="text-gray-600">No updates available</p>';
            return;
        }

        let html = '<h4 class="font-medium text-gray-900 mb-2 flex items-center gap-2"><span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-amber-100 text-amber-700 text-xs ring-1 ring-amber-200">UP</span> Available Updates</h4>';
        updates.forEach(update => {
            const v = update?.version ?? '';
            const rd = update?.release_date ?? '';
            html += `
                <div class="rounded-xl border border-white/20 bg-white/80 backdrop-blur-xl p-3 mb-2 shadow-sm ring-1 ring-white/30">
                    <p class="text-sm text-gray-800 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 6v12m6-6H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            ${v}
                        </span>
                        ${rd ? `<span class="text-gray-500">Release date:</span> <span class="font-medium">${rd}</span>` : ''}
                    </p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <button class="fi-btn inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3 py-2 text-sm font-medium text-black shadow-sm ring-1 ring-amber-500/30 hover:bg-amber-600 active:bg-amber-700 transition"
                                onclick="scheduleUpdate('${moduleId}', '${v}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 0 0 2-2V7H5v12a2 2 0 0 0 2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Schedule
                        </button>
                        <button class="fi-btn inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-red-500/30 hover:bg-red-700 active:bg-red-800 transition"
                                onclick="skipUpdate('${moduleId}', '${v}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Skip
                        </button>
                    </div>
                </div>
            `;
        });
        updatesDiv.innerHTML = html;
    }

    // Single module: schedule one update
    async function scheduleUpdate(moduleId, version) {
        if (!version) {
            showStatus(`No version to schedule for ${moduleId}`, 'error');
            return;
        }
        showStatus(`Scheduling update ${version} for ${moduleId}...`, 'loading');
        try {
            const res = await fetch('/updates/schedule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ module_id: moduleId, version })
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok) {
                showStatus('Update scheduled successfully', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showStatus(`Error ${data.code ?? res.status}: ${data.error ?? 'Unknown error'}`, 'error');
            }
        } catch (e) {
            showStatus(`Network error: ${e.message}`, 'error');
        }
    }

    function skipUpdate(moduleId, version) {
        showStatus(`Skipped update ${version || ''} for ${moduleId}`, 'success');
    }

    // Global: check all modules sequentially (avoid server overload)
    async function checkAll() {
        if (!Array.isArray(modulesList) || modulesList.length === 0) {
            showStatus('No modules to check', 'error');
            return;
        }
        showStatus(`Checking updates for ${modulesList.length} module(s)...`, 'loading');
        for (const moduleId of modulesList) {
            const card = document.getElementById(`module-${moduleId}`);
            const versionText = card?.querySelector('p span.font-medium')?.textContent?.trim() || '0.0.0';
            // eslint-disable-next-line no-await-in-loop
            await checkUpdates(moduleId, versionText);
        }
        showStatus('Finished checking all modules', 'success');
    }

    // Global: schedule best available update per module if any exists
    async function scheduleAll() {
        if (!Array.isArray(modulesList) || modulesList.length === 0) {
            showStatus('No modules to schedule', 'error');
            return;
        }
        showStatus('Scheduling updates for all modules with available updates...', 'loading');

        for (const moduleId of modulesList) {
            const updates = updatesCache.get(moduleId);
            if (Array.isArray(updates) && updates.length > 0) {
                const best = updates[0];
                const version = best?.version;
                if (version) {
                    // eslint-disable-next-line no-await-in-loop
                    await scheduleUpdate(moduleId, version);
                }
            }
        }
        showStatus('Finished scheduling updates', 'success');
    }

    // Bind global buttons
    document.getElementById('btn-check-all')?.addEventListener('click', checkAll);
    document.getElementById('btn-schedule-all')?.addEventListener('click', scheduleAll);
</script>
</body>
</html>
