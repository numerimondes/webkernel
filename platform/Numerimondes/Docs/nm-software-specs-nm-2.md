# Numerimondes MVP - Spécifications Techniques Complètes

## 🎯 Vue d'Ensemble du Système

Numerimondes est un système de distribution et de gestion de modules pour applications Laravel/FilamentPHP basées sur Webkernel. Il repose sur une architecture serveur-client sécurisée, où le serveur maître (numerimondes.com) agit comme une application Webkernel enrichie du module MasterConnector pour orchestrer la distribution. Les applications clientes, y compris le serveur maître lui-même en tant que consommateur de ses propres services, utilisent les modules Platform partagés (Core, Connector, Updator) pour interagir avec le serveur. Cette réutilisation maximale du code assure une cohérence parfaite et simplifie la maintenance, sans recours à des flags comme `isServer()` ou `isClient()`. Être serveur signifie simplement installer le module MasterConnector, qui active les tâches de distribution tout en permettant au serveur de fonctionner comme un client via les modules Platform.

Le système priorise la sécurité par design, avec des tokens cryptographiques inviolables, une validation stricte des domaines, et une intégrité garantie des fichiers via hash SHA256. Les performances sont optimisées pour un overhead total inférieur à 1 ms, grâce à OPcache et au chargement dynamique d'Arcanes. Les migrations sont forward-only pour éviter toute perte de données, avec rollback via backups de fichiers. Le support des connexions lentes (jusqu'à 10 Kio/s) est intégré via streaming adaptatif, avec affichage de progression en temps réel inspiré des messages Git pour une expérience utilisateur fluide.

**Critique et Améliorations Intégrées :** La fusion des documents originaux élimine les redondances (par exemple, descriptions répétées des tokens et structures de modules) tout en enrichissant les paragraphes descriptifs pour une clarté accrue. L'accent est mis sur la réutilisation commune des modules Platform, avec une logique contextuelle asymétrique (serveur/client) sans conditionnalité lourde. Pour le fondateur/développeur, commencez par implémenter les modules Platform en commun (Core pour la sécurité de base, Connector pour la communication, Updator pour le cycle de vie), car ils forment le socle réutilisable. Les étapes prioritaires sont : 1) Configurer Webkernel et Arcanes ; 2) Développer Core (tokens et chiffrement) ; 3) Ajouter Connector (API client et sync) ; 4) Implémenter Updator (extraction et migrations) ; 5) Finaliser MasterConnector pour le serveur. Cela permet un MVP fonctionnel côté serveur en priorisant les flux critiques (validation, catalogue, téléchargement).

## 🏗️ Principes Architecturaux Fondamentaux

- **Réutilisation Maximale :** Les modules Platform (Core, Connector, Updator) contiennent toute la logique commune, partagée entre serveur et clients. MasterConnector orchestre ces modules côté serveur sans dupliquer de code.
- **Sécurité Intégrée :** Tokens générés avec `random_bytes(32)` (NIST 2025, 256 bits d'entropie), encodés en base64url ; stockage via hash SHA256 (serveur) et chiffrement AES-256 (client) ; validation domaine stricte via `HTTP_HOST`.
- **Performance <1 ms :** Cache OPcache pour configurations et modules ; hash invalidation <0.05 ms ; chargement dynamique Arcanes scalable à 50 000+ modules.
- **Migrations Forward-Only :** Exécution stricte sans `down()` ; rollback via backups fichiers (conservés 12h).
- **Support Connexions Lentes :** Streaming par chunks de 8 Kio, retry 3x (backoff exponentiel), timeout 30s + 10s/Mo ; progression Git-like pour feedback utilisateur.
- **Audit et Traçabilité :** Logs structurés JSON ; table `download_logs` optionnelle pour détection abus.

**Étapes de Développement Prioritaires (pour le Fondateur/Développeur) :**  
1. **Préparation Environnement :** Installer Webkernel, configurer Arcanes pour chargement dynamique ; créer base de données serveur/client avec migrations Platform.  
2. **Implémentation Core (Commun) :** Développer services tokens/chiffrement/cache ; tester génération/validation.  
3. **Implémentation Connector (Commun) :** Créer client HTTP, SyncService, middleware rate-limit (activé seulement si MasterConnector présent).  
4. **Implémentation Updator (Commun) :** Développer extraction, backups, migrations ; intégrer overrides et progression.  
5. **MasterConnector (Serveur Seul) :** Ajouter API, interface Filament, modèles ; tester flux end-to-end (création licence → sync client).  
6. **Tests et Déploiement :** Unitaires/intégration (90% couverture critiques) ; config prod (HTTPS, cache:cache).  
Ne manquez pas : Validation hash à chaque étape ; simulation dry-run côté serveur ; mode dégradé pour offline.

## 🏗️ Architecture des Modules Platform (Core, Connector, Updator) - Communs

Ces modules, situés dans `webkernel/src/Aptitudes/Platform/`, suivent la structure Webkernel standard. Ils encapsulent la logique réutilisable, avec différenciation contextuelle via présence de MasterConnector (serveur) ou non (client). Pour le développement, priorisez-les comme socle : Core pour sécurité, Connector pour réseau, Updator pour opérations fichiers/DB.

### Module Core - Gestion des Licences et Sécurité

**Emplacement :** `webkernel/src/Aptitudes/Platform/Core/`  
Ce module gère la cryptographie et les licences localement. Il génère/validate tokens avec entropie optimale, chiffre données sensibles via Laravel Crypt (clé APP_KEY), et maintient un cache dégradé pour offline.  

**Responsabilités Communes :**  
- Génération tokens : `random_bytes(32)` → base64url (64 chars, 256 bits entropie).  
- Validation : Hash SHA256 comparé (serveur) ou déchiffré localement (client).  
- Chiffrement : Wrapper Crypt pour stockage DB.  
- Cache : Infos licence (expiration, modules autorisés) pour fonctionnement dégradé (>7 jours sans sync = avertissement).  

**Structure :**  
```
Core/
├── Services/
│   ├── LicenseTokenService.php      // Génération/validation
│   ├── EncryptionService.php        // Chiffrement AES-256
│   └── LicenseCacheService.php      // Cache local
├── Models/
│   └── LocalLicense.php             // Table locale (1 ligne typique)
└── Config/
    └── CoreServiceProvider.php      // Enregistrement services
```

**Logique Différenciée :**  
- **Serveur (avec MasterConnector) :** Génère tokens/hashes pour nouvelles licences ; valide requêtes API.  
- **Client :** Déchiffre token stocké ; met à jour cache post-sync.  

**Critique :** Renforce la sécurité en évitant stockage token clair ; ajoute gestion erreurs (clé APP_KEY changée → reconfig).

### Module Connector - Communication Réseau

**Emplacement :** `webkernel/src/Aptitudes/Platform/Connector/`  
Ce module orchestre les interactions HTTP, avec robustesse pour réseaux instables : retry, timeouts adaptatifs, streaming lent.  

**Responsabilités Communes :**  
- Client HTTP : Laravel Http (timeout 30s +10s/Mo, retry 3x backoff, SSL strict).  
- Streaming : Downloads chunks 8 Kio ; événements progression (débit sur 5s fenêtre).  
- Synchronisation : Validation licence → catalogue → détection updates.  
- Rate Limiting : Middleware (60/h IP auth, 10/h download, 300/h list ; activé seulement serveur).  

**Structure :**  
```
Connector/
├── Services/
│   ├── MasterApiClient.php          // Requêtes HTTP
│   ├── StreamingDownloader.php      // Progression événements
│   └── SyncService.php              // Orchestration sync
├── Http/
│   └── Middleware/
│       └── RateLimitMiddleware.php  // Limites configurables
├── Filament/
│   └── Pages/
│       └── LicenseConfigPage.php    // Config client
└── Config/
    └── ConnectorServiceProvider.php
```

**Logique Différenciée :**  
- **Serveur :** Applique middleware sur API ; services clients pour tests.  
- **Client :** Utilise ApiClient/Streaming pour sync ; orchestre périodique (quotidien recommandé).  

**Critique :** Optimise bande passante via checksum checks ; intègre logs JSON pour debugging.

### Module Updator - Cycle de Vie des Modules

**Emplacement :** `webkernel/src/Aptitudes/Platform/Updator/`  
Ce module gère installation/mise à jour avec intégrité : hash validation, backups, migrations sécurisées, overrides serveur.  

**Responsabilités Communes :**  
- Extraction ZIP : Hash SHA256 pré-extraction ; validation structure (classe WebkernelApp).  
- Overrides : Backup/remplacement fichiers (composer.json, User.php, config/*, BASIX).  
- Migrations : Forward-only via Artisan ; rollback backups (no down()).  
- Backups : Complets (12h TTL) avec JSON métadonnées.  
- Progression : Événements Git-like ("Receiving objects: 100%...").  

**Structure :**  
```
Updator/
├── Services/
│   ├── ModuleExtractor.php          // Extraction/hash
│   ├── MigrationRunner.php          // Migrations sécurisées
│   ├── BackupService.php            // Backups/restore
│   ├── OverrideManager.php          // Overrides serveur
│   ├── ProgressReporter.php         // Messages Git
│   ├── ModuleInstaller.php          // Installation
│   └── ModuleUpdater.php            // Mise à jour
├── Console/
│   ├── InstallModuleCommand.php
│   ├── UpdateModuleCommand.php
│   ├── SyncCommand.php
│   └── ListModulesCommand.php
├── Filament/
│   └── Resources/
│       ├── InstalledModulesResource.php
│       └── AvailableModulesResource.php
└── Config/
    └── UpdatorServiceProvider.php
```

**Logique Différenciée :**  
- **Serveur :** Dry-run validation ZIP uploadés.  
- **Client :** Applique flux réel (téléchargement → backup → extract → migrate).  

**Critique :** Assure réversibilité sans perte données ; priorise SemVer pour updates (auto patch/minor, confirm major).

## 🎭 Module MasterConnector - Spécificité Serveur

**Emplacement :** `platform/MasterConnector/`  
Seule couche serveur : orchestre Platform pour distribution ; serveur reste client via eux.  

**Responsabilités Exclusives :**  
- **Interface Filament :** CRUD licences (token one-time, assign modules) ; CRUD modules (upload ZIP/hash) ; dashboard stats (téléchargements, actives). Optionnel : organisations/namespaces PROPLUS (custom composer.json).  
- **API REST :** 5 endpoints (validate, list, download, checksum/{id}, updates).  
- **Catalogue :** Upload/validation ZIP ; stockage Storage (local/S3) ; versioning SemVer.  
- **Audit :** Logs downloads ; historique révocations.  

**Structure :**  
```
MasterConnector/
├── Filament/
│   ├── Resources/
│   │   ├── LicenseResource.php      // CRUD + timeline
│   │   ├── ModuleResource.php       // Upload/hash
│   │   └── OrganizationResource.php // Optionnel
│   └── Pages/
│       └── Dashboard.php            // Stats
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       // Validate
│   │   └── ModulesController.php    // List/download/etc.
│   └── Middleware/
│       └── AuthenticateToken.php    // Bearer validation
├── Services/
│   ├── LicenseManager.php           // Métier licences
│   ├── ModuleCatalog.php            // Catalogue
│   └── ModuleUploader.php           // Upload/validation
├── Models/
│   ├── License.php
│   ├── Module.php
│   └── LicenseModule.php            // Pivot
├── Database/
│   └── Migrations/                  // 4 tables
└── Config/
    └── MasterConnectorServiceProvider.php
```

**Intégration Platform :** Utilise Core (tokens), Connector (rate-limit), Updator (extraction sim).  

**Critique :** Simplifie admin (one-time token modal) ; étend pour custom (namespaces via overrides).

## 💾 Schéma Base de Données

### Serveur Maître - 4 Tables

| Table | Description | Colonnes Clés | Logique |
|-------|-------------|---------------|---------|
| **licenses** | Licences émises | `id` (PK), `token_hash` (SHA256, unique), `domain` (VARCHAR(255)), `expires_at` (TIMESTAMP NULL), `status` (ENUM: active/expired/revoked), `metadata` (JSON), `created_at/updated_at` ; INDEX (token_hash, domain) | Hash seulement (no token clair) ; domaine strict ; metadata (client, plan). Ex: Acme Corp, perpétuelle. |
| **modules** | Catalogue | `id` (PK), `identifier` (VARCHAR(100), unique/version), `name` (VARCHAR(255)), `version` (VARCHAR(20) SemVer), `description` (TEXT), `zip_path` (VARCHAR(500)), `hash` (SHA256), `file_size` (BIGINT), `metadata` (JSON), `created_at/updated_at` ; UNIQUE (identifier, version) | Versioning multi ; hash intégrité ; metadata (changelog, deps). Ex: crm-pro 1.1.0, 1.1 MiB. |
| **license_modules** | Associations | `id` (PK), `license_id`/`module_id` (FK CASCADE), `granted_at` (TIMESTAMP), `revoked_at` (TIMESTAMP NULL) ; UNIQUE (license_id, module_id) | Many-to-many ; historique révocations. |
| **download_logs** (optionnel) | Audit | `id` (PK), `license_id` (FK), `module_id` (NULL), `ip_address` (VARCHAR(45)), `success` (BOOL), `error_message` (TEXT), `downloaded_at` ; INDEX (license_id, downloaded_at) | Détection abus ; stats dashboard. |

### Client - 1 Table + Cache Fichier

- **local_license :** `id` (PK), `token_encrypted` (TEXT, Crypt), `domain` (VARCHAR(255)), `last_synced_at`/`expires_at` (TIMESTAMP NULL), `status` (ENUM: active/expired/revoked/pending), `created_at/updated_at`. Logique : 1 ligne ; pending post-saisie ; >7j sync = warning.  
- **modules_cache.php** (`storage/framework/cache/`) : Array PHP (OPcache <0.1ms) ; champs : `synced_at`/`expires_at` (TTL 1h), `modules` (par identifier : id/name/version/hash/size/desc), `updates_available` (current/new/hash). Pourquoi fichier ? Perf/sécurité/simplicité ; hash invalidation ; futur chiffrement. Modules installés : Scan Arcanes (no DB track).

**Critique :** Optimise client (fichier vs DB) ; serveur scalable (JSON metadata).

## 🔐 Système de Sécurité des Tokens

**Génération (Serveur) :**  
```php
public function generateToken(): string {
    $bytes = random_bytes(32);
    return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
}
```
Propriétés : 256 bits entropie ; safe HTTP ; one-time affichage (modal copie + warning).  

**Stockage :**  
- Serveur : Hash SHA256 → DB ; no original post-création.  
- Client : `Crypt::encryptString($token)` → DB ; protégée APP_KEY.  

**Validation (Serveur) :**  
Middleware extrait Bearer ; hash calculé → query DB (exact domain via `getHost()`) ; check status/expiration. Ex: 403 si mismatch. Logs warning (expected/received domain, IP). No wildcard MVP.  

**Critique :** Résiste brute-force/DB leak ; strict domain limite impact vol.

## 🌐 API REST du Serveur Maître

**Conventions :** JSON `{success: bool, data: {}, error: str}` ; codes HTTP sémantiques ; rate-limits (60/h IP auth, 10/h download, 300/h list) ; cache 1h (invalide upload).  

| Endpoint | Méthode | Auth | Params/Body | Response Succès | Erreurs | Rate Limit |
|----------|---------|------|-------------|-----------------|---------|------------|
| `/api/auth/validate` | POST | Bearer (token) | Body: `{domain: str}` | `{license_id, expires_at, status, modules: []}` | 401 (no token), 400 (no domain), 403 (invalid/expired) | 60/h IP |
| `/api/modules/list` | GET | Bearer | - | `{modules: [{identifier, name, version, desc, size, hash}]}` | 401/403 | 300/h token |
| `/api/modules/download` | GET | Bearer | Query: `modules= id1,id2` | ZIP stream (manifest.json + modules/) ; Content-Type: application/zip | 401/403 (unauth/unauthorized), 404 (no module) | 10/h token |
| `/api/modules/checksum/{identifier}` | GET | Bearer | Route: identifier | `{identifier, version, hash}` | 401/403/404 | 300/h token |
| `/api/modules/updates` | GET | Bearer | Body: `{modules: [{id, version, hash}]}` | `{updates: [{id, current_version, new_version, hash, size, changelog, type: major/minor/patch}]}` | 401/403/400 | 300/h token |

**Critique :** Optimise (multi-download, checksum pre-check) ; streaming pour lent ; update_type guide auto-updates.

## 🔄 Processus de Synchronisation, Installation et Mise à Jour

**Synchronisation (Client, périodique) :** Déclenchée manual (Artisan/Filament) ou future schedule (daily). Étapes : Déchiffrer token → validate API → update local_license/cache → list catalogue → updates check → checksum validate (si needed). Échecs : Mode dégradé (cache conservé) ; >30j = restriction installs. Temps : <2s normal.  

**Installation (ModuleInstaller) :** Trigger : Filament/Artisan. Étapes : Backup (si overrides) → Download ZIP (stream) → Hash/Extract (temp) → Overrides apply (backup hôte) → Migrations forward → Move atomic → Cache clear. Progression Git-like. Échecs : Delete temp/restore. Temps : <5s.  

**Mise à Jour (ModuleUpdater) :** Similaire + backup existant (incl overrides) ; SemVer compare (confirm major) ; delete old post-backup. Rollback 12h. Temps : <3s.  

**Critique :** Atomique/réversible ; dépendances déclaratives auto (si autorisées).

## ⚙️ Chargement Dynamique et Interface

**Arcanes (Webkernel) :** Boot : instantBootstrap (OPcache cache) ou smartDiscovery (scan <0.5ms) ; lazyBoot (providers/routes). Invalidation hash mtime/size. Scalable <1ms total.  

**Interface Filament Client :** LicenseConfigPage (token masque/sync) ; InstalledModules (scan Arcanes, updates badge) ; AvailableModules (cache, install bulk) ; Dashboard (stats licence/modules).  

**Commandes Artisan :** `configure [--token]` (chiffre/sync initial) ; `sync` (résumé) ; `install/update {id} [--allow-major]` (progression) ; `list [--updates]` ; `rollback {id}`.  

**Critique :** Dynamique sans DB track ; UI intuitive pour admin non-tech.

## 🛡️ Gestion Erreurs, Sécurité et Déploiement

La gestion des erreurs est conçue pour assurer une résilience maximale, en distinguant les défaillances transitoires des problèmes critiques. Pour les erreurs réseau, telles que des timeouts ou des connexions refusées lors des synchronisations, le système enregistre des logs au niveau warning sans interrompre les opérations courantes. Le cache local des modules et des licences est préservé, permettant un fonctionnement continu en mode dégradé. L'interface Filament affiche des avertissements contextuels : un bandeau jaune après 7 jours sans synchronisation réussie, et un blocage partiel des nouvelles installations après 30 jours, incitant à une intervention manuelle. En cas d'échec d'authentification (code 403), la licence est marquée comme potentiellement révoquée localement, bloquant les téléchargements futurs tout en maintenant les modules existants opérationnels pour une transition gracieuse. Les échecs de validation de hash ou de migrations déclenchent un rollback immédiat via restauration des backups, sans invocation de méthodes `down()`, et émettent des événements Laravel pour un logging centralisé et des notifications optionnelles vers le serveur maître.

La sécurité supplémentaire renforce les protections au-delà des tokens. Tous les logs sont structurés en JSON pour une analyse automatisée, incluant les échecs de validation de domaine (domaine attendu vs reçu, adresse IP, hash partiel du token). Les requêtes SQL utilisent exclusivement les builders Eloquent pour éviter les injections, sans concaténation manuelle. L'HTTPS est imposé strictement via middleware de redirection, avec vérification SSL côté client. L'audit via la table `download_logs` permet la détection proactive d'abus, comme des téléchargements massifs, et génère des alertes automatisées pour l'administrateur serveur. Aucune fonction système dangereuse n'est utilisée ; toutes les opérations de fichiers passent par Laravel Filesystem pour une isolation sécurisée.

Le déploiement est simplifié pour une mise en production rapide et sécurisée. Pour le serveur maître, installez Webkernel via Composer, copiez le module MasterConnector dans `platform/`, exécutez les migrations pour les 4 tables, et configurez le fichier `.env` avec `APP_KEY` (générée via `php artisan key:generate`), `APP_URL` (HTTPS obligatoire), et `NUMERIMONDES_MASTER_SECRET` pour des clés internes optionnelles. Le disque de stockage pour les modules (local ou S3) doit avoir des permissions 770 pour restreindre l'accès. Pour les clients, installez Webkernel et les modules Platform ; définissez `NUMERIMONDES_MASTER_URL` dans `.env` ; saisissez le token via l'interface Filament (sans stockage en clair). En production, exécutez `php artisan config:cache` et `route:cache` pour optimiser les performances ; configurez un scheduler Laravel pour une synchronisation quotidienne (`schedule()->command('numerimondes:sync')->daily()`) et un nettoyage mensuel des backups (`schedule()->command('cleanup:backups')->monthly()`) ; intégrez un monitoring comme Laravel Telescope ou Sentry pour tracer les erreurs en temps réel. Les environnements de staging utilisent des domaines distincts pour tester les validations strictes.

**Critique :** Erreurs gérées proactivement pour UX fluide ; sécurité multicouche sans complexité ; déploiement idempotent et scalable.

## 🧪 Tests et Assurance Qualité

Les tests couvrent exhaustivement les composants critiques pour garantir une fiabilité de 99,9 % en production. Les tests unitaires se concentrent sur les services isolés du module Core, validant le chiffrement/déchiffrement des tokens avec des cas limites (clés corrompues, tokens malformés) et la génération/validation des hash SHA256. Les tests d'intégration simulent les flux end-to-end, comme la création d'une licence, la synchronisation client-serveur, et l'installation/mise à jour de modules, en mockant les appels HTTP à 10 Kio/s pour reproduire des connexions lentes. Les tests E2E utilisent Pest pour valider les API REST (codes de réponse, payloads JSON) et les événements Laravel émis lors des progressions. La couverture code cible 90 % pour les parties critiques (sécurité, migrations, sync), avec un focus sur la latence (<1 ms pour chargement Arcanes). Des benchmarks automatisés mesurent l'overhead OPcache et l'invalidation de cache. Les tests de sécurité incluent des scans pour injections et fuites de tokens, tandis que les simulations dry-run côté serveur valident les ZIP avant distribution.

**Critique :** Approche pyramidale (unit > intégration > E2E) ; mocks réalistes pour edge cases ; intégration CI/CD pour runs quotidiens.

## 📋 Considérations Prioritaires Intégrées

Les overrides serveur permettent une personnalisation profonde sans compromettre l'intégrité : détection automatique et backup des fichiers comme `composer.json`, `app/Models/User.php`, `config/app.php`, `config/database.php`, ou le répertoire BASIX entier. Pour les modules personnalisés (licence PROPLUS minimum), l'enregistrement obligatoire sur numerimondes.com assure la traçabilité des namespaces ; un `composer.json` enrichi (autoload PSR-4) est distribué via overrides pour une intégration immédiate. Le module Updator exclut toute logique de résolution de dépendances (déclaratives seulement, auto-install si autorisées), émet des messages Git-like pour les opérations longues, et supporte le streaming à 10 Kio/s. Le cache `modules_cache.php` utilise un array PHP pour des lectures ultra-rapides, avec TTL 1h et chiffrement futur pour opacité. Les tokens respectent strictement les standards NIST 2025 via `random_bytes`. La performance globale reste sous 1 ms grâce à OPcache et à l'invalidation par hash (mtime + size). Ces éléments garantissent un MVP robuste, évolutif, et aligné sur les contraintes réseau/sécurité.

**Critique Globale :** Spécifications fusionnées exhaustives, sans redondance ni omission ; paragraphes descriptifs renforcent la compréhension ; priorités développement centrées sur Platform commun pour un démarrage serveur efficace. Le système est prêt pour un prototype fonctionnel en 4-6 semaines, avec scalabilité native.
