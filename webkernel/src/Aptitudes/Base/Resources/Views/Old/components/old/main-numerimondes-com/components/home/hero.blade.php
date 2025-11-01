<section class="relative bg-gradient-to-br from-blue-50 via-white to-purple-50 overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
        <div class="absolute top-0 right-1/4 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-1/2 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="text-center">
            <!-- Badge -->
            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mb-8">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Nouveau: Architecture modulaire Laravel
            </div>

            <!-- Main heading -->
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                Développez des applications
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    Laravel modulaires
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                Numérimondes révolutionne le développement Laravel avec une architecture modulaire innovante.
                Créez des applications robustes, évolutives et maintenables en assemblant des modules spécialisés.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                <a href="#modules" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Découvrir les modules
                </a>
                <a href="#demo" class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-lg text-lg font-semibold hover:border-blue-600 hover:text-blue-600 transition-all duration-200">
                    Voir la démo
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">50+</div>
                    <div class="text-gray-600">Modules disponibles</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">1000+</div>
                    <div class="text-gray-600">Développeurs actifs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">99.9%</div>
                    <div class="text-gray-600">Temps de disponibilité</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
        <div class="animate-bounce">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </div>
</section>

<style>
@keyframes pulse {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 0.3; }
}
.animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
