@php
    $navigationSections = [
        [
            'title' => 'Prologue',
            'items' => [
                ['title' => 'Release Notes', 'href' => '#release-notes'],
                ['title' => 'Upgrade Guide', 'href' => '#upgrade-guide'],
                ['title' => 'Contribution Guide', 'href' => '#contribution-guide']
            ]
        ],
        [
            'title' => 'Getting Started',
            'items' => [
                ['title' => 'Installation', 'href' => '#installation'],
                ['title' => 'Configuration', 'href' => '#configuration'],
                ['title' => 'Directory Structure', 'href' => '#directory-structure'],
                ['title' => 'Frontend', 'href' => '#frontend'],
                ['title' => 'Deployment', 'href' => '#deployment']
            ]
        ],
        [
            'title' => 'Architecture Concepts',
            'items' => [
                ['title' => 'Request Lifecycle', 'href' => '#request-lifecycle'],
                ['title' => 'Service Container', 'href' => '#service-container'],
                ['title' => 'Service Providers', 'href' => '#service-providers'],
                ['title' => 'Facades', 'href' => '#facades']
            ]
        ],
        [
            'title' => 'The Basics',
            'items' => [
                ['title' => 'Routing', 'href' => '#routing'],
                ['title' => 'Middleware', 'href' => '#middleware'],
                ['title' => 'CSRF Protection', 'href' => '#csrf-protection'],
                ['title' => 'Controllers', 'href' => '#controllers'],
                ['title' => 'Requests', 'href' => '#requests'],
                ['title' => 'Responses', 'href' => '#responses']
            ]
        ]
    ];
@endphp

<!-- Left Sidebar -->
<aside class="docs-sidebar bg-black border-r border-gray-800">
    <div class="px-6 py-8">
        <!-- Version Selector -->
        <div class="mb-8">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Version</label>
            <select class="mt-2 block w-full rounded-md border-gray-700 bg-gray-900 text-gray-200 text-sm focus:ring-red-500 focus:border-red-500 px-3 py-2">
                <option>11.x</option>
                <option selected>12.x</option>
                <option>Master</option>
            </select>
        </div>

        <!-- Navigation -->
        <nav class="space-y-8">
            @foreach($navigationSections as $section)
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                        {{ $section['title'] }}
                    </h3>
                    <ul class="space-y-2">
                        @foreach($section['items'] as $item)
                            <li>
                                <a href="{{ $item['href'] }}"
                                   class="block text-sm font-medium text-gray-300 hover:text-red-400 py-1 transition-colors">
                                    {{ $item['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>
    </div>
</aside>
