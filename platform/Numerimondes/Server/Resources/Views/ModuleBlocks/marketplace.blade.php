<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Numerimondes</title>
    <meta name="description" content="Découvrez notre catalogue de logiciels B2B premium. Solutions éprouvées par les leaders du marché marocain.">

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
                    <a href="/" class="text-gray-700 hover:text-primary-600">Accueil</a>
                    <a href="#" class="text-primary-600 font-medium">Marketplace</a>
                    <a href="#" class="text-gray-700 hover:text-primary-600">Développeurs</a>
                    <a href="#" class="text-gray-700 hover:text-primary-600">Entreprises</a>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Se connecter</a>
                    <a href="#" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Publier</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Catalogue Premium B2B
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Solutions éprouvées par Attijariwafa Bank, OCP Group, Maroc Telecom et les leaders du marché marocain.
                </p>
            </div>

            <!-- Search and Filters -->
            <div class="max-w-4xl mx-auto" x-data="{
                searchQuery: '',
                selectedCategory: 'all',
                selectedIndustry: 'all',
                priceRange: 'all'
            }">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-12">
                    <!-- Search Bar -->
                    <div class="relative mb-6">
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Rechercher un logiciel, module ou fonctionnalité..."
                            class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg"
                        >
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Filters -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <select x-model="selectedCategory" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500">
                            <option value="all">Toutes catégories</option>
                            <option value="crm">CRM</option>
                            <option value="erp">ERP</option>
                            <option value="ecommerce">E-commerce</option>
                            <option value="finance">Finance</option>
                            <option value="hr">Ressources Humaines</option>
                        </select>

                        <select x-model="selectedIndustry" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500">
                            <option value="all">Tous secteurs</option>
                            <option value="banking">Banque</option>
                            <option value="insurance">Assurance</option>
                            <option value="transport">Transport</option>
                            <option value="retail">Commerce</option>
                            <option value="healthcare">Santé</option>
                        </select>

                        <select x-model="priceRange" class="border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500">
                            <option value="all">Tous budgets</option>
                            <option value="0-50k">0 - 50 000 DH</option>
                            <option value="50k-200k">50 000 - 200 000 DH</option>
                            <option value="200k+">200 000 DH+</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Software Catalog -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Featured Software -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">🏆 Logiciels Vedettes</h2>
                    <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Voir tous →</a>
                </div>

                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Featured Software 1 -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                        <div class="relative">
                            <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H7m2 0v-5a2 2 0 012-2h2a2 2 0 012 2v5"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 bg-accent-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                Exclusif
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">CRM</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">4.9</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                                CRM Bank Pro Maroc
                            </h3>
                            <p class="text-gray-600 mb-4">
                                CRM spécialisé secteur bancaire avec conformité BAM, scoring client et workflow approbation crédit automatisé.
                            </p>

                            <!-- Features -->
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Conformité Bank Al-Maghrib
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Interface mobile advisors
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Reporting réglementaire intégré
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">850 000 DH</div>
                                    <div class="text-sm text-gray-500">Licence perpétuelle</div>
                                </div>
                                <a href="#" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Software 2 -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                        <div class="relative">
                            <div class="h-48 bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center">
                                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                Populaire
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">E-commerce</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">4.7</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                                MarocShop Pro
                            </h3>
                            <p class="text-gray-600 mb-4">
                                Plateforme e-commerce complète avec paiement local, livraison Maroc, facturation DGI et multi-vendeurs.
                            </p>

                            <div class="space-y-2 mb-6">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Paiement CMI, PayPal, Virement
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Intégration Amana, CTM, DHL
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Facturation conforme DGI
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">320 000 DH</div>
                                    <div class="text-sm text-gray-500">Licence + Setup</div>
                                </div>
                                <a href="#" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Software 3 -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                        <div class="relative">
                            <div class="h-48 bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 bg-purple-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                Nouveau
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">ERP</span>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">4.8</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
                                IndustriePro ERP
                            </h3>
                            <p class="text-gray-600 mb-4">
                                ERP industriel avec gestion production, stocks, qualité et maintenance prédictive par IA.
                            </p>

                            <div class="space-y-2 mb-6">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    IA maintenance prédictive
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Contrôle qualité ISO 9001
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Planification production MRP
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">1 200 000 DH</div>
                                    <div class="text-sm text-gray-500">Licence enterprise</div>
                                </div>
                                <a href="#" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Software Catalog -->
            <div class="mb-16">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Catalogue Complet</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">42 logiciels disponibles</span>
                        <select class="border border-gray-300 rounded-lg px-3 py-2">
                            <option>Plus récents</option>
                            <option>Plus populaires</option>
                            <option>Prix croissant</option>
                            <option>Prix décroissant</option>
                        </select>
                    </div>
                </div>

                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Software Item 1 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex">
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Finance</span>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    4.6 (23 avis)
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">ComptaMaroc Pro</h3>
                            <p class="text-gray-600 text-sm mb-3">
                                Comptabilité conforme Plan Comptable Général Marocain avec déclarations automatisées DGI.
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900">180 000 DH</span>
                                    <span class="text-sm text-gray-500">HT</span>
                                </div>
                                <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Voir détails →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Software Item 2 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex">
                        <div class="w-32 h-32 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Assurance</span>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    4.5 (18 avis)
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">AssuranceCore</h3>
                            <p class="text-gray-600 text-sm mb-3">
                                Plateforme gestion complète assurance avec conformité ACAPS et calculs actuariels certifiés.
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900">950 000 DH</span>
                                    <span class="text-sm text-gray-500">HT</span>
                                </div>
                                <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Voir détails →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Software Item 3 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex">
                        <div class="w-32 h-32 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H7m2 0v-5a2 2 0 012-2h2a2 2 0 012 2v5"></path>
                            </svg>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Immobilier</span>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    4.7 (31 avis)
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">PropertyManager Pro</h3>
                            <p class="text-gray-600 text-sm mb-3">
                                Gestion immobilière complète avec syndic, location, vente et interface notaires.
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900">420 000 DH</span>
                                    <span class="text-sm text-gray-500">HT</span>
                                </div>
                                <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Voir détails →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Software Item 4 -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex">
                        <div class="w-32 h-32 bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Santé</span>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    4.8 (42 avis)
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">MediCabinet Pro</h3>
                            <p class="text-gray-600 text-sm mb-3">
                                Dossier médical électronique avec télémédecine, ordonnances et conformité CNOM.
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900">280 000 DH</span>
                                    <span class="text-sm text-gray-500">HT</span>
                                </div>
                                <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm">
                                    Voir détails →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Load More -->
                <div class="text-center mt-12">
                    <a href="#" class="inline-flex items-center bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Charger plus de logiciels
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-accent-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white mb-6">
                Vous développez un logiciel B2B ?
            </h2>
            <p class="text-xl text-primary-100 mb-8">
                Rejoignez notre écosystème et monétisez votre expertise auprès des entreprises marocaines.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#" class="bg-white text-primary-600 px-8 py-4 rounded-lg hover:bg-gray-50 transition-colors font-bold text-lg">
                    Publier mon logiciel
                </a>
                <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-white/10 transition-colors font-bold text-lg">
                    Devenir développeur tiers
                </a>
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
                        L'écosystème modulaire qui révolutionne le développement logiciel B2B au Maroc.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Marketplace</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white">Catalogue</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Nouveautés</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Top ventes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Promotions</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Développeurs</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white">Publier</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Forum</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Formation</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Support</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white">Aide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Statut</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">CGU</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">© 2025 Numerimondes. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
