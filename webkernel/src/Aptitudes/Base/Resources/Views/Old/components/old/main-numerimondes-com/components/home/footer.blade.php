<style>
    .footer-border {
        position: relative;
        /* important */
    }

    .footer-border::before {
        position: absolute;
        content: '';
        left: 0;
        top: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(to right, #fd004c, #fe9000, #fff020, #3edf4b, #3363ff, #b102b7, #fd004c);
        background-size: 400% 400%;
        animation: footer-border 6s infinite linear alternate;

        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-text-size-adjust: none;
    }

    /* Définition de l'animation */
    @keyframes footer-border {
        0% {
            background-position: 0% 50%;
        }

        100% {
            background-position: 100% 50%;
        }
    }
</style>

<footer id="contact" class="bg-black text-white footer-border">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center mb-6">
                    <div
                        class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white font-bold text-lg">N</span>
                    </div>
                    <span class="text-2xl font-bold">Numérimondes</span>
                </div>
                <p class="text-gray-300 mb-6 max-w-md">
                    Numérimondes révolutionne le développement Laravel avec une architecture modulaire innovante.
                    Créez des applications robustes, évolutives et maintenables en assemblant des modules spécialisés.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-6">Navigation</h3>
                <ul class="space-y-3">
                    <li><a href="#modules"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Modules</a></li>
                    <li><a href="#architecture"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Architecture</a></li>
                    <li><a href="#offers"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Offres</a></li>
                    <li><a href="#roadmap"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Roadmap</a></li>
                    <li><a href="#"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Documentation</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-lg font-semibold mb-6">Support</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Centre
                            d'aide</a></li>
                    <li><a href="#"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Communauté</a></li>
                    <li><a href="#"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Contact</a></li>
                    <li><a href="#"
                            class="text-gray-300 hover:text-white transition-colors duration-200">Status</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">Blog</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-gray-800 rounded-xl p-8 mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">Contactez-nous</h3>
                    <p class="text-gray-300 mb-6">
                        Prêt à transformer votre développement Laravel ? Contactez-nous pour commencer votre projet.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-gray-300">contact@numerimondes.com</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <span class="text-gray-300">+33 1 23 45 67 89</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-300">Paris, France</span>
                        </div>
                    </div>
                </div>
                <div>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" placeholder="Prénom"
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                            <input type="text" placeholder="Nom"
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                        </div>
                        <input type="email" placeholder="Email"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500">
                        <textarea placeholder="Message" rows="4"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"></textarea>
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                            Envoyer le message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    © 2024 Numérimondes. Tous droits réservés.
                </div>
                <div class="flex space-x-6 text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Conditions
                        d'utilisation</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Politique
                        de confidentialité</a>
                    <a href="#"
                        class="text-gray-400 hover:text-white transition-colors duration-200">Cookies</a>
                    <a href="#"
                        class="text-gray-400 hover:text-white transition-colors duration-200">Licences</a>
                </div>
            </div>
        </div>
    </div>
</footer>
