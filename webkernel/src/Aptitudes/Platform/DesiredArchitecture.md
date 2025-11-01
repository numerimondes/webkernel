webkernel/src/Aptitudes/Platform/
├── Core/
│   ├── CoreModule.php
│   ├── Config/
│   │   └── LicenseConfig.php
│   ├── Services/
│   │   ├── LicenseService.php
│   │   ├── SecurityService.php
│   │   └── CacheService.php
│   ├── Storage/
│   │   └── LicenseStorage.php
│   ├── ValueObjects/
│   │   ├── License.php
│   │   └── ValidationResult.php
│   ├── Exceptions/
│   │   ├── LicenseException.php
│   │   ├── ValidationException.php
│   │   └── StorageException.php
│   ├── Traits/
│   │   └── LicenseSensitive.php
│   ├── Http/
│   │   └── Middleware/
│   │       └── EnsureValidLicense.php
│   ├── Providers/
│   │   └── CoreServiceProvider.php
│   └── Facades/
│       └── License.php
│
├── Connector/
│   ├── ConnectorModule.php
│   ├── Services/
│   │   ├── AuthenticationService.php
│   │   ├── ValidationService.php
│   │   └── TokenManager.php
│   ├── Http/
│   │   └── Client/
│   │       └── LicenseClient.php
│   ├── Filament/
│   │   └── Pages/
│   │       └── LicenseManagement.php
│   ├── Resources/
│   │   └── Views/
│   │       └── license-management.blade.php
│   ├── Providers/
│   │   └── ConnectorServiceProvider.php
│   └── Facades/
│       └── Connector.php
│
└── Updator/
    ├── UpdatorModule.php
    ├── Services/
    │   ├── UpdateService.php
    │   ├── DownloadService.php
    │   ├── ExtractionService.php
    │   ├── MigrationService.php
    │   ├── BackupService.php
    │   └── CleanupService.php
    ├── ValueObjects/
    │   ├── UpdatePackage.php
    │   └── UpdateResult.php
    ├── Filament/
    │   └── Pages/
    │       └── UpdateManagement.php
    ├── Console/
    │   └── Commands/
    │       ├── CheckUpdatesCommand.php
    │       ├── InstallUpdateCommand.php
    │       └── RollbackUpdateCommand.php
    ├── Providers/
    │   └── UpdatorServiceProvider.php
    └── Facades/
        └── Updator.php
