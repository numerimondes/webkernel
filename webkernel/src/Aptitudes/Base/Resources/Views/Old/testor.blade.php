<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organisations et Collectifs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    fontSize: {
                        'xs': '0.6875rem',
                        'xxs': '0.75rem',
                        'xxxs': '0.8125rem',
                    },
                    letterSpacing: {
                        'tight': '-0.025em',
                    }
                }
            }
        }
    </script>
</head>

 <body class="bg-white min-h-screen p-4 sm:p-6 font-inter">
     <div class="max-w-7xl mx-auto">
        @php
            $organizations = [
                [
                    'name' => 'Numerimondes',
                    'type' => 'Organisation',
                    'country' => '🇲🇦 Morocco',
                    'tags' => ['open source', 'nonprofit'],
                    'stats' => [
                        'Collectifs hébergés' => '2633',
                        'Commissions reversées' => '45 MDHS 🏆',
                        'Nombre de développeurs' => '10',
                    ],
                    'description' =>
                        'Fostering a sustainable and innovative open-source ecosystem in Morocco.',
'header_style' => 'bg-gradient-to-tr from-emerald-700 via-teal-800 to-slate-900',
                    'logo' => 'OSC',
                    'logo_color' => 'text-purple-800',
                    'logo_image' =>
                        'https://raw.githubusercontent.com/numerimondes/.github/refs/heads/main/assets/brands/numerimondes/identity/logos/v2/faviconV2_Numerimondes.png',
                    'has_checkmark' => true,
                    'background_image' => null,
                    'background_shapes' => true,
                ],
                 [
                     'name' => 'WebKernel Framework',
                     'type' => 'Plateforme',
                     'country' => '🇲🇦 Morocco',
                     'tags' => ['framework', 'laravel', 'modular'],
                     'stats' => [
                         'Modules disponibles' => '25+',
                         'Développeurs actifs' => '150+',
                         'Projets déployés' => '500+',
                     ],
                     'description' => 'Framework modulaire Laravel avec système d\'extensions utilisateur et architecture scalable pour applications B2B.',
                     'header_style' => 'bg-gradient-to-r from-blue-200 via-cyan-300 to-blue-300',
                     'logo' => 'WK',
                     'logo_color' => 'text-blue-700',
                     'has_checkmark' => true,
                     'background_image' => null,
                     'has_rocket' => true,
                 ],
                 [
                     'name' => 'Junior to Freelance',
                     'type' => 'Programme',
                     'country' => '🇲🇦 Morocco',
                     'tags' => ['formation', 'freelance', 'développement'],
                     'stats' => [
                         'Développeurs formés' => '500+',
                         'Taux de réussite' => '85%',
                         'Revenus moyens' => '3000 DH/mois',
                     ],
                     'description' => 'Programme de formation transformant des développeurs juniors en freelances autonomes en 3 mois avec accompagnement complet.',
                     'header_style' => 'bg-gradient-to-br from-emerald-400 via-teal-500 to-emerald-500',
                     'logo' => 'J2F',
                     'logo_color' => 'text-emerald-800',
                     'has_checkmark' => true,
                     'background_image' => null,
                     'background_shapes' => true,
                 ],
                 [
                     'name' => 'Marketplace Modules',
                     'type' => 'Écosystème',
                     'country' => '🇲🇦 Morocco',
                     'tags' => ['modules', 'extensions', 'marketplace'],
                     'stats' => [
                         'Modules publiés' => '200+',
                         'Développeurs tiers' => '50+',
                         'Taux d\'approbation' => '90%',
                     ],
                     'description' => 'Écosystème de modules et extensions développés par la communauté avec système d\'approbation et de commission automatisé.',
                     'header_style' => 'bg-gradient-to-br from-slate-800 via-blue-900 to-slate-900',
                     'logo' => 'MM',
                     'logo_color' => 'text-slate-200',
                     'has_checkmark' => true,
                     'background_image' => null,
                     'has_photo' => true,
                 ],
                 [
                     'name' => 'Enterprise Solutions',
                     'type' => 'Services',
                     'country' => '🇲🇦 Morocco',
                     'tags' => ['enterprise', 'b2b', 'secteurs'],
                     'stats' => [
                         'Clients enterprise' => '50+',
                         'Secteurs couverts' => '8',
                         'Panier moyen' => '500K DH',
                     ],
                     'description' => 'Solutions sur-mesure pour secteurs à forte complexité métier : banque, assurance, transport avec packages premium.',
                     'header_style' => 'bg-gradient-to-br from-cyan-600 via-teal-700 to-emerald-600',
                     'logo' => 'ES',
                     'logo_color' => 'text-emerald-200',
                     'has_checkmark' => true,
                     'background_image' => null,
                     'has_leaves' => true,
                 ],
                 [
                     'name' => 'Installateur White-Label',
                     'type' => 'Technologie',
                     'country' => '🇲🇦 Morocco',
                     'tags' => ['installateur', 'déploiement', 'automatisation'],
                     'stats' => [
                         'Installations' => '500+',
                         'Temps moyen' => '15 min',
                         'Taux de succès' => '98%',
                     ],
                     'description' => 'Générateur d\'installateurs PHP standalone personnalisables pour déploiement automatisé avec branding client.',
                     'header_style' => 'bg-gradient-to-br from-amber-400 via-yellow-400 to-orange-400',
                     'logo' => 'IWL',
                     'logo_color' => 'text-amber-900',
                     'has_checkmark' => true,
                     'background_image' => null,
                 ],
                 [
                     'name' => 'Developer Forum',
                     'type' => 'Communauté',
                     'country' => '🇲🇦 Morocco',
                     'tags' => ['forum', 'communauté', 'support'],
                     'stats' => [
                         'Membres actifs' => '1000+',
                         'Contributions/mois' => '200+',
                         'Taux de résolution' => '95%',
                     ],
                     'description' => 'Forum communautaire avec gamification, mentorat, templates partagés et système de points pour développeurs.',
                     'header_style' => 'bg-gradient-to-br from-violet-500 via-purple-600 to-violet-700',
                     'logo' => 'DF',
                     'logo_color' => 'text-violet-100',
                     'has_checkmark' => true,
                     'background_image' => null,
                 ],
                 [
                     'name' => 'Solo Billion Vision',
                     'type' => 'Stratégie',
                     'country' => '🇲🇦 Morocco',
                     'tags' => ['vision', 'automatisation', 'scaling'],
                     'stats' => [
                         'Revenus/h cible' => '2000+ DH',
                         'Processus automatisés' => '90%',
                         'Objectif CA 36m' => '25M DH',
                     ],
                     'description' => 'Vision "1-Person Billion Dollar Company" avec automatisation maximale, IA et effet de réseau pour scaling solo.',
                     'header_style' => 'bg-gradient-to-br from-blue-600 via-indigo-700 to-blue-800',
                     'logo' => 'SBV',
                     'logo_color' => 'text-blue-100',
                     'has_checkmark' => true,
                     'background_image' => null,
                     'has_dots' => true,
                 ],
            ];
        @endphp

         <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach ($organizations as $org)
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-shadow duration-300 hover:scale-105 hover:cursor-pointer overflow-hidden border border-gray-100 flex flex-col h-full">
                    <!-- Header with background image or gradient -->
                    <div class="relative h-32 overflow-hidden">
                        @if ($org['background_image'])
                            <!-- Background image with overlay -->
                            <div class="absolute inset-0">
                                <img src="{{ $org['background_image'] }}" alt="Background"
                                    class="w-full h-full object-cover object-center">
                            </div>
                            <div class="absolute inset-0 {{ $org['header_style'] }} mix-blend-multiply opacity-80">
                            </div>
                        @else
                            <!-- Gradient background -->
                            <div class="absolute inset-0 {{ $org['header_style'] }}"></div>
                        @endif

                        <!-- Background decorative elements -->
                        @if (isset($org['background_shapes']) && $org['background_shapes'])
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-4 right-8 w-16 h-16 bg-white rounded-full"></div>
                                <div class="absolute bottom-6 right-4 w-12 h-12 bg-white rounded-full opacity-50"></div>
                                <div class="absolute top-12 left-12 w-8 h-8 bg-white rounded-full opacity-30"></div>
                                <div class="absolute bottom-12 left-20 w-6 h-6 bg-white rounded-full opacity-40"></div>
                            </div>
                        @endif

                        @if (isset($org['has_leaves']) && $org['has_leaves'])
                            <div class="absolute inset-0 opacity-40">
                                <svg class="absolute top-4 right-4 w-6 h-6 text-white" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <svg class="absolute bottom-4 right-8 w-5 h-5 text-white opacity-60" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif

                        @if (isset($org['has_dots']) && $org['has_dots'])
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-8 left-8 w-2 h-2 bg-white rounded-full"></div>
                                <div class="absolute top-12 right-12 w-1 h-1 bg-white rounded-full"></div>
                                <div class="absolute bottom-8 left-16 w-3 h-3 bg-white rounded-full opacity-50"></div>
                                <div class="absolute bottom-12 right-8 w-2 h-2 bg-white rounded-full"></div>
                            </div>
                        @endif

                         <!-- Logo -->
                         <div class="relative z-10 absolute top-6 left-6 w-16 h-16 {{ isset($org['logo_image']) && $org['logo_image'] ? '' : 'bg-white' }} rounded-xl flex items-center justify-center shadow-md">
                             @if (isset($org['logo_image']) && $org['logo_image'])
                                 <img src="{{ $org['logo_image'] }}" alt="{{ $org['name'] }}"
                                     class="w-full h-full object-contain">
                             @else
                                 @php
                                     $words = explode(' ', $org['name']);
                                     $initials = '';
                                     foreach (array_slice($words, 0, 2) as $word) {
                                         $initials .= substr($word, 0, 1);
                                     }
                                     if (strlen($initials) < 2 && isset($words[0])) {
                                         $initials = substr($words[0], 0, 3);
                                     }
                                 @endphp
                                 <span
                                     class="text-2xl font-bold {{ $org['logo_color'] }} tracking-tight leading-none uppercase">{{ $initials }}</span>
                             @endif
                         </div>

                    <!-- Rocket icon for Open Collective -->
                    @if (isset($org['has_rocket']) && $org['has_rocket'])
                        <div class="relative z-10 absolute top-4 right-6">
                            <svg class="w-8 h-8 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif

                    <!-- Photo placeholder for OFi -->
                    @if (isset($org['has_photo']) && $org['has_photo'])
                        <div
                            class="relative z-10 absolute top-6 right-6 w-16 h-16  rounded-xl flex items-center justify-center shadow-md overflow-hidden">
                            <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            <div class="absolute -top-1 -right-1 w-4 h-4  rounded-full"></div>
                            <div class="absolute -bottom-1 -left-1 w-5 h-5  rounded-full"></div>
                            <div class="absolute top-2 left-2 w-3 h-3  rounded-full"></div>
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-5 flex flex-col flex-grow">
                    <!-- Title with checkmark -->
                    <div class="flex items-start gap-2 mb-3">
                        <h3 class="text-base font-semibold leading-tight text-gray-900 flex-1 tracking-tight">
                            {{ $org['name'] }}</h3>
                        @if ($org['has_checkmark'])
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>

                    <!-- Type and Country -->
                    <div class="flex items-center gap-2 mb-3">
                        <span
                            class="text-xs font-medium px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full">{{ $org['type'] }}</span>
                        @if ($org['country'])
                            <span class="text-xxs font-medium text-gray-600">{{ $org['country'] }}</span>
                        @endif
                    </div>

                    <!-- Tags -->
                    @if (!empty($org['tags']))
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach ($org['tags'] as $tag)
                                <span
                                    class="text-xs font-medium px-2 py-0.5 bg-gray-100 text-gray-600 rounded">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Stats collées au dessus de À PROPOS -->
                    <div class="mt-auto">
                        @if (!empty($org['stats']))
                            <div class="space-y-1.5 mb-4">
                                @foreach ($org['stats'] as $label => $value)
                                    <div class="flex justify-between items-baseline">
                                        <span class="text-xxs font-medium text-gray-500">{{ $label }}</span>
                                        <span class="text-xxxs font-semibold text-gray-900">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Description Section -->
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-xxs font-semibold text-gray-500 mb-2 tracking-wider uppercase">À PROPOS DE
                                NOUS</h4>
                            <p class="text-xxxs text-gray-600 leading-relaxed font-normal">{{ $org['description'] }}
                            </p>
                        </div>
                    </div>
                </div>
        </div>
        @endforeach
    </div>
    </div>
</body>

</html>
