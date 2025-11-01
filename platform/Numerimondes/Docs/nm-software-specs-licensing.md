# Spécifications techniques du système Numerimondes

## Vue d'ensemble

Numerimondes est un gestionnaire de modules pour applications Laravel/FilamentPHP basé sur une architecture serveur maître et applications filles. Le système permet l'installation, la mise à jour et la gestion de modules de manière sécurisée, entièrement en PHP natif, sans recours aux fonctions système dangereuses.

## Architecture générale

Le système repose sur une architecture client-serveur où le serveur maître héberge et distribue les modules, tandis que les applications filles les consomment. Chaque application fille est identifiée de manière unique par son domaine ou sous-domaine et possède un token de licence qui regroupe l'ensemble de ses autorisations. La communication entre le serveur et les clients se fait exclusivement via des API REST sécurisées en HTTPS, avec validation par token Bearer et vérification d'intégrité des données transmises.

## Modules du système

### Architecture modulaire

Le système Numerimondes repose sur quatre modules principaux qui assurent le fonctionnement complet de l'écosystème de gestion de licences et de distribution de modules.

### Module MasterConnector (Serveur maître)

Le module MasterConnector est le cœur du serveur maître. Il gère toutes les opérations côté serveur : génération et validation des tokens de licence, stockage et distribution des modules, vérification des autorisations d'accès et tracking des installations et mises à jour. Ce module est installé uniquement sur le serveur Numerimondes principal et expose l'API REST pour tous les clients.

```php
namespace Platform\Numerimondes\MasterConnector;

class MasterConnectorModule extends WebkernelApp 
{
    public function configureModule(): void
    {
        $this->setModuleConfig(
            $this->module()
                ->id('master-connector')
                ->name('Master Connector')
                ->version('1.0.0')
                ->description('Module serveur pour la gestion centralisée des licences et modules')
        );
    }
}
```

### Module Connector (Applications filles)

Le module Connector est installé sur chaque application fille. Il assure la communication avec le serveur maître, l'authentification via token Bearer, le téléchargement et la vérification des modules, et la synchronisation périodique des licences. C'est le pont entre l'application cliente et le serveur maître.

```php
namespace Webkernel\Aptitudes\Platform\Connector;

class ConnectorModule extends WebkernelApp 
{
    public function configureModule(): void
    {
        $this->setModuleConfig(
            $this->module()
                ->id('connector')
                ->name('Connector')
                ->version('1.0.0')
                ->description('Module client pour la connexion au serveur maître')
                ->requires(['core-platform-licencing'])
        );
    }
}
```

### Module Updator (Applications filles)

Le module Updator gère le cycle de vie des modules installés sur l'application fille. Il vérifie les mises à jour disponibles, télécharge et installe les nouvelles versions, effectue les rollbacks en cas d'erreur et gère l'activation et la désactivation des modules. Il travaille en étroite collaboration avec le module Connector.

```php
namespace Webkernel\Aptitudes\Platform\Updator;

class UpdatorModule extends WebkernelApp 
{
    public function configureModule(): void
    {
        $this->setModuleConfig(
            $this->module()
                ->id('updator')
                ->name('Updator')
                ->version('1.0.0')
                ->description('Module de gestion des mises à jour')
                ->requires(['connector', 'core-platform-licencing'])
        );
    }
}
```

### Module Core (Applications filles)

Le module Core Platform Licencing gère le système de licences local. Il stocke et chiffre les tokens, vérifie la validité des licences, maintient le registre des modules autorisés et expose les méthodes de validation pour les autres modules. C'est la fondation du système de sécurité côté client.

```php
namespace Webkernel\Aptitudes\Platform\Core;

class CoreModule extends WebkernelApp 
{
    public function configureModule(): void
    {
        $this->setModuleConfig(
            $this->module()
                ->id('core-platform-licencing')
                ->name('Core Platform Licencing')
                ->version('1.0.0')
                ->description('Module de base pour la gestion des licences')
        );
    }
}
```

## Structure standardisée d'un module

Chaque module Webkernel suit une structure standardisée qui reproduit une mini-application Laravel complète. Cette architecture garantit la cohérence et facilite le développement.

```
module-name/
├── Console/
│   └── Commands/
├── Database/
│   ├── Migrations/
│   ├── Factories/
│   └── Seeders/
├── Filament/
│   ├── Resources/
│   └── Pages/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Resources/
│   ├── Views/
│   └── Assets/
├── Routes/
│   ├── web.php
│   └── api.php
├── Services/
├── Models/
├── Helpers/
│   └── helpers.php
├── Config/
│   └── module.php
├── Lang/
├── Tests/
│   ├── Feature/
│   └── Unit/
└── module.json
```

Le fichier module.json contient les métadonnées essentielles du module : identifiant unique, version, description, dépendances, version minimale du noyau, hash SHA256 et liste des providers à charger.

## Sécurité et contraintes techniques

Le système doit fonctionner exclusivement avec des fonctions PHP natives, sans recours à exec, shell_exec, system, passthru ou eval. Pour la gestion des fichiers, Laravel Filesystem et Storage seront utilisés systématiquement. Les téléchargements se feront via le client HTTP de Laravel (Guzzle intégré) et la manipulation des archives ZIP utilisera la classe native ZipArchive de PHP.

Toutes les communications API doivent être authentifiées par token Bearer avec validation côté serveur. Chaque module téléchargé doit inclure un hash SHA256 dans son manifeste, vérifié localement avant extraction. Les tokens de licence sont stockés chiffrés en base de données via les méthodes de chiffrement Laravel (Crypt facade). Aucune information sensible ne doit transiter en clair, et toutes les URLs d'API doivent utiliser HTTPS exclusivement.

## Étapes de développement côté serveur (MasterConnector)

### Phase 1 : Infrastructure de base

La première phase consiste à créer la structure du module MasterConnector avec tous ses répertoires. Il faut ensuite créer les migrations pour les tables principales : licenses (id, token, domain, expires_at, metadata), modules (id, name, version, hash, file_path, metadata) et license_modules (license_id, module_id, granted_at). Ces tables constituent le cœur du système de gestion des licences.

### Phase 2 : Modèles et relations

Il faut créer les modèles Eloquent License, Module et LicenseModule avec leurs relations. Le modèle License doit gérer le chiffrement du token et la validation du domaine. Le modèle Module gère les métadonnées et le hash. La relation many-to-many entre License et Module permet de définir quels modules sont autorisés pour quelle licence.

### Phase 3 : Service de génération de tokens

Il faut développer le service TokenGenerator qui crée des tokens sécurisés avec signature HMAC. Ce service génère un token unique, y encode les informations de licence (modules autorisés, domaine, expiration) et crée une signature HMAC pour garantir l'intégrité. Le token final est une chaîne JWT-like contenant payload et signature.

### Phase 4 : Service de validation

Le service TokenValidator vérifie la validité des tokens reçus. Il vérifie la signature HMAC, contrôle l'expiration, valide le domaine demandeur et vérifie que le token n'est pas révoqué. Ce service est appelé par tous les endpoints protégés de l'API.

### Phase 5 : Contrôleur API pour les licences

Le LicenseController expose les endpoints de l'API : POST /api/licenses/validate pour valider un token, GET /api/licenses/modules pour obtenir la liste des modules autorisés et POST /api/licenses/revoke pour révoquer un token. Chaque endpoint retourne des réponses JSON standardisées avec codes HTTP appropriés.

### Phase 6 : Gestionnaire de modules

Le service ModuleManager gère le stockage des modules sur le serveur. Il stocke les fichiers ZIP dans Storage, calcule et enregistre les hash SHA256, crée les métadonnées dans la base de données et gère les versions multiples d'un même module. Ce service est utilisé lors de l'upload de nouveaux modules ou de mises à jour.

### Phase 7 : Contrôleur de distribution

Le DistributionController gère la distribution des modules aux clients. L'endpoint GET /api/modules/download vérifie le token, valide les autorisations, prépare les modules autorisés et renvoie une archive ZIP consolidée avec les métadonnées. L'endpoint GET /api/modules/updates liste les mises à jour disponibles en comparant les versions.

### Phase 8 : Middleware de sécurité

Il faut créer un middleware ValidateTokenMiddleware qui intercepte toutes les requêtes API, extrait le token Bearer, le valide via TokenValidator et attache les informations de licence à la requête. Ce middleware protège tous les endpoints sensibles.

### Phase 9 : Service de packaging

Le service PackageBuilder crée des archives ZIP optimisées contenant les modules. Il agrège plusieurs modules en une seule archive, ajoute un manifeste global avec les hash, optimise la taille de l'archive et signe le package final. Ce service est crucial pour les performances lors des téléchargements.

### Phase 10 : Commandes Artisan

Il faut créer les commandes Artisan pour l'administration : master:create-license pour créer une nouvelle licence, master:revoke-license pour révoquer une licence, master:add-module pour ajouter un module au catalogue et master:assign-module pour assigner un module à une licence. Ces commandes facilitent la gestion quotidienne.

### Phase 11 : Dashboard Filament

Il faut créer les ressources Filament pour l'interface d'administration : LicenseResource pour gérer les licences, ModuleResource pour gérer le catalogue de modules et DownloadResource pour visualiser les statistiques de téléchargement. Ces interfaces permettent une gestion visuelle complète du système.

### Phase 12 : Logging et monitoring

Il faut implémenter un système de logging complet qui trace toutes les opérations : génération de tokens, téléchargements de modules, validations échouées et révocations. Ces logs permettent l'audit et le monitoring du système.

## Étapes de développement côté client (Modules Connector, Updator, Core)

### Phase 1 : Module Core - Infrastructure

La première phase côté client consiste à développer le module Core qui est la fondation. Il faut créer les migrations pour les tables local_licenses (token chiffré, domain, last_sync), installed_modules (module_id, version, status, hash) et module_registry (cache des métadonnées). Ces tables maintiennent l'état local du système de licences.

### Phase 2 : Core - Service de chiffrement

Il faut développer le service EncryptionService qui utilise la facade Crypt de Laravel pour chiffrer et déchiffrer les tokens stockés localement. Ce service assure que même en cas d'accès à la base de données, les tokens restent protégés. Les méthodes encrypt et decrypt sont wrappées avec gestion d'erreurs.

### Phase 3 : Core - Gestionnaire de licences local

Le service LocalLicenseManager gère les licences localement. Il stocke le token chiffré, maintient le registre des modules autorisés, vérifie la validité locale et expose des méthodes pour interroger les autorisations. Ce service est utilisé par tous les autres modules pour vérifier les droits.

### Phase 4 : Module Connector - Client HTTP

Il faut créer le service MasterApiClient qui encapsule toutes les communications avec le serveur maître. Ce client utilise le Http facade de Laravel, ajoute automatiquement le token Bearer, gère les timeouts et retry, vérifie les certificats SSL et parse les réponses JSON. Toutes les requêtes passent par ce client unique.

### Phase 5 : Connector - Service de synchronisation

Le service SyncService gère la synchronisation périodique avec le serveur. Il appelle l'API de validation du token, récupère la liste des modules autorisés, met à jour le registre local et détecte les changements de licence. Ce service est appelé par une tâche planifiée et peut aussi être déclenché manuellement.

### Phase 6 : Connector - Téléchargeur de modules

Le service ModuleDownloader gère le téléchargement des modules depuis le serveur. Il télécharge les archives ZIP via MasterApiClient, vérifie les hash SHA256, stocke temporairement les fichiers et retourne les chemins des fichiers téléchargés. La vérification d'intégrité est systématique avant toute utilisation.

### Phase 7 : Module Updator - Extracteur de modules

Le service ModuleExtractor gère l'extraction des archives ZIP. Il utilise ZipArchive pour décompresser, valide la structure du module extraite, vérifie la présence du fichier module.json et déplace les fichiers vers leur destination finale. Toutes les opérations utilisent Laravel Filesystem pour la sécurité.

### Phase 8 : Updator - Gestionnaire d'installation

Le service InstallationManager orchestre l'installation complète d'un module. Il vérifie les dépendances requises, crée un backup de l'état actuel, extrait le module, exécute les migrations, enregistre le module dans la base de données et nettoie les fichiers temporaires. En cas d'erreur, il effectue un rollback complet.

### Phase 9 : Updator - Gestionnaire de mises à jour

Le service UpdateManager gère les mises à jour de modules existants. Il compare les versions locale et distante, vérifie la compatibilité avec le noyau, télécharge la nouvelle version, crée un backup de l'ancienne version, installe la nouvelle version et effectue un rollback si nécessaire. Le processus est atomique et sûr.

### Phase 10 : Updator - Service de rollback

Le service RollbackService gère les restaurations en cas d'erreur. Il maintient des backups versionnés des modules, restaure les fichiers depuis le backup, reverse les migrations exécutées et met à jour le registre local. Ce service est crucial pour la fiabilité du système.

### Phase 11 : Chargeur dynamique de modules

Il faut créer le service ModuleLoader qui charge dynamiquement les modules au démarrage de l'application. Il lit le registre des modules actifs, charge leurs fichiers module.json, vérifie la compatibilité des versions, enregistre les providers dans le container Laravel et charge les routes et ressources Filament. Ce service s'intègre dans le boot process de Laravel.

### Phase 12 : Commandes Artisan clientes

Il faut créer les commandes Artisan pour les utilisateurs finaux : numerimondes:install pour l'installation initiale, numerimondes:sync pour synchroniser avec le serveur, numerimondes:update pour mettre à jour les modules, numerimondes:list pour lister les modules installés et numerimondes:clean pour nettoyer les modules non autorisés. Ces commandes offrent un contrôle complet en ligne de commande.

### Phase 13 : Tâches planifiées

Il faut configurer les tâches planifiées Laravel pour automatiser les opérations récurrentes : synchronisation quotidienne des licences, vérification hebdomadaire des mises à jour et nettoyage mensuel des backups anciens. Ces tâches maintiennent le système à jour sans intervention manuelle.

### Phase 14 : Interface Filament cliente

Il faut créer des pages Filament pour la gestion visuelle côté client : page de configuration du token de licence, tableau de bord des modules installés, page de vérification des mises à jour disponibles et historique des opérations. Ces interfaces simplifient l'usage pour les utilisateurs non techniques.

## Packages Laravel et dépendances

### Packages Laravel natifs requis

Le système s'appuie sur plusieurs packages Laravel natifs. Laravel HTTP Client (Guzzle) gère toutes les requêtes HTTP vers le serveur maître. Laravel Filesystem abstrait les opérations sur les fichiers de manière sécurisée. Laravel Crypt assure le chiffrement des tokens stockés localement. Laravel Queue permet l'exécution asynchrone des téléchargements et installations. Laravel Schedule gère les tâches planifiées de synchronisation. Laravel Cache optimise les performances en mettant en cache les métadonnées. Laravel Log trace toutes les opérations importantes.

### Packages tiers recommandés

Aucun package tiers n'est strictement nécessaire. Le système peut être développé entièrement avec les outils natifs de Laravel. Cependant, pour améliorer certains aspects, des packages optionnels peuvent être considérés. Spatie Laravel Permission peut gérer les permissions avancées si nécessaire, bien que ce ne soit pas requis initialement. Spatie Laravel Backup peut gérer des backups plus sophistiqués, mais Laravel Filesystem suffit pour les besoins de base. L'objectif est de minimiser les dépendances externes pour maximiser la sécurité et la maintenabilité.

### Extension ZipArchive PHP

La classe ZipArchive est une extension PHP native qui doit être activée sur le serveur. Elle permet la création et l'extraction d'archives ZIP sans dépendance externe. Il faut vérifier que l'extension zip est bien installée et activée dans php.ini. Cette extension est standard dans la plupart des hébergements modernes.

### Vérifications d'environnement

Avant le déploiement, il faut vérifier que plusieurs éléments sont en place. PHP 8.1 ou supérieur est requis pour bénéficier des dernières fonctionnalités de sécurité. L'extension OpenSSL doit être active pour le chiffrement et HTTPS. L'extension ZIP doit être active pour la manipulation des archives. L'extension JSON doit être active pour les échanges API. Laravel 10 ou supérieur est recommandé pour profiter des dernières améliorations. FilamentPHP 3.x est requis pour l'interface d'administration.

## Gestion des métadonnées et module.json

### Structure du fichier module.json

Chaque module contient un fichier module.json à sa racine qui décrit complètement le module. Ce fichier contient l'identifiant unique du module (id), sa version suivant le semantic versioning (version), son nom d'affichage (name), une description détaillée (description), la liste des modules requis (requires), la version minimale du noyau (min_kernel_version), le hash SHA256 du package (hash), la liste des providers à charger (providers) et des métadonnées additionnelles optionnelles (metadata).

Exemple de structure :

```json
{
  "id": "connector",
  "version": "1.0.0",
  "name": "Connector",
  "description": "Module client pour la connexion au serveur maître",
  "requires": ["core-platform-licencing"],
  "min_kernel_version": "2.0.0",
  "hash": "a1b2c3d4e5f6...",
  "providers": [
    "Webkernel\\Aptitudes\\Platform\\Connector\\ConnectorServiceProvider"
  ],
  "metadata": {
    "author": "Numerimondes",
    "license": "proprietary"
  }
}
```

### Validation des métadonnées

Lors du chargement d'un module, le système doit valider la structure du fichier module.json. Il vérifie la présence de tous les champs obligatoires, valide le format de la version (semantic versioning), vérifie que l'ID est unique dans le système, valide que les dépendances existent et sont installées, vérifie la compatibilité de version avec le noyau et valide le hash du package si fourni. Tout module avec un module.json invalide est rejeté et n'est pas chargé.

### Cache des métadonnées

Pour optimiser les performances, les métadonnées de tous les modules sont mises en cache au démarrage de l'application. Le cache est invalidé uniquement lors de l'installation ou la mise à jour d'un module. Cela évite de lire et parser les fichiers JSON à chaque requête.

## Système de versioning et compatibilité

### Semantic Versioning

Tous les modules et le noyau suivent le standard Semantic Versioning (SemVer). Une version est composée de trois nombres : MAJOR.MINOR.PATCH. Une modification MAJOR indique des changements incompatibles avec les versions précédentes. Une modification MINOR ajoute des fonctionnalités de manière rétrocompatible. Une modification PATCH corrige des bugs de manière rétrocompatible.

### Vérification de compatibilité

Avant d'installer ou de mettre à jour un module, le système vérifie la compatibilité de version. Il compare la version du noyau installée avec la version minimale requise par le module. Il vérifie aussi que tous les modules requis sont installés avec une version compatible. Si les conditions ne sont pas remplies, l'installation est refusée avec un message explicite.

### Gestion des dépendances

Chaque module déclare ses dépendances dans le champ requires de son module.json. Le système résout automatiquement l'ordre de chargement des modules en fonction de ces dépendances. Les dépendances circulaires sont détectées et provoquent une erreur. Lors de la désinstallation d'un module, le système avertit si d'autres modules en dépendent.

## API REST du serveur maître

### Endpoints d'authentification

L'endpoint POST /api/auth/validate valide un token de licence. Il reçoit le token et le domaine en paramètres, vérifie la validité et retourne les informations de licence si valide. L'endpoint POST /api/auth/refresh permet de rafraîchir un token proche de l'expiration.

### Endpoints de gestion des modules

L'endpoint GET /api/modules/list liste tous les modules disponibles dans le catalogue. L'endpoint GET /api/modules/download télécharge les modules autorisés pour une licence. Il reçoit le token en header et retourne une archive ZIP. L'endpoint GET /api/modules/updates vérifie les mises à jour disponibles en comparant les versions installées avec les versions du catalogue.

### Endpoints d'administration

L'endpoint POST /api/admin/licenses crée une nouvelle licence (accès admin uniquement). L'endpoint DELETE /api/admin/licenses/{id} révoque une licence. L'endpoint POST /api/admin/licenses/{id}/modules assigne un module à une licence. L'endpoint GET /api/admin/stats retourne les statistiques d'utilisation.

### Format des réponses

Toutes les réponses API suivent un format JSON standardisé. Les réponses de succès contiennent un champ success à true, un champ data avec les données et optionnellement un champ message. Les réponses d'erreur contiennent success à false, un champ error avec le message d'erreur et un champ code avec le code d'erreur spécifique. Les codes HTTP sont utilisés correctement : 200 pour succès, 401 pour non authentifié, 403 pour non autorisé, 404 pour non trouvé, 422 pour validation échouée, 500 pour erreur serveur.

## Sécurité approfondie

### Protection des tokens

Les tokens de licence sont générés avec un secret serveur stocké dans les variables d'environnement. Ils incluent une signature HMAC calculée avec ce secret. Côté client, les tokens sont stockés chiffrés dans la base de données avec la clé d'application Laravel. Les tokens ne sont jamais loggés ou affichés dans les interfaces. Ils ne transitent que via HTTPS et sont automatiquement révoqués en cas de détection d'usage suspect.

### Validation d'intégrité

Chaque module distribué inclut un hash SHA256 dans ses métadonnées. Lors du téléchargement, le client recalcule le hash du fichier reçu et le compare avec le hash attendu. Si les hash ne correspondent pas, le fichier est rejeté et supprimé immédiatement. Cette vérification garantit qu'aucun module n'a été altéré pendant le transfert ou le stockage.

### Rate limiting

Toutes les API sont protégées par rate limiting pour prévenir les abus. Les endpoints publics sont limités à 60 requêtes par heure par IP. Les endpoints authentifiés sont limités à 300 requêtes par heure par token. Les endpoints de téléchargement sont limités à 10 téléchargements par heure par licence. Ces limites sont configurables via les variables d'environnement.

### Audit trail

Toutes les opérations sensibles sont tracées dans une table d'audit : création et révocation de licences, téléchargements de modules, échecs d'authentification et modifications de configuration. Chaque entrée contient un timestamp, l'utilisateur ou le token concerné, l'action effectuée et les détails de l'opération. Ces logs permettent l'analyse forensique en cas d'incident.

## Performance et optimisation

### Cache stratégique

Le système utilise plusieurs niveaux de cache. Les métadonnées des modules sont mises en cache au démarrage de l'application. Les résultats de validation de token sont mis en cache pendant 5 minutes pour éviter des appels API répétés. La liste des modules disponibles est mise en cache côté client pendant 1 heure. Ces caches sont invalidés intelligemment lors des modifications.

### Téléchargements optimisés

Le serveur compresse les archives ZIP avec un niveau d'optimisation élevé pour réduire la bande passante. Les modules sont groupés dans une seule archive pour minimiser le nombre de requêtes HTTP. Le serveur supporte les requêtes range pour permettre la reprise des téléchargements interrompus. Les fichiers statiques sont servis via CDN si disponible.

### Chargement lazy des modules

Les modules ne sont pas tous chargés au démarrage de l'application. Seuls les providers essentiels sont enregistrés initialement. Les routes et ressources Filament sont chargées de manière lazy lors de leur premier accès. Cette approche réduit significativement le temps de démarrage de l'application.

### Optimisation des requêtes base de données

Le système utilise les eager loading pour éviter les problèmes N+1. Les requêtes répétitives sont mises en cache. Les index sont créés sur toutes les colonnes utilisées dans les WHERE et JOIN. Les migrations incluent les index nécessaires dès leur création.

## Tests et qualité

### Tests unitaires requis

Chaque service critique doit avoir une suite de tests unitaires. Pour le serveur, il faut tester TokenGenerator, TokenValidator, ModuleManager et PackageBuilder. Pour le client, il faut tester EncryptionService, LocalLicenseManager, MasterApiClient et ModuleExtractor. Les tests doivent couvrir les cas normaux et les cas d'erreur. Un coverage minimum de 80% est requis.

### Tests d'intégration

Des tests d'intégration doivent valider le flux complet : installation d'une nouvelle instance, synchronisation avec le serveur, téléchargement et installation d'un module, mise à jour d'un module existant et gestion des erreurs avec rollback. Ces tests garantissent que tous les composants fonctionnent ensemble correctement.

### Tests de sécurité

Des tests spécifiques doivent valider la sécurité : tentative d'accès avec token invalide, tentative de téléchargement de module non autorisé, vérification du rejet de fichiers avec hash incorrect et validation de la révocation de token. Ces tests sont critiques pour garantir la robustesse du système.

### Environnements de test

Il faut maintenir trois environnements : développement local pour le travail quotidien, staging qui réplique la production pour les tests finaux et production pour le déploiement réel. Chaque environnement a sa propre base de données et ses propres clés de chiffrement. Les déploiements passent toujours par staging avant la production.

## Documentation et maintenance

### Documentation technique

Une documentation complète doit être maintenue couvrant l'architecture générale du système, les schémas de base de données, les endpoints API avec exemples, la structure des modules et le processus de développement de nouveaux modules. Cette documentation est versionnée avec le code.

### Documentation utilisateur

Des guides utilisateurs doivent expliquer comment installer Numerimondes sur une nouvelle application, comment configurer un token de licence, comment installer et mettre à jour des modules et comment résoudre les problèmes courants. Ces guides incluent des captures d'écran et des exemples concrets.

### Changelog

Chaque version du système et de chaque module doit avoir un changelog détaillé listant les nouvelles fonctionnalités ajoutées, les bugs corrigés, les changements incompatibles et les dépréciations. Le changelog suit le format Keep a Changelog.

### Plan de maintenance

Un plan de maintenance régulière doit être établi : revue mensuelle de sécurité pour appliquer les patches, mise à jour trimestrielle des dépendances, audit semestriel du code pour identifier les points d'amélioration et backup quotidien des bases de données. Ce plan garantit la pérennité du système.

## Migration et déploiement

### Stratégie de déploiement

Le déploiement du serveur maître se fait via des migrations Laravel classiques. Les modules sont déployés via le système lui-même une fois opérationnel. Pour les premières installations clientes, un package bootstrap contenant Core, Connector et Updator est fourni. Ce package s'auto-installe et configure le système complet.

### Gestion des migrations

Chaque module contient ses propres migrations. Lors de l'installation d'un module, ses migrations sont exécutées automatiquement. Lors d'une mise à jour, seules les nouvelles migrations sont exécutées. Un système de versioning des migrations garantit qu'elles ne sont jamais exécutées deux fois. En cas de rollback, les migrations peuvent être inversées automatiquement.

### Configuration des environnements

Les variables d'environnement critiques incluent NUMERIMONDES_MASTER_URL pour l'URL du serveur maître, NUMERIMONDES_TOKEN pour le token de licence, NUMERIMONDES_SECRET pour le secret de chiffrement côté serveur et NUMERIMONDES_ENV pour différencier développement et production. Ces variables sont documentées dans un fichier .env.example.

## Évolution et extensibilité

### Hooks et événements

Le système expose des événements Laravel pour permettre l'extension sans modification du code core. Les événements incluent ModuleInstalled, ModuleUpdated, ModuleDisabled, LicenseSynced et UpdateAvailable. D'autres modules peuvent écouter ces événements pour ajouter des fonctionnalités personnalisées.

### Interfaces et contrats

Tous les services principaux implémentent des interfaces PHP. Cela permet de remplacer les implémentations par défaut par des implémentations personnalisées. Par exemple, TokenGeneratorInterface, TokenValidatorInterface, ModuleInstallerInterface et DownloaderInterface. Les développeurs peuvent créer leurs propres implémentations et les binder dans le service container Laravel.

### Système de plugins

Au-delà des modules standards, le système peut être étendu par des plugins qui modifient le comportement du système lui-même. Les plugins peuvent ajouter des sources de téléchargement alternatives, modifier la logique de validation des licences, ajouter des méthodes de chiffrement personnalisées ou intégrer des systèmes tiers. Les plugins suivent la même structure que les modules mais ont des permissions étendues.

## Processus de développement d'un nouveau module

### Création de la structure

Pour créer un nouveau module, il faut d'abord générer la structure de base avec tous les répertoires nécessaires. Une commande Artisan peut automatiser cette création : php artisan webkernel:make-module NomDuModule. Cette commande crée tous les répertoires listés dans moduleStructure et génère les fichiers de base à partir des stubs.

### Configuration du module

Le fichier ModuleServiceProvider doit être créé en étendant WebkernelApp. La méthode configureModule doit être implémentée pour définir l'ID unique, le nom, la version, la description et les dépendances. Cette configuration est chargée automatiquement lors du boot du module.

### Développement des fonctionnalités

Le développement suit les conventions Laravel standards. Les contrôleurs sont placés dans Http/Controllers, les modèles dans Models, les services dans Services. Les routes web vont dans Routes/web.php et les routes API dans Routes/api.php. Les vues Blade vont dans Resources/Views et les ressources Filament dans Filament/Resources.

### Création du manifeste

Une fois le développement terminé, le fichier module.json doit être créé avec toutes les métadonnées. L'ID doit correspondre exactement à celui configuré dans ModuleServiceProvider. La version doit suivre SemVer. Les dépendances doivent lister tous les modules requis. La version minimale du kernel doit être spécifiée en fonction des fonctionnalités utilisées.

### Tests du module

Avant la distribution, le module doit être testé en isolation. Il faut vérifier que toutes les dépendances sont correctement déclarées, que les migrations s'exécutent sans erreur, que les routes sont accessibles et que les ressources Filament s'affichent correctement. Des tests automatisés doivent être créés dans Tests/Feature et Tests/Unit.

### Packaging et distribution

Une fois les tests passés, le module est packagé en archive ZIP. Le hash SHA256 de l'archive est calculé et ajouté au module.json. L'archive est uploadée sur le serveur maître via l'interface d'administration. Le module est alors disponible pour distribution aux licences autorisées.

## Workflow complet d'installation d'une application fille

### Étape 1 : Installation de Laravel

L'utilisateur commence par créer une nouvelle application Laravel via Composer. Il installe ensuite FilamentPHP selon la documentation officielle. L'application de base est configurée avec la base de données, le .env et les paramètres essentiels.

### Étape 2 : Installation du bootstrap

L'utilisateur télécharge le package bootstrap Numerimondes depuis le site officiel ou via une commande curl. Ce package contient les trois modules essentiels : Core, Connector et Updator. Le package est extrait dans le répertoire de l'application. Les migrations sont exécutées pour créer les tables nécessaires.

### Étape 3 : Configuration du token

L'utilisateur obtient un token de licence depuis son compte sur numerimondes.com. Ce token est ajouté soit via l'interface Filament soit via la commande Artisan numerimondes:configure. Le token est immédiatement chiffré et stocké en base de données. Une première synchronisation est lancée automatiquement.

### Étape 4 : Synchronisation initiale

Le module Connector contacte le serveur maître avec le token. Le serveur valide le token et retourne la liste complète des modules autorisés avec leurs métadonnées. Le module Core met à jour le registre local avec ces informations. L'utilisateur peut maintenant voir les modules disponibles dans l'interface Filament.

### Étape 5 : Installation des modules

L'utilisateur sélectionne les modules qu'il souhaite installer depuis l'interface. Le système vérifie les dépendances et propose d'installer les modules requis automatiquement. Les modules sont téléchargés, leurs hash vérifiés, puis extraits et installés. Les migrations sont exécutées et les modules sont activés. L'application est maintenant opérationnelle avec tous les modules choisis.

### Étape 6 : Utilisation quotidienne

Une tâche planifiée synchronise automatiquement les licences chaque nuit. Les mises à jour sont vérifiées et installées automatiquement si configuré. L'utilisateur peut vérifier manuellement les mises à jour via l'interface Filament. Les modules peuvent être activés ou désactivés à tout moment sans désinstallation.

## Gestion des erreurs et récupération

### Erreurs de téléchargement

Si un téléchargement échoue, le système effectue automatiquement trois tentatives avec délai exponentiel. Si toutes les tentatives échouent, l'erreur est loggée et l'utilisateur est notifié. Le téléchargement peut être relancé manuellement. Les fichiers partiellement téléchargés sont nettoyés automatiquement.

### Erreurs d'installation

Si une installation échoue pendant l'extraction, les fichiers extraits sont supprimés. Si elle échoue pendant les migrations, un rollback des migrations est effectué. Si elle échoue après l'installation, le module est marqué comme défaillant mais les fichiers restent pour investigation. L'utilisateur peut tenter une réinstallation propre via l'interface.

### Corruption de modules

Si un module installé est détecté comme corrompu (hash différent), il est automatiquement désactivé. L'utilisateur est alerté et peut relancer une installation propre. Le système conserve des backups qui peuvent être utilisés pour restaurer rapidement un module fonctionnel.

### Perte de connexion serveur

Si le serveur maître est inaccessible, les opérations locales continuent normalement. Les synchronisations sont reportées et réessayées automatiquement. Les modules déjà installés fonctionnent sans interruption. Un mode dégradé permet de continuer à travailler même sans connexion pendant plusieurs jours.

### Expiration de licence

Si une licence expire, les modules concernés sont automatiquement désactivés lors de la prochaine synchronisation. Les données créées par ces modules restent intactes. L'utilisateur est averti 7 jours avant l'expiration. Une fois la licence renouvelée, les modules sont réactivés automatiquement sans réinstallation.

## Sécurité avancée et prévention des abus

### Protection contre le vol de tokens

Chaque token est lié à un domaine spécifique. Si le token est utilisé depuis un autre domaine, la requête est rejetée et le token peut être automatiquement révoqué. Le serveur maintient un historique des domaines d'utilisation et détecte les anomalies. Une alerte est envoyée au propriétaire de la licence en cas d'usage suspect.

### Limitation des installations

Chaque licence peut spécifier un nombre maximum d'installations simultanées. Le serveur compte les domaines actifs pour chaque token. Si la limite est dépassée, les nouvelles installations sont bloquées. L'utilisateur doit désactiver une installation existante avant d'en activer une nouvelle.

### Watermarking des modules

Chaque module distribué peut inclure un watermark invisible lié au token de licence. Ce watermark permet de tracer l'origine d'un module en cas de redistribution non autorisée. Le système peut détecter et révoquer les licences utilisées pour distribuer illégalement des modules.

### Protection contre la rétro-ingénierie

Bien que le code source soit distribué, des techniques d'obfuscation peuvent être appliquées aux parties critiques. Les algorithmes de validation de licence sont volontairement complexes et distribués entre plusieurs fichiers. Les constantes et secrets sont générés dynamiquement à l'exécution plutôt que hardcodés.

## Monitoring et analytics

### Métriques côté serveur

Le serveur collecte des métriques anonymisées : nombre d'installations actives par module, nombre de téléchargements par jour, temps moyen de téléchargement et taux d'échec des installations. Ces métriques permettent d'identifier les problèmes et d'optimiser la distribution.

### Métriques côté client

Les clients peuvent optionnellement envoyer des métriques d'usage anonymisées : modules installés et leurs versions, fréquence d'utilisation de chaque module, erreurs rencontrées et performances des opérations. Ces données aident à améliorer le système et à prioriser les développements.

### Dashboard d'administration

Un dashboard Filament sur le serveur maître affiche toutes les métriques en temps réel. Il permet de visualiser les licences actives, les modules les plus populaires, les erreurs fréquentes et les tendances d'usage. Des alertes peuvent être configurées pour les événements critiques.

## Internationalisation et localisation

### Support multilingue

Le système supporte plusieurs langues pour l'interface. Les fichiers de traduction sont placés dans Lang/ de chaque module. Laravel Localization gère automatiquement la langue de l'utilisateur. Toutes les interfaces Filament utilisent les helpers de traduction. Les messages d'erreur et notifications sont également traduits.

### Langues supportées

Le système supporte initialement le français et l'anglais. D'autres langues peuvent être ajoutées facilement en créant les fichiers de traduction appropriés. La langue par défaut est configurée dans le .env. L'utilisateur peut changer de langue depuis l'interface Filament.

## Conformité et aspects légaux

### Licence logicielle

Chaque module doit déclarer sa licence dans le module.json. Les licences propriétaires, open source ou hybrides sont supportées. Le système affiche clairement les licences de tous les modules installés. L'utilisateur accepte les termes lors de l'installation d'un module.

### Protection des données

Le système respecte le RGPD pour les données collectées. Les tokens et licences sont des données personnelles protégées. Les métriques collectées sont anonymisées. L'utilisateur peut demander l'export ou la suppression de ses données. Une politique de confidentialité claire est fournie.

### Conformité PCI-DSS

Si le système gère des paiements, il doit être conforme PCI-DSS. Les données de carte bancaire ne sont jamais stockées localement. Les paiements passent par un gateway certifié. Les tokens de paiement sont isolés des tokens de licence.

## Roadmap et évolutions futures

### Phase 1 : MVP

La phase 1 comprend le développement des quatre modules core, l'API REST complète côté serveur, l'interface Filament d'administration et les commandes Artisan essentielles. Cette phase établit les fondations du système.

### Phase 2 : Fonctionnalités avancées

La phase 2 ajoute le support des licences d'essai temporaires, le système de plugins extensibles, les mises à jour différentielles pour économiser la bande passante et l'API webhook pour notifier les événements. Cette phase améliore l'expérience utilisateur.

### Phase 3 : Écosystème

La phase 3 crée un marketplace public de modules, un système de reviews et ratings, une documentation collaborative et un forum communautaire. Cette phase construit l'écosystème autour du système.

### Phase 4 : Enterprise

La phase 4 ajoute le support multi-tenant, l'authentification SSO, l'intégration Active Directory, les audits de sécurité avancés et le support technique prioritaire. Cette phase cible les grandes entreprises.

## Checklist de mise en production

### Avant le déploiement serveur

Vérifier que tous les tests passent avec coverage suffisant. Configurer correctement les variables d'environnement de production. Activer HTTPS avec certificat valide. Configurer les backups automatiques de base de données. Mettre en place le monitoring et les alertes. Documenter les procédures d'urgence. Effectuer un audit de sécurité complet.

### Avant le déploiement client

Tester l'installation sur un environnement vierge. Vérifier que toutes les dépendances sont satisfaites. Valider le processus complet d'installation à mise à jour. Préparer la documentation utilisateur finale. Former le support client sur les problèmes courants. Préparer les scripts de migration si nécessaire.

### Post-déploiement

Monitorer les logs intensivement les premiers jours. Être disponible pour le support immédiat. Collecter les retours utilisateurs rapidement. Corriger les bugs critiques en priorité. Communiquer de manière transparente sur les problèmes. Itérer rapidement sur les améliorations nécessaires.

## Conclusion

Ce système Numerimondes fournit une infrastructure complète, sécurisée et maintenable pour la distribution de modules Laravel/FilamentPHP. L'architecture modulaire garantit l'évolutivité et la flexibilité nécessaires pour s'adapter aux besoins futurs. L'accent mis sur la sécurité et l'utilisation exclusive de fonctions PHP natives assure la robustesse et la pérennité du système. Le développement séquentiel des quatre modules core (MasterConnector, Core, Connector, Updator) permet une mise en place progressive et testée à chaque étape. Cette approche méthodique minimise les risques et garantit un déploiement réussi.