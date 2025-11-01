<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Charter Bateaux - EnjoyySXM.com</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-caribbean {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #38bdf8 100%);
        }
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header avec navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="/images/enjoysxm-logo.png" alt="EnjoyySXM" class="h-12">
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition duration-300">Accueil</a>
                    <a href="#" class="text-blue-600 font-medium">Charter</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition duration-300">Excursions</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 transition duration-300">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">FR</span>
                    <span class="text-gray-400">|</span>
                    <span class="text-sm text-gray-600">EN</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-caribbean relative overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-20">
            <div class="text-center text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 text-shadow">
                    <x-lucide-anchor class="w-16 h-16 mx-auto mb-4" />
                    Envie d'évasion ?
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-shadow">
                    Vivez une journée inoubliable en mer !
                </p>
                <div class="flex items-center justify-center mb-6">
                    <x-lucide-users class="w-6 h-6 mr-2" />
                    <span class="text-lg">(de 8 à 12 pax)</span>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" class="w-full h-12 fill-gray-50">
                <path d="M0,64L1440,32L1440,120L0,120Z"></path>
            </svg>
        </div>
    </div>

    <!-- Description Section -->
    <div class="py-16">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center mb-6">
                    <x-lucide-waves class="w-8 h-8 text-blue-600 mr-3" />
                    <h2 class="text-3xl font-bold text-gray-800">Une Aventure Marine Unique</h2>
                </div>
                <p class="text-lg text-gray-600 leading-relaxed mb-8">
                    Partez à l'aventure avec nos sorties d'observation des baleines et rapprochez-vous des dauphins ! À bord, vous disposerez d'un hydrophone pour écouter les chants des baleines, une expérience unique.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg">
                    <p class="text-blue-800 font-medium text-lg">
                        <x-lucide-star class="w-5 h-5 inline mr-2" />
                        Ne manquez pas cette occasion de naviguer dans des eaux cristallines et de découvrir la beauté de notre région ! Réservez dès maintenant votre charter et laissez-vous emporter par la magie de la mer.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Boats Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">
                    <x-lucide-ship class="w-8 h-8 inline mr-3 text-blue-600" />
                    Nos Bateaux
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">

                <!-- Bateau Axopar 29 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover-scale">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                        <h3 class="text-xl font-bold mb-2">
                            <x-lucide-zap class="w-5 h-5 inline mr-2" />
                            Bateau moteur Axopar 29
                        </h3>
                        <p class="text-blue-100">
                            <x-lucide-users class="w-4 h-4 inline mr-1" />
                            Capacité : 8 personnes
                        </p>
                        <p class="text-blue-100 text-sm">
                            <x-lucide-map-pin class="w-4 h-4 inline mr-1" />
                            Départ : Anse Marcel
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Saint-Martin Full Day</span>
                                <span class="font-bold text-blue-600">1 190 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">4 Hrs</span>
                                <span class="font-bold text-blue-600">790 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Sunset 3 Hrs</span>
                                <span class="font-bold text-blue-600">550 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Anguilla Full Day (+35€ pp)</span>
                                <span class="font-bold text-blue-600">1 290 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Anguilla 4 Hrs (+35€ pp)</span>
                                <span class="font-bold text-blue-600">850 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">St Barthélemy Full Day</span>
                                <span class="font-bold text-blue-600">1 390 €</span>
                            </div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="text-green-800 text-sm font-medium">
                                <x-lucide-check class="w-4 h-4 inline mr-1" />
                                Inclus : Boissons, snorkeling, canoë, paddle
                            </p>
                        </div>
                        <button class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition duration-300">
                            <x-lucide-calendar class="w-4 h-4 inline mr-2" />
                            Réserver maintenant
                        </button>
                    </div>
                </div>

                <!-- Catamaran Lagoon 38 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover-scale">
                    <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white p-6">
                        <h3 class="text-xl font-bold mb-2">
                            <x-lucide-sailboat class="w-5 h-5 inline mr-2" />
                            Catamaran Lagoon 38
                        </h3>
                        <p class="text-teal-100">
                            <x-lucide-users class="w-4 h-4 inline mr-1" />
                            Capacité : 12 personnes
                        </p>
                        <p class="text-teal-100 text-sm">
                            <x-lucide-map-pin class="w-4 h-4 inline mr-1" />
                            Départ : Orient Bay
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Saint-Martin Full Day</span>
                                <span class="font-bold text-teal-600">1 090 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">4 Hrs</span>
                                <span class="font-bold text-teal-600">690 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Sunset 3 Hrs</span>
                                <span class="font-bold text-teal-600">550 €</span>
                            </div>
                            <div class="flex justify-between items-center border-2 border-orange-200 bg-orange-50 p-2 rounded">
                                <span class="text-gray-600 flex items-center">
                                    <x-lucide-eye class="w-4 h-4 mr-1 text-orange-600" />
                                    Observation baleines (hiver)
                                </span>
                                <span class="font-bold text-orange-600">1 240 €</span>
                            </div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="text-green-800 text-sm font-medium">
                                <x-lucide-check class="w-4 h-4 inline mr-1" />
                                Inclus : Boissons, snorkeling, canoë, paddle
                            </p>
                        </div>
                        <button class="w-full mt-4 bg-teal-600 hover:bg-teal-700 text-white py-3 px-6 rounded-lg font-medium transition duration-300">
                            <x-lucide-calendar class="w-4 h-4 inline mr-2" />
                            Réserver maintenant
                        </button>
                    </div>
                </div>

                <!-- Zodiac Pro 7 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover-scale">
                    <div class="bg-gradient-to-r from-orange-600 to-orange-700 text-white p-6">
                        <h3 class="text-xl font-bold mb-2">
                            <x-lucide-life-buoy class="w-5 h-5 inline mr-2" />
                            Zodiac Pro 7
                        </h3>
                        <p class="text-orange-100">
                            <x-lucide-users class="w-4 h-4 inline mr-1" />
                            8 pers. (tour) / 12 pers. (location)
                        </p>
                        <p class="text-orange-100 text-sm">
                            <x-lucide-map-pin class="w-4 h-4 inline mr-1" />
                            Départ : Anse Marcel
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Saint-Martin Full Day</span>
                                <span class="font-bold text-orange-600">990 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">4 Hrs</span>
                                <span class="font-bold text-orange-600">590 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Sunset 3 Hrs</span>
                                <span class="font-bold text-orange-600">450 €</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Anguilla Full Day (+35€ pp)</span>
                                <span class="font-bold text-orange-600">1 040 €</span>
                            </div>
                            <hr class="my-3">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-700 font-medium text-sm mb-2">
                                    <x-lucide-key class="w-4 h-4 inline mr-1" />
                                    Location (sans skipper) :
                                </p>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">1 jour</span>
                                    <span class="font-bold text-gray-700">550 €</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm">4 Hrs</span>
                                    <span class="font-bold text-gray-700">390 €</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="text-green-800 text-sm font-medium">
                                <x-lucide-check class="w-4 h-4 inline mr-1" />
                                Inclus : Boissons, snorkeling, canoë, paddle
                            </p>
                        </div>
                        <button class="w-full mt-4 bg-orange-600 hover:bg-orange-700 text-white py-3 px-6 rounded-lg font-medium transition duration-300">
                            <x-lucide-calendar class="w-4 h-4 inline mr-2" />
                            Réserver maintenant
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- English Section -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center mb-6">
                    <img src="/images/uk-flag.png" alt="English" class="w-6 h-6 mr-3">
                    <h2 class="text-3xl font-bold text-gray-800">Want to escape? Live an unforgettable day at sea!</h2>
                </div>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Go on an adventure with our whale watching outings and get closer to the dolphins! On board, you will have a hydrophone to listen to the songs of whales, a unique experience.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg mt-8 max-w-4xl mx-auto">
                    <p class="text-blue-800 font-medium text-lg">
                        <x-lucide-star class="w-5 h-5 inline mr-2" />
                        Don't miss this opportunity to sail in crystal clear waters and discover the beauty of our region! Book now your charter and let yourself be carried away by the magic of the sea.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-caribbean py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="text-white">
                <h2 class="text-3xl font-bold mb-6 text-shadow">
                    <x-lucide-phone class="w-8 h-8 inline mr-3" />
                    Prêt pour l'aventure ?
                </h2>
                <p class="text-xl mb-8 text-shadow">
                    Contactez-nous dès maintenant pour réserver votre excursion en mer
                </p>
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <button class="bg-white text-blue-600 hover:bg-gray-100 py-4 px-8 rounded-lg font-bold text-lg transition duration-300">
                        <x-lucide-phone class="w-5 h-5 inline mr-2" />
                        Appeler maintenant
                    </button>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white py-4 px-8 rounded-lg font-bold text-lg transition duration-300">
                        <x-lucide-message-circle class="w-5 h-5 inline mr-2" />
                        WhatsApp
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white py-4 px-8 rounded-lg font-bold text-lg transition duration-300">
                        <x-lucide-mail class="w-5 h-5 inline mr-2" />
                        Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">EnjoyySXM</h3>
                    <p class="text-gray-300">
                        Votre spécialiste des excursions en mer à Saint-Martin
                    </p>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">Nos Services</h4>
                    <ul class="text-gray-300 space-y-2">
                        <li>Charter privé</li>
                        <li>Observation baleines</li>
                        <li>Snorkeling</li>
                        <li>Location sans skipper</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">Destinations</h4>
                    <ul class="text-gray-300 space-y-2">
                        <li>Saint-Martin</li>
                        <li>Anguilla</li>
                        <li>Saint-Barthélemy</li>
                        <li>Îles environnantes</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-md font-semibold mb-4">Contact</h4>
                    <div class="text-gray-300 space-y-2">
                        <p class="flex items-center">
                            <x-lucide-phone class="w-4 h-4 mr-2" />
                            +590 690 XX XX XX
                        </p>
                        <p class="flex items-center">
                            <x-lucide-mail class="w-4 h-4 mr-2" />
                            info@enjoysxm.com
                        </p>
                        <p class="flex items-center">
                            <x-lucide-map-pin class="w-4 h-4 mr-2" />
                            Saint-Martin, Antilles françaises
                        </p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">© 2024 EnjoyySXM.com - Tous droits réservés</p>
            </div>
        </div>
    </footer>

</body>
</html>
