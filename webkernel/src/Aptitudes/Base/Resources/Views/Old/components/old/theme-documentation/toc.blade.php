@php
    $tocItems = [
        [
            'id' => 'meet-numerimondes',
            'title' => 'Meet Numerimondes',
            'level' => 2,
            'children' => [
                ['id' => 'why-numerimondes', 'title' => 'Why Numerimondes?', 'level' => 3]
            ]
        ],
        [
            'id' => 'creating-a-project',
            'title' => 'Creating a Numerimondes Application',
            'level' => 2,
            'children' => [
                ['id' => 'installing-php', 'title' => 'Installing PHP and the Installer', 'level' => 3],
                ['id' => 'creating-an-application', 'title' => 'Creating an Application', 'level' => 3]
            ]
        ],
        [
            'id' => 'initial-configuration',
            'title' => 'Initial Configuration',
            'level' => 2,
            'children' => [
                ['id' => 'environment-config', 'title' => 'Environment Based Configuration', 'level' => 3],
                ['id' => 'databases-migrations', 'title' => 'Databases and Migrations', 'level' => 3],
                ['id' => 'directory-config', 'title' => 'Directory Configuration', 'level' => 3]
            ]
        ],
        [
            'id' => 'next-steps',
            'title' => 'Next Steps',
            'level' => 2,
            'children' => [
                ['id' => 'fullstack-framework', 'title' => 'Numerimondes the Full Stack Framework', 'level' => 3],
                ['id' => 'api-backend', 'title' => 'Numerimondes the API Backend', 'level' => 3]
            ]
        ]
    ];
@endphp

<!-- Right Sidebar - Table of Contents -->
<aside class="docs-toc bg-black border-l border-gray-800"
       x-data="{ activeSection: 'meet-numerimondes' }">
    <div class="px-6 py-8">
        <div class="sticky top-8">
            <h3 class="text-xs font-medium text-gray-400 mb-4 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-bars-3" class="w-4 h-4 text-gray-500" />
                On this page
            </h3>
            <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                <div class="border-l border-gray-700">
                    <ul class="space-y-1">
                        @foreach($tocItems as $item)
                            <li>
                                <a href="#{{ $item['id'] }}"
                                   class="toc-link inline-block border-l-[3px] border-transparent pl-4 py-1.5 text-[13px] text-gray-400 hover:text-gray-200 hover:border-gray-600 transition-all duration-200"
                                   :class="{ 'active border-red-500 text-white': activeSection === '{{ $item['id'] }}' }"
                                   @click="activeSection = '{{ $item['id'] }}'">
                                    {{ $item['title'] }}
                                </a>
                                @if(isset($item['children']))
                                    <ul class="mt-1 space-y-1">
                                        @foreach($item['children'] as $child)
                                            <li>
                                                <a href="#{{ $child['id'] }}"
                                                   class="toc-link inline-block border-l-[3px] border-transparent pl-8 py-1 text-[12px] text-gray-500 hover:text-gray-300 hover:border-gray-600 transition-all duration-200"
                                                   :class="{ 'active border-red-500 text-gray-200': activeSection === '{{ $child['id'] }}' }"
                                                   @click="activeSection = '{{ $child['id'] }}'">
                                                    {{ $child['title'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Publicité Numerimondes Pro -->
                <div class="mt-10">
                    <div class="relative hover:-translate-y-1 transition-transform duration-300">
                        <a href="https://numerimondes.pro" target="_blank" rel="noopener noreferrer"
                           class="block relative overflow-hidden rounded-lg p-5 h-48 bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 text-white">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <div class="relative z-10 h-full flex flex-col justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                        <x-filament::icon icon="heroicon-o-server" class="w-5 h-5 text-white" />
                                    </div>
                                    <span class="font-bold text-lg">Numerimondes</span>
                                </div>
                                <div>
                                    <p class="text-sm text-emerald-100 mb-4 leading-relaxed">
                                        Server management made simple for any PHP app
                                    </p>
                                    <div class="flex items-center text-white text-sm font-medium">
                                        <span>Learn more</span>
                                        <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4 ml-2" />
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>
