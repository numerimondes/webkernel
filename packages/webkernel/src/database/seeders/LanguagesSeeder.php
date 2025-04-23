<?php

namespace Webkernel\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesSeeder extends Seeder
{
    public function run()
    {
        DB::table('webkernel_lang')->insert([
            [
                'code' => 'en',
                'ISO' => 'en-US',
                'label' => 'English',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'fr',
                'ISO' => 'fr-FR',
                'label' => 'Français',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ar',
                'ISO' => 'ar-MA',
                'label' => 'العربية',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);


        // Insérer les traductions pour la clé "Numerimondes" dans 3 langues
        DB::table('webkernel_lang_words')->insert([
            [
                'lang' => 1, // ID pour le français
                'lang_ref' => 'numerimondes',
                'translation' => 'Numerimondes', // Traduction en français
                'app' => 'core', // Ou l'application associée
                'theme' => 'default', // Thème associé
                'belongs_to' => 1, // Multi-tenant (si c'est nécessaire)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lang' => 2, // ID pour l'anglais
                'lang_ref' => 'numerimondes',
                'translation' => 'Numerimondes', // Traduction en anglais
                'app' => 'core', // Ou l'application associée
                'theme' => 'default', // Thème associé
                'belongs_to' => 1, // Multi-tenant (si c'est nécessaire)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lang' => 3, // ID pour l'espagnol
                'lang_ref' => 'numerimondes',
                'translation' => 'Numerimondes', // Traduction en espagnol
                'app' => 'core', // Ou l'application associée
                'theme' => 'default', // Thème associé
                'belongs_to' => 1, // Multi-tenant (si c'est nécessaire)
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
