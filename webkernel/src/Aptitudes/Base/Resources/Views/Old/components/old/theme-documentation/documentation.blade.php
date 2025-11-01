<!DOCTYPE html>
<html lang="fr" x-data="{
    darkMode: true,
    activeSection: 'meet-laravel',
    tocItems: [
        { id: 'meet-laravel', title: 'Meet Laravel', level: 2, children: [
            { id: 'why-laravel', title: 'Why Laravel?', level: 3 }
        ]},
        { id: 'creating-a-laravel-project', title: 'Creating a Laravel Application', level: 2, children: [
            { id: 'installing-php', title: 'Installing PHP and the Laravel Installer', level: 3 },
            { id: 'creating-an-application', title: 'Creating an Application', level: 3 }
        ]},
        { id: 'initial-configuration', title: 'Initial Configuration', level: 2, children: [
            { id: 'environment-based-configuration', title: 'Environment Based Configuration', level: 3 },
            { id: 'databases-and-migrations', title: 'Databases and Migrations', level: 3 },
            { id: 'directory-configuration', title: 'Directory Configuration', level: 3 }
        ]},
        { id: 'installation-using-herd', title: 'Installation Using Herd', level: 2, children: [
            { id: 'herd-on-macos', title: 'Herd on macOS', level: 3 },
            { id: 'herd-on-windows', title: 'Herd on Windows', level: 3 }
        ]},
        { id: 'ide-support', title: 'IDE Support', level: 2 },
        { id: 'laravel-and-ai', title: 'Laravel and AI', level: 2, children: [
            { id: 'installing-laravel-boost', title: 'Installing Laravel Boost', level: 3 }
        ]},
        { id: 'next-steps', title: 'Next Steps', level: 2, children: [
            { id: 'laravel-the-fullstack-framework', title: 'Laravel the Full Stack Framework', level: 3 },
            { id: 'laravel-the-api-backend', title: 'Laravel the API Backend', level: 3 }
        ]}
    ]
}" :class="{ 'dark': darkMode }" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation Laravel Style</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- CodeMirror CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/default.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'laravel-red': '#ef3b2d',
                        'sand-light': {
                            '5': '#f7f6f4',
                            '9': '#a8a69f',
                            '11': '#6c6a63',
                            '12': '#1c1b18'
                        },
                        'sand-dark': {
                            '4': '#3a3934',
                            '5': '#44433d',
                            '11': '#b5b3ad',
                            '12': '#eeeeec'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: black;
            color: white;
        }
        .docs-layout {
            display: flex;
            min-height: calc(100vh - 64px);
        }
        .docs-sidebar {
            width: 280px;
            flex-shrink: 0;
            height: calc(100vh - 64px);
            overflow-y: auto;
            position: sticky;
            top: 64px;
        }
        .docs-content {
            flex: 1;
            min-width: 0;
            max-width: calc(100vw - 560px);
        }
        .docs-toc {
            width: 280px;
            flex-shrink: 0;
            height: calc(100vh - 64px);
            overflow-y: auto;
            position: sticky;
            top: 64px;
        }
        @media (max-width: 1280px) {
            .docs-toc {
                display: none;
            }
            .docs-content {
                max-width: calc(100vw - 280px);
            }
        }
        @media (max-width: 1024px) {
            .docs-sidebar {
                display: none;
            }
            .docs-content {
                max-width: 100vw;
            }
        }
        /* CodeMirror customization */
        .CodeMirror {
            height: auto;
            min-height: 200px;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 0 0 12px 12px;
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }
        .CodeMirror-scroll {
            min-height: 200px;
        }
        .CodeMirror-gutters {
            background: #1e293b !important;
            border-right: 1px solid #334155 !important;
        }
        .CodeMirror-linenumber {
            color: #64748b !important;
        }
        .toc-link.active {
            border-left-color: #ef3b2d !important;
            color: white !important;
        }
        /* Scrollbar styling for dark theme */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        ::-webkit-scrollbar-thumb {
            background: #404040;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #525252;
        }
    </style>
</head>
<body class="bg-black text-white antialiased">
    <!-- Top Navigation -->
    <nav class="bg-black border-b border-gray-800 sticky top-0 z-50 h-16">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">Laravel</span>
                    </div>
                    <div class="hidden lg:flex items-center space-x-8 ml-8">
                        <a href="#" class="text-sm font-medium text-gray-300 hover:text-red-400 transition-colors">Docs</a>
                        <a href="#" class="text-sm font-medium text-gray-300 hover:text-red-400 transition-colors">Bootcamp</a>
                        <a href="#" class="text-sm font-medium text-gray-300 hover:text-red-400 transition-colors">News</a>
                        <a href="#" class="text-sm font-medium text-gray-300 hover:text-red-400 transition-colors">Partners</a>
                    </div>
                </div>
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <button class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-400 bg-gray-900 border border-gray-700 rounded-lg hover:border-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Search</span>
                    </button>
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode"
                            class="p-2 text-gray-400 hover:text-gray-300 rounded-lg transition-colors">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>
                    <!-- GitHub -->
                    <button class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-400 bg-gray-900 border border-gray-700 rounded-lg hover:border-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        <span>GitHub</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-screen-2xl mx-auto docs-layout">
        <!-- Left Sidebar -->
        <aside class="docs-sidebar bg-black border-r border-gray-800">
            <div class="px-6 py-8">
                <!-- Version Selector avec style Filament -->
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
                    <template x-for="section in [
                        { title: 'Prologue', items: [
                            { title: 'Release Notes', href: '#release-notes' },
                            { title: 'Upgrade Guide', href: '#upgrade-guide' },
                            { title: 'Contribution Guide', href: '#contribution-guide' }
                        ]},
                        { title: 'Getting Started', items: [
                            { title: 'Installation', href: '#installation' },
                            { title: 'Configuration', href: '#configuration' },
                            { title: 'Directory Structure', href: '#directory-structure' },
                            { title: 'Frontend', href: '#frontend' },
                            { title: 'Deployment', href: '#deployment' }
                        ]},
                        { title: 'Architecture Concepts', items: [
                            { title: 'Request Lifecycle', href: '#request-lifecycle' },
                            { title: 'Service Container', href: '#service-container' },
                            { title: 'Service Providers', href: '#service-providers' },
                            { title: 'Facades', href: '#facades' }
                        ]},
                        { title: 'The Basics', items: [
                            { title: 'Routing', href: '#routing' },
                            { title: 'Middleware', href: '#middleware' },
                            { title: 'CSRF Protection', href: '#csrf-protection' },
                            { title: 'Controllers', href: '#controllers' },
                            { title: 'Requests', href: '#requests' },
                            { title: 'Responses', href: '#responses' }
                        ]}
                    ]" :key="section.title">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3" x-text="section.title"></h3>
                            <ul class="space-y-2">
                                <template x-for="item in section.items" :key="item.href">
                                    <li>
                                        <a :href="item.href"
                                           class="block text-sm font-medium text-gray-300 hover:text-red-400 py-1 transition-colors"
                                           x-text="item.title">
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="docs-content">
            <div class="px-6 lg:px-12 py-12">
                <!-- Breadcrumb -->
                <nav class="flex items-center space-x-2 text-sm text-gray-400 mb-8">
                    <a href="#" class="hover:text-red-400 transition-colors">Documentation</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span>Getting Started</span>
                </nav>

                <!-- Article -->
                <article class="max-w-4xl">
                    <!-- Header -->
                    <header class="mb-12">
                        <h1 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                            Installation
                        </h1>
                        <p class="text-xl text-gray-400 leading-relaxed">
                            Laravel is a web application framework with expressive, elegant syntax. We've already laid the foundation — freeing you to create without sweating the small things.
                        </p>
                    </header>

                    <!-- Content Sections -->
                    <template x-for="(section, index) in [
                        { id: 'meet-laravel', title: 'Meet Laravel', content: 'Laravel is a web application framework with expressive, elegant syntax. A web framework provides a structure and starting point for creating your application, allowing you to focus on creating something amazing while we sweat the details.' },
                        { id: 'creating-a-laravel-project', title: 'Your First Laravel Project', content: 'Laravel strives to provide an amazing developer experience while providing powerful features such as thorough dependency injection, expressive database abstraction layer, queues and scheduled jobs, unit and integration testing, and more.' },
                        { id: 'initial-configuration', title: 'Laravel & Docker', content: 'We want it to be as easy as possible to get started with Laravel regardless of your preferred operating system. So, there are a variety of options for developing and running a Laravel project on your local machine.' },
                        { id: 'next-steps', title: 'Next Steps', content: 'Now that you have created your first Laravel application, you might be wondering what to learn next. First, we strongly recommend becoming familiar with how Laravel works by reading the following documentation.' }
                    ]" :key="section.id">
                        <section :id="section.id" class="mb-16"
                                 x-intersect:enter.half="activeSection = section.id">
                            <h2 class="text-2xl font-bold text-white mb-6">
                                <a :href="'#' + section.id" class="group" x-text="section.title">
                                    <span class="opacity-0 group-hover:opacity-100 text-red-500 ml-2 transition-opacity">#</span>
                                </a>
                            </h2>
                            <div class="prose prose-lg max-w-none prose-gray prose-invert mb-8">
                                <p class="text-gray-400 leading-relaxed mb-6" x-text="section.content"></p>
                            </div>

                            <!-- Code Example -->
                            <div class="mb-8" x-show="index < 3">
                                <h3 class="text-lg font-semibold text-white mb-4" x-text="'Code Example ' + (index + 1)"></h3>
                                <div class="bg-gray-900 rounded-xl border border-gray-700 overflow-hidden shadow-sm">
                                    <div class="border-b border-gray-700 px-4 py-3 bg-gray-800/50">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="flex space-x-1">
                                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-400" x-text="'example' + (index + 1) + '.php'"></span>
                                            </div>
                                            <button class="flex items-center space-x-2 px-2 py-1 text-xs text-gray-400 bg-gray-700 border border-gray-600 rounded hover:border-gray-500 transition-colors"
                                                    @click="copyToClipboard($event)">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>Copy</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-gray-950 text-gray-200 text-sm font-mono overflow-x-auto">
                                        <template x-if="index === 0">
                                            <pre class="text-gray-200"><code>&lt;?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        return response()->view('welcome', [
            'message' => 'Welcome to Laravel!',
            'version' => app()->version()
        ]);
    }

    public function show(Request $request, $id): Response
    {
        return response()->json([
            'id' => $id,
            'user' => $request->user(),
            'timestamp' => now()
        ]);
    }
}</code></pre>
                                        </template>
                                        <template x-if="index === 1">
                                            <pre class="text-gray-200"><code>&lt;?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('users', UserController::class);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
         ->name('profile.edit');
});</code></pre>
                                        </template>
                                        <template x-if="index === 2">
                                            <pre class="text-gray-200"><code>&lt;?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'slug', 'published_at', 'user_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}</code></pre>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Box -->
                            <div class="bg-blue-900/20 border border-blue-700 rounded-lg p-6 mb-8" x-show="index === 0">
                                <div class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="font-semibold text-blue-100 mb-2">Note</h4>
                                        <p class="text-blue-200 text-sm leading-relaxed">
                                            Whether you're a beginner or you have prior experience, Laravel grows with you.
                                            We'll help you take your first steps as a web developer or give you a boost as you take your expertise to the next level.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Section amélioré -->
                            <div class="bg-gradient-to-br from-red-600 via-red-500 to-orange-500 rounded-2xl p-8 text-white shadow-2xl" x-show="index === 3">
                                <div class="max-w-2xl">
                                    <div class="flex items-center mb-4">
                                        <svg class="w-8 h-8 text-red-200 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <h3 class="text-2xl font-bold">Ready to dive deeper?</h3>
                                    </div>
                                    <p class="text-red-100 mb-8 leading-relaxed text-lg">
                                        Now that you've created your first Laravel application, you might be wondering what to learn next.
                                        First, we strongly recommend becoming familiar with how Laravel works by reading the following documentation.
                                    </p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <button class="flex items-center justify-center space-x-3 px-6 py-4 bg-white text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <span>Read the Documentation</span>
                                        </button>
                                        <button class="flex items-center justify-center space-x-3 px-6 py-4 bg-transparent text-white font-semibold border-2 border-white/30 rounded-lg hover:border-white hover:bg-white/10 transition-all duration-200 backdrop-blur-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a2.5 2.5 0 110 5H9V6h3a2.5 2.5 0 110 5H9.5m-1 4v.5a2 2 0 002 2h2a2 2 0 002-2v-.5"></path>
                                            </svg>
                                            <span>Watch Laracasts</span>
                                        </button>
                                    </div>
                                    <div class="mt-6 flex items-center text-red-100 text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Join over 50,000 developers already using Laravel in production</span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </template>

                    <!-- Navigation Footer -->
                    <footer class="border-t border-gray-700 pt-8 mt-16">
                        <div class="flex justify-between items-center">
                            <button class="flex items-center space-x-2 px-4 py-2 text-gray-400 bg-gray-900 border border-gray-700 rounded-lg hover:border-gray-600 hover:text-gray-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <span>Release Notes</span>
                            </button>
                            <button class="flex items-center space-x-2 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                <span>Configuration</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </footer>
                </article>
            </div>
        </main>

        <!-- Right Sidebar - Table of Contents -->
        <aside class="docs-toc bg-black border-l border-gray-800">
            <div class="px-6 py-8">
                <div class="sticky top-8">
                    <h3 class="text-xs font-medium text-gray-400 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h7"></path>
                        </svg>
                        On this page
                    </h3>
                    <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                        <div class="border-l border-gray-700">
                            <ul class="space-y-1">
                                <template x-for="item in tocItems" :key="item.id">
                                    <li>
                                        <a :href="'#' + item.id"
                                           class="toc-link inline-block border-l-[3px] border-transparent pl-4 py-1.5 text-[13px] text-gray-400 hover:text-gray-200 hover:border-gray-600 transition-all duration-200"
                                           :class="{ 'active border-laravel-red text-white': activeSection === item.id }"
                                           x-text="item.title"
                                           @click="activeSection = item.id">
                                        </a>
                                        <template x-if="item.children">
                                            <ul class="mt-1 space-y-1">
                                                <template x-for="child in item.children" :key="child.id">
                                                    <li>
                                                        <a :href="'#' + child.id"
                                                           class="toc-link inline-block border-l-[3px] border-transparent pl-8 py-1 text-[12px] text-gray-500 hover:text-gray-300 hover:border-gray-600 transition-all duration-200"
                                                           :class="{ 'active border-laravel-red text-gray-200': activeSection === child.id }"
                                                           x-text="child.title"
                                                           @click="activeSection = child.id">
                                                        </a>
                                                    </li>
                                                </template>
                                            </ul>
                                        </template>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Publicité Laravel Forge -->
                        <div class="mt-10">
                            <div class="relative hover:-translate-y-1 transition-transform duration-300">
                                <a href="https://forge.laravel.com" target="_blank" rel="noopener noreferrer"
                                   class="block relative overflow-hidden rounded-lg p-5 h-48 bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 text-white">
                                    <div class="absolute inset-0 bg-black/10"></div>
                                    <div class="relative z-10 h-full flex flex-col justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                                </svg>
                                            </div>
                                            <span class="font-bold text-lg">Laravel Forge</span>
                                        </div>
                                        <div>
                                            <p class="text-sm text-emerald-100 mb-4 leading-relaxed">
                                                Server management made simple for any PHP app
                                            </p>
                                            <div class="flex items-center text-white text-sm font-medium">
                                                <span>Learn more</span>
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
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
    </div>

    <!-- Scripts -->
    <script>
        // Copy to clipboard functionality
        function copyToClipboard(event) {
            const codeBlock = event.target.closest('.bg-gray-900').querySelector('code');
            const text = codeBlock.textContent;

            navigator.clipboard.writeText(text).then(() => {
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;
                button.innerHTML = `
                    <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-400">Copied!</span>
                `;
                setTimeout(() => {
                    button.innerHTML = originalContent;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        // Smooth scrolling for anchor links
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href^="#"]');
            if (link) {
                e.preventDefault();
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                        inline: 'nearest'
                    });
                }
            }
        });

        // Enhanced scroll spy for table of contents
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Update active section in Alpine.js
                        const sectionId = entry.target.id;
                        if (sectionId) {
                            // Dispatch custom event to update Alpine.js state
                            window.dispatchEvent(new CustomEvent('section-visible', {
                                detail: { sectionId }
                            }));
                        }
                    }
                });
            }, {
                rootMargin: '-20% 0px -70% 0px',
                threshold: 0.1
            });

            // Observe all sections
            document.querySelectorAll('section[id]').forEach(section => {
                observer.observe(section);
            });
        });

        // Listen for section visibility events
        window.addEventListener('section-visible', function(e) {
            // This will be handled by Alpine.js intersection observer
        });

        // Auto-update table of contents based on scroll position
        let ticking = false;
        function updateActiveSection() {
            const sections = document.querySelectorAll('section[id]');
            const scrollPosition = window.scrollY + 100;

            let currentSection = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;

                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    currentSection = section.id;
                }
            });

            if (currentSection) {
                // Update Alpine.js state
                Alpine.store('activeSection', currentSection);
            }
        }

        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    updateActiveSection();
                    ticking = false;
                });
                ticking = true;
            }
        });
    </script>
</body>
</html>

Merci
