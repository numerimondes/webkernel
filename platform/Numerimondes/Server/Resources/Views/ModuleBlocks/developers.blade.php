<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Développeurs - Programme Junior to Freelance | Numerimondes</title>
    <meta name="description" content="Transformez votre carrière de développeur en 3 mois. Programme complet Laravel + Business pour générer 3000+ DH/mois.">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        },
                        accent: {
                            500: '#f97316',
                            600: '#ea580c'
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/90 backdrop-blur-md border-b border-gray-200 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">N</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Numerimondes</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="index" class="text-gray-700 hover:text-primary-600">Accueil</a>
                    <a href="marketplace" class="text-gray-700 hover:text-primary-600">Marketplace</a>
                    <a href="#" class="text-accent-600 font-medium">Développeurs</a>
                    <a href="#" class="text-gray-700 hover:text-primary-600">Entreprises</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Se connecter</a>
                    <a href="#" class="bg-accent-600 text-white px-4 py-2 rounded-lg hover:bg-accent-700">Candidater</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-accent-50 via-white to-primary-50 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center mb-16">
                <div class="mb-6">
                    <span class="bg-accent-100 text-accent-800 px-4 py-2 rounded-full text-sm font-medium">
                        🚀 Programme "Junior to Freelance"
                    </span>
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in-up">
                    "Même sans beaucoup d'expérience,<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-600 to-primary-600">vous pouvez gagner votre vie</span><br>
                    en freelance"
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-4xl mx-auto leading-relaxed">
                    Transformez des développeurs juniors en freelances autonomes capables de générer des revenus décents
                    en utilisant l'écosystème Numerimondes et ses outils révolutionnaires.
                </p>

                <!-- Success Stats -->
                <div class="grid md:grid-cols-3 gap-8 max-w-3xl mx-auto mb-12">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-accent-600 mb-2">80%</div>
                        <div class="text-gray-600">Taux de réussite</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600 mb-2">3 mois</div>
                        <div class="text-gray-600">Pour devenir autonome</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 mb-2">3000+ DH</div>
                        <div class="text-gray-600">Revenu mensuel moyen</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#" class="bg-accent-600 text-white px-8 py-4 rounded-lg hover:bg-accent-700 transition-all duration-200 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Candidater maintenant
                    </a>
                    <a href="#program" class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-lg hover:border-accent-600 hover:text-accent-600 transition-colors font-semibold text-lg">
                        Découvrir le programme
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Overview -->
    <section id="program" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                    Parcours "Junior to Freelance" - 3 mois
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Un programme révolutionnaire qui vous accompagne de développeur débutant à freelance autonome
                    avec des outils et un encadrement d'exception.
                </p>
            </div>

            <!-- Timeline -->
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-gradient-to-b from-accent-400 via-primary-500 to-green-500"></div>

                <!-- Timeline Items -->
                <div class="space-y-16">
                    <!-- Phase 1 -->
                    <div class="flex items-center" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                        <div class="flex-1 text-right pr-8">
                            <div class="bg-accent-50 rounded-2xl p-8 transition-all duration-300" :class="hovered ? 'scale-105 shadow-xl' : 'shadow-lg'">
                                <div class="text-accent-600 font-semibold text-sm mb-2">SEMAINES 1-2</div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Formation de Base</h3>
                                <p class="text-gray-600 mb-4">
                                    HTML5, CSS3, JavaScript ES6+, PHP 8.2+ avec OOP, introduction à Git et Laravel
                                </p>
                                <ul class="text-left space-y-2">
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Setup environnement (Docker, VS Code)
                                    </li>
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Premier contact avec Laravel
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-accent-500 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg z-10 transition-transform duration-300" :class="hovered ? 'scale-110' : ''">
                            1
                        </div>
                        <div class="flex-1 pl-8"></div>
                    </div>

                    <!-- Phase 2 -->
                    <div class="flex items-center" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                        <div class="flex-1 pr-8"></div>
                        <div class="w-16 h-16 bg-primary-500 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg z-10 transition-transform duration-300" :class="hovered ? 'scale-110' : ''">
                            2
                        </div>
                        <div class="flex-1 pl-8">
                            <div class="bg-primary-50 rounded-2xl p-8 transition-all duration-300" :class="hovered ? 'scale-105 shadow-xl' : 'shadow-lg'">
                                <div class="text-primary-600 font-semibold text-sm mb-2">SEMAINES 3-6</div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Laravel & Blade Mastery</h3>
                                <p class="text-gray-600 mb-4">
                                    Architecture MVC, templating Blade, Eloquent ORM, routes et authentification
                                </p>
                                <ul class="space-y-2">
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-primary-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Système de templating Blade
                                    </li>
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-primary-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Templates Numerimondes existants
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 3 -->
                    <div class="flex items-center" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                        <div class="flex-1 text-right pr-8">
                            <div class="bg-blue-50 rounded-2xl p-8 transition-all duration-300" :class="hovered ? 'scale-105 shadow-xl' : 'shadow-lg'">
                                <div class="text-blue-600 font-semibold text-sm mb-2">SEMAINES 7-10</div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Premier Projet Accompagné</h3>
                                <p class="text-gray-600 mb-4">
                                    Projet simple (300-500 DH) avec mentor assigné et support Numerimondes
                                </p>
                                <ul class="text-left space-y-2">
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Développement guidé avec reviews
                                    </li>
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Livraison client avec support
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg z-10 transition-transform duration-300" :class="hovered ? 'scale-110' : ''">
                            3
                        </div>
                        <div class="flex-1 pl-8"></div>
                    </div>

                    <!-- Phase 4 -->
                    <div class="flex items-center" x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false">
                        <div class="flex-1 pr-8"></div>
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg z-10 transition-transform duration-300" :class="hovered ? 'scale-110' : ''">
                            4
                        </div>
                        <div class="flex-1 pl-8">
                            <div class="bg-green-50 rounded-2xl p-8 transition-all duration-300" :class="hovered ? 'scale-105 shadow-xl' : 'shadow-lg'">
                                <div class="text-green-600 font-semibold text-sm mb-2">SEMAINES 11-12</div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">Autonomie Progressive</h3>
                                <p class="text-gray-600 mb-4">
                                    Projets autonomes (800-1200 DH), support communautaire et portfolio
                                </p>
                                <ul class="space-y-2">
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Optimisation workflow personnel
                                    </li>
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Construction portfolio et références
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tools Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                    Outils de Support au Développement
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Une suite complète d'outils pour vous accompagner dans votre montée en compétence et votre réussite professionnelle.
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 mb-16">
                <!-- Template Starter Kits -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">Template Starter Kits Sectoriels</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-6">
                            Templates prêts à l'emploi pour accélérer votre développement et impressionner vos clients.
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-semibold text-gray-900">Kit E-commerce</div>
                                <div class="text-xs text-gray-600">Panier + Paiement + Inventory</div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-semibold text-gray-900">Kit CRM</div>
                                <div class="text-xs text-gray-600">Clients + Pipeline + Reports</div>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-semibold text-gray-900">Kit Blog/Portfolio</div>
                                <div class="text-xs text-gray-600">CMS + Admin Interface</div>
                            </div>
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 text-center">
                                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-semibold text-gray-900">Kit Finance</div>
                                <div class="text-xs text-gray-600">Comptabilité + Facturation</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Developer Pricing Calculator -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-accent-500 to-accent-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">Calculator Pricing Intelligent</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-6">
                            Outil IA pour tarifier vos services de manière optimale selon multiples paramètres.
                        </p>

                        <!-- Mock Calculator -->
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="text-xs font-medium text-gray-700 block mb-1">Niveau</label>
                                    <div class="bg-accent-100 text-accent-800 text-xs px-2 py-1 rounded-full text-center">Junior Certifié</div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-700 block mb-1">Type Projet</label>
                                    <div class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full text-center">CRM Moyen</div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-700 block mb-1">Secteur</label>
                                    <div class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full text-center">Standard</div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-700 block mb-1">Délai</label>
                                    <div class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full text-center">Normal</div>
                                </div>
                            </div>
                            <div class="border-t pt-4 text-center">
                                <div class="text-lg font-bold text-gray-900 mb-1">800 - 1,200 DH</div>
                                <div class="text-sm text-gray-600">+ 20% marge négociation</div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Analyse de marché automatique
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Recommandations personnalisées
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-accent-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Evolution tarifaire trackée
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Tools -->
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Client Pitch Templates -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Client Pitch Templates</h3>
                    <p class="text-gray-600 mb-4">
                        Modèles de propositions commerciales, argumentaires techniques et templates de contrats.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Propositions par type de projet</li>
                        <li>• Arguments business value</li>
                        <li>• Guides de négociation</li>
                    </ul>
                </div>

                <!-- Time Tracking Tools -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Time Tracking Intégré</h3>
                    <p class="text-gray-600 mb-4">
                        Suivi temps automatique avec captures d'écran et génération de rapports client.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Facturation automatisée</li>
                        <li>• Analytics productivité</li>
                        <li>• Rapports transparents</li>
                    </ul>
                </div>

                <!-- Developer Forum -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Forum Communauté</h3>
                    <p class="text-gray-600 mb-4">
                        Communauté active avec système de points, mentoring et live coding sessions.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• 500+ développeurs actifs</li>
                        <li>• Sessions live hebdomadaires</li>
                        <li>• Support pair à pair</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">
                    Ils ont réussi avec Numerimondes
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Découvrez les parcours inspirants de développeurs qui ont transformé leur carrière en 3 mois.
                </p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Success Story 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            AH
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">Ahmed H.</h4>
                            <p class="text-sm text-gray-600">Ex-Junior Dev → Freelance Certifié</p>
                        </div>
                    </div>
                    <blockquote class="text-gray-700 mb-6 italic">
                        "En 3 mois, je suis passé de développeur junior à 300 DH/jour à freelance autonome générant 4500 DH/mois. Les templates et le mentoring ont été déterminants."
                    </blockquote>
                    <div class="flex justify-between items-center text-sm">
                        <div class="text-center">
                            <div class="font-bold text-blue-600">4,500 DH</div>
                            <div class="text-gray-600">Revenu mensuel</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-green-600">8 projets</div>
                            <div class="text-gray-600">Livrés avec succès</div>
                        </div>
                    </div>
                </div>

                <!-- Success Story 2 -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            SB
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">Sara B.</h4>
                            <p class="text-sm text-gray-600">Reconversion → Dev Laravel</p>
                        </div>
                    </div>
                    <blockquote class="text-gray-700 mb-6 italic">
                        "Après une reconversion professionnelle, j'ai trouvé ma voie grâce à Numerimondes. Aujourd'hui je développe des modules pour des banques !"
                    </blockquote>
                    <div class="flex justify-between items-center text-sm">
                        <div class="text-center">
                            <div class="font-bold text-green-600">6,200 DH</div>
                            <div class="text-gray-600">Revenu mensuel</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-blue-600">Bancaire</div>
                            <div class="text-gray-600">Spécialisation</div>
                        </div>
                    </div>
                </div>

                <!-- Success Story 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            OM
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">Omar M.</h4>
                            <p class="text-sm text-gray-600">Étudiant → Freelance Pro</p>
                        </div>
                    </div>
                    <blockquote class="text-gray-700 mb-6 italic">
                        "Encore étudiant, j'ai commencé avec Numerimondes. Maintenant je finance mes études et ai même embauché un autre développeur !"
                    </blockquote>
                    <div class="flex justify-between items-center text-sm">
                        <div class="text-center">
                            <div class="font-bold text-purple-600">8,100 DH</div>
                            <div class="text-gray-600">Revenu mensuel</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-orange-600">Mini-équipe</div>
                            <div class="text-gray-600">1 développeur</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-accent-600 to-primary-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8 relative">
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">
                Prêt à transformer votre carrière ?
            </h2>
            <p class="text-xl text-primary-100 mb-8">
                Rejoignez les 500+ développeurs qui ont déjà fait le pari Numerimondes.
                Places limitées pour maintenir la qualité de l'accompagnement.
            </p>

            <!-- Urgency Banner -->
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 mb-8 border border-white/20">
                <div class="flex items-center justify-center space-x-4 text-white">
                    <svg class="w-5 h-5 text-accent-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold">Prochaine session : 15 places restantes</span>
                    <div class="text-accent-300 text-sm">Début : 1er Mars 2025</div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#" class="bg-white text-accent-600 px-8 py-4 rounded-lg hover:bg-gray-50 transition-colors duration-200 font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Candidater maintenant
                </a>
                <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-white/10 transition-colors duration-200 font-bold text-lg">
                    Télécharger la brochure
                </a>
            </div>

            <div class="mt-8 text-primary-100 text-sm">
                ✅ Satisfaction garantie ou remboursé<br>
                ✅ Accompagnement personnalisé<br>
                ✅ Accès vie communauté développeurs
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">N</span>
                        </div>
                        <span class="text-xl font-bold">Numerimondes</span>
                    </div>
                    <p class="text-gray-400 mb-6">
                        L'écosystème qui transforme les développeurs juniors en freelances prospères.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Formation</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white">Programme complet</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Candidater</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Success stories</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Outils</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white">Templates</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Pricing Calculator</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Forum</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Documentation</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Communauté</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white">Discord</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Live Coding</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Mentors</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Événements</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">© 2025 Numerimondes. Programme de formation développeurs - Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <style>
        .bg-grid-pattern {
            background-image:
                linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</body>
</html>
