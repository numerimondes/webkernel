
{{-- 
    resources/views/builder/blocks/footer.blade.php
    @meta name: Footer
    @meta icon: 🔗
    @meta category: Navigation
    @meta preview: Footer with links and company info
    @meta description: Complete footer section with links, contact info, and social media
--}}

@php
$blockId = $blockId ?? 'footer-default';
$title = $title ?? 'Mon Entreprise';
$description = $description ?? 'Votre partenaire de confiance';
$alignment = $alignment ?? 'left';
$variant = $variant ?? 'dark';
@endphp

<footer class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            {{-- Company Info --}}
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <x-filament::icon icon="heroicon-o-building-office" class="w-8 h-8 text-primary-400" />
                    <h3 class="text-2xl font-bold">{{ $title }}</h3>
                </div>
                <p class="text-gray-300 mb-6 text-lg">
                    {{ $description }}
                </p>
                <div class="flex space-x-4">
                    <x-filament::button color="gray" variant="ghost" size="sm">
                        <x-filament::icon icon="heroicon-o-envelope" class="w-4 h-4 mr-1" />
                        Email
                    </x-filament::button>
                    <x-filament::button color="gray" variant="ghost" size="sm">
                        <x-filament::icon icon="heroicon-o-phone" class="w-4 h-4 mr-1" />
                        Téléphone
                    </x-filament::button>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-lg font-semibold mb-4">Liens rapides</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Accueil</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Services</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">À propos</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h4 class="text-lg font-semibold mb-4">Légal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Mentions légales</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Politique de confidentialité</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">CGU</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm">
                © {{ date('Y') }} {{ $title }}. Tous droits réservés.
            </p>
            <div class="flex space-x-4 mt-4 md:mt-0">
                <span class="text-gray-400 text-sm">Créé avec Website Builder Pro</span>
            </div>
        </div>
    </div>
</footer>