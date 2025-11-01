<?php declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migration to seed initial data for Enjoy The World module.
 * This provides base service types and example data.
 */
return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    if (app()->runningInConsole()) {
      // Check if data already exists
      $existingServiceTypes = DB::table('enjoy_tw_service_types')->count();
      $existingProviders = DB::table('enjoy_tw_providers')->count();

      if ($existingServiceTypes > 0 || $existingProviders > 0) {
        $this->info('Existing data detected in Enjoy The World tables.');
        $this->info("Service Types: {$existingServiceTypes}");
        $this->info("Providers: {$existingProviders}");

        $action = $this->ask('What would you like to do? [skip/clear/update]', 'skip');

        switch (strtolower($action)) {
          case 'clear':
            $this->clearExistingData();
            $this->info('Existing data cleared successfully.');
            break;
          case 'update':
            $this->info('Will update existing records and skip duplicates.');
            break;
          case 'skip':
          default:
            $this->info('Skipping Enjoy The World seeding.');
            return;
        }
      }

      $shouldSeed = $this->ask('Do you want to seed Enjoy The World module with initial data?', 'yes');

      if (!in_array(strtolower($shouldSeed), ['yes', 'y', 'oui', 'o'])) {
        $this->info('Skipping Enjoy The World seeding.');
        return;
      }

      $defaultLanguages = $this->ask('Which languages to seed? (comma-separated, e.g., en,fr,es)', 'en,fr');
      $languages = array_map('trim', explode(',', $defaultLanguages));

      $seedExamples = $this->ask('Do you want to seed example providers and services?', 'yes');
      $shouldSeedExamples = in_array(strtolower($seedExamples), ['yes', 'y', 'oui', 'o']);

      $this->info('Seeding Enjoy The World module...');
      $this->seedServiceTypes($languages);
      $this->info('Service types seeded successfully.');

      if ($shouldSeedExamples) {
        $this->seedProvidersAndServices($languages);
        $this->info('Example providers and services seeded successfully.');
      }
    } else {
      $this->seedServiceTypes(['en', 'fr']);
      $this->seedProvidersAndServices(['en', 'fr']);
    }
  }

  /**
   * Clear existing data from tables.
   *
   * @return void
   */
  private function clearExistingData(): void
  {
    DB::table('enjoy_tw_media')->delete();
    DB::table('enjoy_tw_service_field_values')->delete();
    DB::table('enjoy_tw_service_translations')->delete();
    DB::table('enjoy_tw_services')->delete();
    DB::table('enjoy_tw_providers')->delete();
    DB::table('enjoy_tw_service_fields')->delete();
    DB::table('enjoy_tw_service_type_translations')->delete();
    DB::table('enjoy_tw_service_types')->delete();
  }

  /**
   * Seed service types with translations.
   *
   * @param array $languages
   * @return void
   */
  private function seedServiceTypes(array $languages): void
  {
    $serviceTypes = [
      [
        'slug' => 'nautical',
        'icon' => 'fa-anchor',
        'translations' => [
          'en' => ['name' => 'Nautical Activities', 'description' => 'Water sports and maritime experiences'],
          'fr' => ['name' => 'Activites Nautiques', 'description' => 'Sports nautiques et experiences maritimes'],
          'es' => ['name' => 'Actividades Nauticas', 'description' => 'Deportes acuaticos y experiencias maritimas'],
        ],
        'fields' => [
          ['field_key' => 'boat_type', 'field_label' => 'Boat Type', 'field_type' => 'select', 'is_required' => true],
          [
            'field_key' => 'max_passengers',
            'field_label' => 'Max Passengers',
            'field_type' => 'number',
            'is_required' => true,
          ],
          [
            'field_key' => 'equipment_included',
            'field_label' => 'Equipment Included',
            'field_type' => 'textarea',
            'is_required' => false,
          ],
        ],
      ],
      [
        'slug' => 'wellness',
        'icon' => 'fa-spa',
        'translations' => [
          'en' => ['name' => 'Wellness & Spa', 'description' => 'Relaxation and wellness treatments'],
          'fr' => ['name' => 'Bien-etre & Spa', 'description' => 'Detente et soins de bien-etre'],
          'es' => ['name' => 'Bienestar & Spa', 'description' => 'Relajacion y tratamientos de bienestar'],
        ],
        'fields' => [
          [
            'field_key' => 'treatment_type',
            'field_label' => 'Treatment Type',
            'field_type' => 'select',
            'is_required' => true,
          ],
          [
            'field_key' => 'session_duration',
            'field_label' => 'Session Duration',
            'field_type' => 'text',
            'is_required' => true,
          ],
          [
            'field_key' => 'special_requirements',
            'field_label' => 'Special Requirements',
            'field_type' => 'textarea',
            'is_required' => false,
          ],
        ],
      ],
      [
        'slug' => 'adventure',
        'icon' => 'fa-hiking',
        'translations' => [
          'en' => ['name' => 'Adventure Sports', 'description' => 'Thrilling outdoor activities and adventures'],
          'fr' => ['name' => 'Sports d\'Aventure', 'description' => 'Activites de plein air palpitantes et aventures'],
          'es' => [
            'name' => 'Deportes de Aventura',
            'description' => 'Actividades al aire libre emocionantes y aventuras',
          ],
        ],
        'fields' => [
          [
            'field_key' => 'difficulty_level',
            'field_label' => 'Difficulty Level',
            'field_type' => 'select',
            'is_required' => true,
          ],
          [
            'field_key' => 'minimum_age',
            'field_label' => 'Minimum Age',
            'field_type' => 'number',
            'is_required' => true,
          ],
          [
            'field_key' => 'safety_equipment',
            'field_label' => 'Safety Equipment Provided',
            'field_type' => 'textarea',
            'is_required' => false,
          ],
        ],
      ],
      [
        'slug' => 'gastronomy',
        'icon' => 'fa-utensils',
        'translations' => [
          'en' => ['name' => 'Gastronomy', 'description' => 'Culinary experiences and food tours'],
          'fr' => ['name' => 'Gastronomie', 'description' => 'Experiences culinaires et tours gastronomiques'],
          'es' => ['name' => 'Gastronomia', 'description' => 'Experiencias culinarias y tours gastronomicos'],
        ],
        'fields' => [
          [
            'field_key' => 'cuisine_type',
            'field_label' => 'Cuisine Type',
            'field_type' => 'select',
            'is_required' => true,
          ],
          [
            'field_key' => 'dietary_options',
            'field_label' => 'Dietary Options',
            'field_type' => 'textarea',
            'is_required' => false,
          ],
          [
            'field_key' => 'group_size',
            'field_label' => 'Max Group Size',
            'field_type' => 'number',
            'is_required' => true,
          ],
        ],
      ],
      [
        'slug' => 'culture',
        'icon' => 'fa-landmark',
        'translations' => [
          'en' => ['name' => 'Culture & Heritage', 'description' => 'Cultural tours and historical experiences'],
          'fr' => ['name' => 'Culture & Patrimoine', 'description' => 'Tours culturels et experiences historiques'],
          'es' => ['name' => 'Cultura & Patrimonio', 'description' => 'Tours culturales y experiencias historicas'],
        ],
        'fields' => [
          ['field_key' => 'tour_type', 'field_label' => 'Tour Type', 'field_type' => 'select', 'is_required' => true],
          [
            'field_key' => 'languages_available',
            'field_label' => 'Languages Available',
            'field_type' => 'text',
            'is_required' => true,
          ],
          [
            'field_key' => 'accessibility',
            'field_label' => 'Accessibility Information',
            'field_type' => 'textarea',
            'is_required' => false,
          ],
        ],
      ],
    ];

    foreach ($serviceTypes as $serviceTypeData) {
      // Check if service type already exists
      $existingServiceType = DB::table('enjoy_tw_service_types')->where('slug', $serviceTypeData['slug'])->first();

      if ($existingServiceType) {
        $this->info("Skipping existing service type: {$serviceTypeData['slug']}");
        $serviceTypeId = $existingServiceType->id;
      } else {
        $serviceTypeId = DB::table('enjoy_tw_service_types')->insertGetId([
          'slug' => $serviceTypeData['slug'],
          'icon' => $serviceTypeData['icon'],
          'is_active' => true,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
        $this->info("Created service type: {$serviceTypeData['slug']}");
      }

      foreach ($languages as $lang) {
        if (isset($serviceTypeData['translations'][$lang])) {
          $existingTranslation = DB::table('enjoy_tw_service_type_translations')
            ->where('service_type_id', $serviceTypeId)
            ->where('language_code', $lang)
            ->exists();

          if (!$existingTranslation) {
            DB::table('enjoy_tw_service_type_translations')->insert([
              'service_type_id' => $serviceTypeId,
              'language_code' => $lang,
              'name' => $serviceTypeData['translations'][$lang]['name'],
              'description' => $serviceTypeData['translations'][$lang]['description'],
              'created_at' => now(),
              'updated_at' => now(),
            ]);
          }
        }
      }

      foreach ($serviceTypeData['fields'] as $field) {
        $existingField = DB::table('enjoy_tw_service_fields')
          ->where('service_type_id', $serviceTypeId)
          ->where('field_key', $field['field_key'])
          ->exists();

        if (!$existingField) {
          DB::table('enjoy_tw_service_fields')->insert([
            'service_type_id' => $serviceTypeId,
            'field_key' => $field['field_key'],
            'field_label' => $field['field_label'],
            'field_type' => $field['field_type'],
            'is_required' => $field['is_required'],
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }
      }
    }
  }

  /**
   * Seed example providers and services.
   *
   * @param array $languages
   * @return void
   */
  private function seedProvidersAndServices(array $languages): void
  {
    $firstUserId = DB::table('users')->orderBy('id')->value('id');

    if (!$firstUserId) {
      $this->info('No users found in database. Skipping provider and service seeding.');
      return;
    }

    $serviceTypeIds = DB::table('enjoy_tw_service_types')->pluck('id', 'slug');

    $providers = [
      [
        'user_id' => $firstUserId,
        'company_name' => 'Ocean Adventures Co.',
        'phone' => '+212-555-0101',
        'website' => 'https://oceanadventures.example',
        'is_active' => true,
        'services' => [
          [
            'type' => 'nautical',
            'price' => 299.99,
            'duration' => '3 hours',
            'location' => 'Marina Bay',
            'is_featured' => true,
            'translations' => [
              'en' => [
                'title' => 'Sunset Sailing Experience',
                'description' =>
                  'Enjoy a breathtaking sunset cruise along the coast with professional crew and refreshments included.',
              ],
              'fr' => [
                'title' => 'Experience de Voile au Coucher du Soleil',
                'description' =>
                  'Profitez d\'une croisiere au coucher du soleil le long de la cote avec equipage professionnel et rafraichissements inclus.',
              ],
              'es' => [
                'title' => 'Experiencia de Navegacion al Atardecer',
                'description' =>
                  'Disfruta de un crucero al atardecer por la costa con tripulacion profesional y refrescos incluidos.',
              ],
            ],
            'field_values' => [
              'boat_type' => 'Sailing Yacht',
              'max_passengers' => '8',
              'equipment_included' => 'Life jackets, snorkeling gear, refreshments',
            ],
            'media' => [
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=1', 'caption' => 'Sunset sailing'],
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=2', 'caption' => 'Our yacht'],
            ],
          ],
          [
            'type' => 'nautical',
            'price' => 449.99,
            'duration' => '5 hours',
            'location' => 'Coastal Waters',
            'is_featured' => false,
            'translations' => [
              'en' => [
                'title' => 'Deep Sea Fishing Trip',
                'description' =>
                  'Experience the thrill of deep sea fishing with expert guides and all equipment provided.',
              ],
              'fr' => [
                'title' => 'Excursion de Peche en Haute Mer',
                'description' =>
                  'Vivez le frisson de la peche en haute mer avec des guides experts et tout l\'equipement fourni.',
              ],
              'es' => [
                'title' => 'Viaje de Pesca en Alta Mar',
                'description' =>
                  'Experimenta la emocion de la pesca en alta mar con guias expertos y todo el equipo proporcionado.',
              ],
            ],
            'field_values' => [
              'boat_type' => 'Fishing Boat',
              'max_passengers' => '6',
              'equipment_included' => 'Fishing rods, bait, cooler, safety equipment',
            ],
            'media' => [
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=3', 'caption' => 'Fishing boat'],
            ],
          ],
        ],
      ],
      [
        'user_id' => $firstUserId,
        'company_name' => 'Serenity Spa & Wellness',
        'phone' => '+212-555-0202',
        'website' => 'https://serenityspa.example',
        'is_active' => true,
        'services' => [
          [
            'type' => 'wellness',
            'price' => 89.99,
            'duration' => '90 minutes',
            'location' => 'Downtown Spa Center',
            'is_featured' => true,
            'translations' => [
              'en' => [
                'title' => 'Moroccan Hammam Experience',
                'description' => 'Traditional hammam treatment with black soap, rhassoul clay, and relaxing massage.',
              ],
              'fr' => [
                'title' => 'Experience Hammam Marocain',
                'description' => 'Traitement hammam traditionnel avec savon noir, argile rhassoul et massage relaxant.',
              ],
              'es' => [
                'title' => 'Experiencia de Hammam Marroqui',
                'description' =>
                  'Tratamiento de hammam tradicional con jabon negro, arcilla rhassoul y masaje relajante.',
              ],
            ],
            'field_values' => [
              'treatment_type' => 'Hammam & Massage',
              'session_duration' => '90 minutes',
              'special_requirements' => 'Please arrive 15 minutes early. Suitable for all skin types.',
            ],
            'media' => [
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=4', 'caption' => 'Hammam room'],
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=5', 'caption' => 'Relaxation area'],
            ],
          ],
        ],
      ],
      [
        'user_id' => $firstUserId,
        'company_name' => 'Atlas Mountain Guides',
        'phone' => '+212-555-0303',
        'website' => 'https://atlasmountain.example',
        'is_active' => true,
        'services' => [
          [
            'type' => 'adventure',
            'price' => 179.99,
            'duration' => 'Full day',
            'location' => 'Atlas Mountains',
            'is_featured' => true,
            'translations' => [
              'en' => [
                'title' => 'Atlas Mountains Hiking Adventure',
                'description' =>
                  'Guided full-day trek through stunning mountain landscapes with lunch in a Berber village.',
              ],
              'fr' => [
                'title' => 'Aventure de Randonnee dans l\'Atlas',
                'description' =>
                  'Randonnee guidee d\'une journee complete a travers des paysages montagneux avec dejeuner dans un village berbere.',
              ],
              'es' => [
                'title' => 'Aventura de Senderismo en las Montanas del Atlas',
                'description' =>
                  'Caminata guiada de dia completo por impresionantes paisajes montanosos con almuerzo en un pueblo bereber.',
              ],
            ],
            'field_values' => [
              'difficulty_level' => 'Moderate',
              'minimum_age' => '12',
              'safety_equipment' => 'Hiking poles, first aid kit, emergency communication device',
            ],
            'media' => [
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=6', 'caption' => 'Mountain trail'],
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=7', 'caption' => 'Berber village'],
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=8', 'caption' => 'Summit view'],
            ],
          ],
        ],
      ],
      [
        'user_id' => $firstUserId,
        'company_name' => 'Taste of Morocco',
        'phone' => '+212-555-0404',
        'website' => 'https://tasteofmorocco.example',
        'is_active' => true,
        'services' => [
          [
            'type' => 'gastronomy',
            'price' => 125.0,
            'duration' => '4 hours',
            'location' => 'Medina Quarter',
            'is_featured' => true,
            'translations' => [
              'en' => [
                'title' => 'Traditional Moroccan Cooking Class',
                'description' =>
                  'Learn to prepare authentic Moroccan dishes with a local chef, including market tour and meal.',
              ],
              'fr' => [
                'title' => 'Cours de Cuisine Marocaine Traditionnelle',
                'description' =>
                  'Apprenez a preparer des plats marocains authentiques avec un chef local, visite du marche et repas inclus.',
              ],
              'es' => [
                'title' => 'Clase de Cocina Marroqui Tradicional',
                'description' =>
                  'Aprende a preparar platos marroquies autenticos con un chef local, tour del mercado y comida incluida.',
              ],
            ],
            'field_values' => [
              'cuisine_type' => 'Traditional Moroccan',
              'dietary_options' => 'Vegetarian and vegan options available upon request',
              'group_size' => '10',
            ],
            'media' => [
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=9', 'caption' => 'Cooking class'],
              [
                'type' => 'image',
                'url' => 'https://picsum.photos/800/600?random=10',
                'caption' => 'Traditional tagine',
              ],
            ],
          ],
        ],
      ],
      [
        'user_id' => $firstUserId,
        'company_name' => 'Heritage Tours Morocco',
        'phone' => '+212-555-0505',
        'website' => 'https://heritagetours.example',
        'is_active' => true,
        'services' => [
          [
            'type' => 'culture',
            'price' => 65.0,
            'duration' => '3 hours',
            'location' => 'Historic Medina',
            'is_featured' => false,
            'translations' => [
              'en' => [
                'title' => 'Medina Historical Walking Tour',
                'description' =>
                  'Discover the rich history and hidden gems of the ancient medina with an expert local guide.',
              ],
              'fr' => [
                'title' => 'Visite Historique a Pied de la Medina',
                'description' =>
                  'Decouvrez la riche histoire et les tresors caches de l\'ancienne medina avec un guide local expert.',
              ],
              'es' => [
                'title' => 'Tour Historico a Pie por la Medina',
                'description' =>
                  'Descubre la rica historia y gemas ocultas de la antigua medina con un guia local experto.',
              ],
            ],
            'field_values' => [
              'tour_type' => 'Walking Tour',
              'languages_available' => 'English, French, Spanish, Arabic',
              'accessibility' => 'Some areas have steps and narrow passages. Not fully wheelchair accessible.',
            ],
            'media' => [
              ['type' => 'image', 'url' => 'https://picsum.photos/800/600?random=11', 'caption' => 'Historic gate'],
              [
                'type' => 'image',
                'url' => 'https://picsum.photos/800/600?random=12',
                'caption' => 'Traditional architecture',
              ],
            ],
          ],
        ],
      ],
    ];

    foreach ($providers as $providerData) {
      $existingProvider = DB::table('enjoy_tw_providers')
        ->where('company_name', $providerData['company_name'])
        ->first();

      if ($existingProvider) {
        $this->info("Skipping existing provider: {$providerData['company_name']}");
        continue;
      }

      $providerId = DB::table('enjoy_tw_providers')->insertGetId([
        'user_id' => $providerData['user_id'],
        'company_name' => $providerData['company_name'],
        'phone' => $providerData['phone'],
        'website' => $providerData['website'],
        'is_active' => $providerData['is_active'],
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      $this->info("Created provider: {$providerData['company_name']}");

      foreach ($providerData['services'] as $serviceData) {
        $serviceTypeId = $serviceTypeIds[$serviceData['type']];

        $serviceId = DB::table('enjoy_tw_services')->insertGetId([
          'provider_id' => $providerId,
          'service_type_id' => $serviceTypeId,
          'price' => $serviceData['price'],
          'duration' => $serviceData['duration'],
          'location' => $serviceData['location'],
          'is_active' => true,
          'is_featured' => $serviceData['is_featured'],
          'created_at' => now(),
          'updated_at' => now(),
        ]);

        foreach ($languages as $lang) {
          if (isset($serviceData['translations'][$lang])) {
            DB::table('enjoy_tw_service_translations')->insert([
              'service_id' => $serviceId,
              'language_code' => $lang,
              'title' => $serviceData['translations'][$lang]['title'],
              'description' => $serviceData['translations'][$lang]['description'],
              'created_at' => now(),
              'updated_at' => now(),
            ]);
          }
        }

        foreach ($serviceData['field_values'] as $fieldKey => $fieldValue) {
          DB::table('enjoy_tw_service_field_values')->insert([
            'service_id' => $serviceId,
            'field_key' => $fieldKey,
            'value' => $fieldValue,
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }

        foreach ($serviceData['media'] as $media) {
          DB::table('enjoy_tw_media')->insert([
            'service_id' => $serviceId,
            'type' => $media['type'],
            'url' => $media['url'],
            'caption' => $media['caption'],
            'order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
          ]);
        }
      }
    }
  }

  /**
   * Helper method to ask questions in console.
   *
   * @param string $question
   * @param string $default
   * @return string
   */
  private function ask(string $question, string $default = ''): string
  {
    $defaultText = $default ? " [default: {$default}]" : '';
    echo "\n\033[32m{$question}{$defaultText}\033[0m\n> ";

    $handle = fopen('php://stdin', 'r');
    $line = fgets($handle);
    fclose($handle);

    $answer = trim($line);
    return $answer ?: $default;
  }

  /**
   * Helper method to display info messages.
   *
   * @param string $message
   * @return void
   */
  private function info(string $message): void
  {
    echo "\n\033[36m{$message}\033[0m\n";
  }

  /**
   * Reverse the migrations.
   * No down method needed as data deletion is handled by table drops.
   *
   * @return void
   */
  public function down(): void
  {
    // No action needed - if tables are dropped, data is automatically removed
  }
};
