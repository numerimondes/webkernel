<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\I18n\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Language Seeder
 *
 * Seeds default languages and basic translations for the I18n system.
 * Safe for production - uses upsert operations to avoid duplicates.
 */
class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedLanguages();
        $this->seedTranslationCategories();
        $this->seedBasicTranslations();

        $this->command->info('Languages and basic translations seeded successfully.');
    }

    /**
     * Seed default languages
     */
    protected function seedLanguages(): void
    {
        $languages = [
            [
                'code' => 'en',
                'iso' => 'en-US',
                'label' => 'English',
                'native_label' => 'English',
                'direction' => 'ltr',
                'active' => true,
                'is_default' => true,
                'tenant_id' => 1,
                'metadata' => json_encode([
                    'country' => 'US',
                    'currency' => 'USD',
                    'date_format' => 'MM/DD/YYYY',
                    'time_format' => '12h',
                ]),
            ],
            [
                'code' => 'fr',
                'iso' => 'fr-FR',
                'label' => 'French',
                'native_label' => 'Français',
                'direction' => 'ltr',
                'active' => true,
                'is_default' => false,
                'tenant_id' => 1,
                'metadata' => json_encode([
                    'country' => 'FR',
                    'currency' => 'EUR',
                    'date_format' => 'DD/MM/YYYY',
                    'time_format' => '24h',
                ]),
            ],
            [
                'code' => 'ar',
                'iso' => 'ar-MA',
                'label' => 'Arabic',
                'native_label' => 'العربية',
                'direction' => 'rtl',
                'active' => true,
                'is_default' => false,
                'tenant_id' => 1,
                'metadata' => json_encode([
                    'country' => 'MA',
                    'currency' => 'MAD',
                    'date_format' => 'DD/MM/YYYY',
                    'time_format' => '24h',
                ]),
            ],
            [
                'code' => 'es',
                'iso' => 'es-ES',
                'label' => 'Spanish',
                'native_label' => 'Español',
                'direction' => 'ltr',
                'active' => true,
                'is_default' => false,
                'tenant_id' => 1,
                'metadata' => json_encode([
                    'country' => 'ES',
                    'currency' => 'EUR',
                    'date_format' => 'DD/MM/YYYY',
                    'time_format' => '24h',
                ]),
            ],
            [
                'code' => 'de',
                'iso' => 'de-DE',
                'label' => 'German',
                'native_label' => 'Deutsch',
                'direction' => 'ltr',
                'active' => true,
                'is_default' => false,
                'tenant_id' => 1,
                'metadata' => json_encode([
                    'country' => 'DE',
                    'currency' => 'EUR',
                    'date_format' => 'DD.MM.YYYY',
                    'time_format' => '24h',
                ]),
            ],
        ];

        $now = Carbon::now();

        foreach ($languages as $language) {
            $language['created_at'] = $now;
            $language['updated_at'] = $now;

            DB::table(APTITUDE_DB_PREFIX . 'languages')->updateOrInsert(
                [
                    'code' => $language['code'],
                    'tenant_id' => $language['tenant_id'],
                ],
                $language
            );
        }

        $this->command->info('Seeded ' . count($languages) . ' languages.');
    }

    /**
     * Seed translation categories
     */
    protected function seedTranslationCategories(): void
    {
        $categories = [
            [
                'name' => 'Authentication',
                'slug' => 'auth',
                'description' => 'Login, registration, and authentication messages',
                'app' => 'core',
                'module' => null,
                'is_system' => true,
            ],
            [
                'name' => 'Validation',
                'slug' => 'validation',
                'description' => 'Form validation error messages',
                'app' => 'core',
                'module' => null,
                'is_system' => true,
            ],
            [
                'name' => 'User Interface',
                'slug' => 'ui',
                'description' => 'General UI elements and navigation',
                'app' => 'core',
                'module' => null,
                'is_system' => false,
            ],
            [
                'name' => 'Website Content',
                'slug' => 'content',
                'description' => 'Website builder page and component content',
                'app' => 'core',
                'module' => 'website-builder',
                'is_system' => false,
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'ecommerce',
                'description' => 'Shopping cart, products, and checkout',
                'app' => 'core',
                'module' => 'ecommerce',
                'is_system' => false,
            ],
            [
                'name' => 'System Messages',
                'slug' => 'system',
                'description' => 'System notifications and alerts',
                'app' => 'core',
                'module' => null,
                'is_system' => true,
            ],
        ];

        $now = Carbon::now();

        foreach ($categories as $category) {
            $category['tenant_id'] = 1;
            $category['created_at'] = $now;
            $category['updated_at'] = $now;

            DB::table(APTITUDE_DB_PREFIX . 'translation_categories')->updateOrInsert(
                [
                    'slug' => $category['slug'],
                    'tenant_id' => $category['tenant_id'],
                ],
                $category
            );
        }

        $this->command->info('Seeded ' . count($categories) . ' translation categories.');
    }

    /**
     * Seed basic translations
     */
    protected function seedBasicTranslations(): void
    {
        $languages = DB::table(APTITUDE_DB_PREFIX . 'languages')
            ->where('tenant_id', 1)
            ->pluck('id', 'code');

        $authCategoryId = DB::table(APTITUDE_DB_PREFIX . 'translation_categories')
            ->where('slug', 'auth')
            ->where('tenant_id', 1)
            ->value('id');

        $uiCategoryId = DB::table(APTITUDE_DB_PREFIX . 'translation_categories')
            ->where('slug', 'ui')
            ->where('tenant_id', 1)
            ->value('id');

        $contentCategoryId = DB::table(APTITUDE_DB_PREFIX . 'translation_categories')
            ->where('slug', 'content')
            ->where('tenant_id', 1)
            ->value('id');

        $translations = [
            // Authentication translations
            'auth.login' => [
                'en' => 'Login',
                'fr' => 'Connexion',
                'ar' => 'تسجيل الدخول',
                'es' => 'Iniciar sesión',
                'de' => 'Anmelden',
            ],
            'auth.register' => [
                'en' => 'Register',
                'fr' => 'S\'inscrire',
                'ar' => 'التسجيل',
                'es' => 'Registrarse',
                'de' => 'Registrieren',
            ],
            'auth.logout' => [
                'en' => 'Logout',
                'fr' => 'Déconnexion',
                'ar' => 'تسجيل الخروج',
                'es' => 'Cerrar sesión',
                'de' => 'Abmelden',
            ],
            'auth.email' => [
                'en' => 'Email',
                'fr' => 'E-mail',
                'ar' => 'البريد الإلكتروني',
                'es' => 'Correo electrónico',
                'de' => 'E-Mail',
            ],
            'auth.password' => [
                'en' => 'Password',
                'fr' => 'Mot de passe',
                'ar' => 'كلمة المرور',
                'es' => 'Contraseña',
                'de' => 'Passwort',
            ],

            // UI translations
            'ui.welcome' => [
                'en' => 'Welcome',
                'fr' => 'Bienvenue',
                'ar' => 'مرحبا',
                'es' => 'Bienvenido',
                'de' => 'Willkommen',
            ],
            'ui.home' => [
                'en' => 'Home',
                'fr' => 'Accueil',
                'ar' => 'الرئيسية',
                'es' => 'Inicio',
                'de' => 'Startseite',
            ],
            'ui.about' => [
                'en' => 'About',
                'fr' => 'À propos',
                'ar' => 'حول',
                'es' => 'Acerca de',
                'de' => 'Über uns',
            ],
            'ui.contact' => [
                'en' => 'Contact',
                'fr' => 'Contact',
                'ar' => 'اتصل بنا',
                'es' => 'Contacto',
                'de' => 'Kontakt',
            ],
            'ui.save' => [
                'en' => 'Save',
                'fr' => 'Enregistrer',
                'ar' => 'حفظ',
                'es' => 'Guardar',
                'de' => 'Speichern',
            ],
            'ui.cancel' => [
                'en' => 'Cancel',
                'fr' => 'Annuler',
                'ar' => 'إلغاء',
                'es' => 'Cancelar',
                'de' => 'Abbrechen',
            ],
            'ui.delete' => [
                'en' => 'Delete',
                'fr' => 'Supprimer',
                'ar' => 'حذف',
                'es' => 'Eliminar',
                'de' => 'Löschen',
            ],
            'ui.edit' => [
                'en' => 'Edit',
                'fr' => 'Modifier',
                'ar' => 'تعديل',
                'es' => 'Editar',
                'de' => 'Bearbeiten',
            ],
            'ui.search' => [
                'en' => 'Search',
                'fr' => 'Rechercher',
                'ar' => 'بحث',
                'es' => 'Buscar',
                'de' => 'Suchen',
            ],

            // Website builder content
            'content.hero_title' => [
                'en' => 'Welcome to Our Amazing Website',
                'fr' => 'Bienvenue sur notre site extraordinaire',
                'ar' => 'مرحبا بكم في موقعنا الرائع',
                'es' => 'Bienvenido a nuestro increíble sitio web',
                'de' => 'Willkommen auf unserer fantastischen Website',
            ],
            'content.hero_subtitle' => [
                'en' => 'Discover amazing features and services',
                'fr' => 'Découvrez des fonctionnalités et services extraordinaires',
                'ar' => 'اكتشف المميزات والخدمات الرائعة',
                'es' => 'Descubre características y servicios increíbles',
                'de' => 'Entdecken Sie erstaunliche Funktionen und Dienstleistungen',
            ],
            'content.learn_more' => [
                'en' => 'Learn More',
                'fr' => 'En savoir plus',
                'ar' => 'اعرف أكثر',
                'es' => 'Saber más',
                'de' => 'Mehr erfahren',
            ],
            'content.get_started' => [
                'en' => 'Get Started',
                'fr' => 'Commencer',
                'ar' => 'ابدأ',
                'es' => 'Empezar',
                'de' => 'Loslegen',
            ],

            // Brand/Company
            'brand.numerimondes' => [
                'en' => 'Numerimondes',
                'fr' => 'Numerimondes',
                'ar' => 'Numerimondes',
                'es' => 'Numerimondes',
                'de' => 'Numerimondes',
            ],
        ];

        $now = Carbon::now();
        $insertData = [];

        foreach ($translations as $reference => $values) {
            foreach ($values as $langCode => $value) {
                if (!isset($languages[$langCode])) {
                    continue;
                }

                // Determine category
                $categoryId = null;
                if (str_starts_with($reference, 'auth.')) {
                    $categoryId = $authCategoryId;
                } elseif (str_starts_with($reference, 'ui.')) {
                    $categoryId = $uiCategoryId;
                } elseif (str_starts_with($reference, 'content.')) {
                    $categoryId = $contentCategoryId;
                }

                $insertData[] = [
                    'language_id' => $languages[$langCode],
                    'tenant_id' => 1,
                    'reference' => $reference,
                    'value' => $value,
                    'app' => 'core',
                    'theme' => 'default',
                    'module' => str_starts_with($reference, 'content.') ? 'website-builder' : null,
                    'category_id' => $categoryId,
                    'content_type' => str_starts_with($reference, 'content.') ? 'html' : 'text',
                    'is_system' => str_starts_with($reference, 'auth.'),
                    'needs_review' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($insertData, 100);
        foreach ($chunks as $chunk) {
            DB::table(APTITUDE_DB_PREFIX . 'translations')->insertOrIgnore($chunk);
        }

        $this->command->info('Seeded ' . count($insertData) . ' basic translations.');
    }

    /**
     * Seed advanced website builder content (optional)
     */
    protected function seedWebsiteBuilderContent(): void
    {
        $languages = DB::table(APTITUDE_DB_PREFIX . 'languages')
            ->where('tenant_id', 1)
            ->pluck('id', 'code');

        $contentCategoryId = DB::table(APTITUDE_DB_PREFIX . 'translation_categories')
            ->where('slug', 'content')
            ->where('tenant_id', 1)
            ->value('id');

        $websiteContent = [
            // Page content
            'page.home.meta_title' => [
                'en' => 'Home - Your Amazing Website',
                'fr' => 'Accueil - Votre site extraordinaire',
                'ar' => 'الرئيسية - موقعكم الرائع',
                'es' => 'Inicio - Tu increíble sitio web',
                'de' => 'Startseite - Ihre fantastische Website',
            ],
            'page.home.meta_description' => [
                'en' => 'Discover our amazing products and services. Join thousands of satisfied customers.',
                'fr' => 'Découvrez nos produits et services extraordinaires. Rejoignez des milliers de clients satisfaits.',
                'ar' => 'اكتشف منتجاتنا وخدماتنا الرائعة. انضم إلى آلاف العملاء الراضين.',
                'es' => 'Descubre nuestros increíbles productos y servicios. Únete a miles de clientes satisfechos.',
                'de' => 'Entdecken Sie unsere erstaunlichen Produkte und Dienstleistungen. Schließen Sie sich Tausenden zufriedener Kunden an.',
            ],

            // Component content
            'component.footer.copyright' => [
                'en' => '© {year} {company}. All rights reserved.',
                'fr' => '© {year} {company}. Tous droits réservés.',
                'ar' => '© {year} {company}. جميع الحقوق محفوظة.',
                'es' => '© {year} {company}. Todos los derechos reservados.',
                'de' => '© {year} {company}. Alle Rechte vorbehalten.',
            ],
            'component.newsletter.title' => [
                'en' => 'Subscribe to Our Newsletter',
                'fr' => 'Abonnez-vous à notre newsletter',
                'ar' => 'اشترك في نشرتنا الإخبارية',
                'es' => 'Suscríbete a nuestro boletín',
                'de' => 'Abonnieren Sie unseren Newsletter',
            ],
            'component.newsletter.description' => [
                'en' => 'Stay updated with our latest news and offers.',
                'fr' => 'Restez informé de nos dernières actualités et offres.',
                'ar' => 'ابق على اطلاع بآخر أخبارنا وعروضنا.',
                'es' => 'Mantente actualizado con nuestras últimas noticias y ofertas.',
                'de' => 'Bleiben Sie über unsere neuesten Nachrichten und Angebote auf dem Laufenden.',
            ],

            // Form labels
            'form.name' => [
                'en' => 'Name',
                'fr' => 'Nom',
                'ar' => 'الاسم',
                'es' => 'Nombre',
                'de' => 'Name',
            ],
            'form.email' => [
                'en' => 'Email Address',
                'fr' => 'Adresse e-mail',
                'ar' => 'عنوان البريد الإلكتروني',
                'es' => 'Dirección de correo electrónico',
                'de' => 'E-Mail-Adresse',
            ],
            'form.message' => [
                'en' => 'Message',
                'fr' => 'Message',
                'ar' => 'الرسالة',
                'es' => 'Mensaje',
                'de' => 'Nachricht',
            ],
            'form.submit' => [
                'en' => 'Send Message',
                'fr' => 'Envoyer le message',
                'ar' => 'إرسال الرسالة',
                'es' => 'Enviar mensaje',
                'de' => 'Nachricht senden',
            ],

            // Status messages
            'status.success' => [
                'en' => 'Success! Your message has been sent.',
                'fr' => 'Succès ! Votre message a été envoyé.',
                'ar' => 'نجح! تم إرسال رسالتك.',
                'es' => '¡Éxito! Tu mensaje ha sido enviado.',
                'de' => 'Erfolg! Ihre Nachricht wurde gesendet.',
            ],
            'status.error' => [
                'en' => 'Error: Please try again later.',
                'fr' => 'Erreur : Veuillez réessayer plus tard.',
                'ar' => 'خطأ: يرجى المحاولة مرة أخرى لاحقاً.',
                'es' => 'Error: Por favor, inténtalo de nuevo más tarde.',
                'de' => 'Fehler: Bitte versuchen Sie es später erneut.',
            ],
        ];

        $now = Carbon::now();
        $insertData = [];

        foreach ($websiteContent as $reference => $values) {
            foreach ($values as $langCode => $value) {
                if (!isset($languages[$langCode])) {
                    continue;
                }

                $insertData[] = [
                    'language_id' => $languages[$langCode],
                    'tenant_id' => 1,
                    'reference' => $reference,
                    'value' => $value,
                    'app' => 'core',
                    'theme' => 'default',
                    'module' => 'website-builder',
                    'category_id' => $contentCategoryId,
                    'content_type' => str_contains($value, '<') ? 'html' : 'text',
                    'is_system' => false,
                    'needs_review' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert in chunks
        $chunks = array_chunk($insertData, 100);
        foreach ($chunks as $chunk) {
            DB::table(APTITUDE_DB_PREFIX . 'translations')->insertOrIgnore($chunk);
        }

        $this->command->info('Seeded ' . count($insertData) . ' website builder translations.');
    }
}
