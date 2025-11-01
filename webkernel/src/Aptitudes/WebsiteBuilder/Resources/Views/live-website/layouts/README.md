<?php
// Exemple dans un Controller Laravel

class HomeController extends Controller
{
    public function index()
    {
        return view('layouts.app', [
            // === SEO ET META ===
            'pageTitle' => 'Accueil - Mon Super Site Web',
            'pageDescription' => 'Découvrez notre plateforme de création de sites web avec des thèmes dynamiques et un design moderne.',
            'keywords' => 'création site web, thèmes, design, responsive, laravel',
            'author' => 'Mon Entreprise',
            
            // === OPEN GRAPH ===
            'ogTitle' => 'Mon Super Site Web - Créateur de sites professionnels',
            'ogDescription' => 'Créez des sites web magnifiques avec nos outils professionnels',
            'ogImage' => asset('images/og-home.jpg'),
            'ogImageWidth' => 1200,
            'ogImageHeight' => 630,
            'ogImageAlt' => 'Aperçu de notre plateforme de création web',
            
            // === TWITTER CARD ===
            'twitterTitle' => 'Mon Super Site Web',
            'twitterDescription' => 'Créez des sites web magnifiques facilement',
            'twitterImage' => asset('images/twitter-card.jpg'),
            'twitterSite' => '@monentreprise',
            'twitterCreator' => '@createur',
            
            // === ICONS ET FAVICONS ===
            'favicon' => asset('images/favicon.ico'),
            'appleTouchIcon180' => asset('images/icons/apple-touch-icon-180x180.png'),
            'androidIcon192' => asset('images/icons/android-icon-192x192.png'),
            
            // === PWA ===
            'manifest' => asset('manifest.json'),
            'themeColor' => '#3b82f6',
            'enablePWA' => true,
            
            // === THEME ET DESIGN ===
            'defaultTheme' => 'dark',
            'primaryColor' => 'blue',
            'primaryColorValue' => '#3b82f6',
            'primaryColorRgb' => '59, 130, 246',
            
            // === ANALYTICS ===
            'googleAnalyticsId' => 'GA_MEASUREMENT_ID',
            'googleTagManagerId' => 'GTM-XXXXXXX',
            'facebookPixelId' => '1234567890',
            'hotjarId' => 1234567,
            'clarityId' => 'abcdefg',
            
            // === FONCTIONNALITÉS ===
            'enableCookieConsent' => true,
            'enableServiceWorker' => true,
            'enablePerformanceMonitoring' => true,
            
            // === COOKIES CONSENT ===
            'cookieMessage' => 'Ce site utilise des cookies pour améliorer votre expérience.',
            'cookieDismiss' => 'J\'accepte',
            'cookieLink' => 'En savoir plus',
            'cookieHref' => '/politique-confidentialite',
            
            // === CHARGEMENT ===
            'loadingText' => 'Chargement en cours...',
            'loadingDuration' => 1000,
            
            // === NAVIGATION ET FOOTER ===
            'hideNavigation' => false,
            'hideFooter' => false,
            'hideThemeToggle' => false,
            'hideFlashMessages' => false,
            'hideLoader' => false,
            
            // === INFORMATIONS ENTREPRISE ===
            'companyName' => 'Mon Entreprise',
            'companyDescription' => 'Nous créons des expériences web exceptionnelles.',
            
            // === RÉSEAUX SOCIAUX ===
            'socialLinks' => [
                'facebook' => 'https://facebook.com/monentreprise',
                'twitter' => 'https://twitter.com/monentreprise',
                'linkedin' => 'https://linkedin.com/company/monentreprise',
                'github' => 'https://github.com/monentreprise'
            ],
            
            // === LIENS FOOTER ===
            'footerLinks' => [
                'quick' => [
                    ['title' => 'Accueil', 'url' => '/'],
                    ['title' => 'À propos', 'url' => '/about'],
                    ['title' => 'Services', 'url' => '/services'],
                    ['title' => 'Contact', 'url' => '/contact']
                ],
                'resources' => [
                    ['title' => 'Documentation', 'url' => '/docs'],
                    ['title' => 'Blog', 'url' => '/blog'],
                    ['title' => 'FAQ', 'url' => '/faq'],
                    ['title' => 'Support', 'url' => '/support']
                ],
                'legal' => [
                    ['title' => 'Mentions légales', 'url' => '/legal'],
                    ['title' => 'Politique de confidentialité', 'url' => '/privacy'],
                    ['title' => 'CGU', 'url' => '/terms'],
                    ['title' => 'Cookies', 'url' => '/cookies']
                ]
            ],
            
            // === CONTACT ===
            'contactInfo' => [
                'email' => 'contact@monentreprise.com',
                'phone' => '+33 1 23 45 67 89',
                'address' => '123 Rue de la Tech, 75001 Paris, France'
            ],
            
            // === LANGUES ALTERNATIVES ===
            'alternateLanguages' => [
                'fr' => url('/fr'),
                'en' => url('/en'),
                'es' => url('/es')
            ],
            
            // === RSS ===
            'rssFeed' => url('/feed'),
            'rssFeedTitle' => 'Mon Entreprise - Actualités',
            
            // === STRUCTURED DATA (JSON-LD) ===
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'Mon Entreprise',
                'url' => url('/'),
                'logo' => asset('images/logo.png'),
                'description' => 'Créateur de sites web professionnels',
                'address' => [
                    '@type' => 'PostalAddress',
