# MasterConnector Module

**Version:** 1.0.0  
**Author:** Numerimondes Team  
**License:** Proprietary

## Overview

MasterConnector is the server-side orchestration module for the Numerimondes distribution system. It transforms a Webkernel application into a master server capable of distributing modules to client applications via secure, token-based authentication.

This module works in conjunction with the Platform modules (Core, Connector, Updator) to provide a complete module distribution ecosystem.

## Features

- **License Management**: Create, validate, and revoke licenses with cryptographic tokens
- **Module Catalog**: Upload, version, and distribute modules with integrity verification
- **Secure API**: RESTful endpoints with bearer token authentication and rate limiting
- **Filament Admin**: Full-featured administrative interface for licenses and modules
- **Audit Logging**: Track downloads and detect potential abuse
- **Organization Support**: Optional multi-tenant support for custom modules (PROPLUS)

## Architecture

### Server Role
When MasterConnector is installed, the application becomes a master server that:
- Generates and validates license tokens
- Distributes modules via secure API endpoints
- Applies rate limiting to prevent abuse
- Logs all download activity

### Reusing Platform Modules
The server also uses Platform modules as a client:
- **Core**: Token generation and cryptographic operations
- **Connector**: API client for testing and rate limiting middleware
- **Updator**: Module extraction and validation for uploads

This design eliminates code duplication and ensures consistency between server and client implementations.

## Installation

### Prerequisites
- Laravel 10+ with Webkernel
- PHP 8.1+
- FilamentPHP 3+
- MySQL/PostgreSQL database
- Storage disk configured (local or S3)

### Steps

1. **Copy Module**
   ```bash
   cp -r platform/MasterConnector /path/to/webkernel/platform/
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```

3. **Publish Configuration**
   ```bash
   php artisan vendor:publish --tag=master-connector-config
   ```

4. **Configure Environment**
   Add to `.env`:
   ```env
   NUMERIMONDES_STORAGE_DISK=local
   NUMERIMONDES_MODULES_PATH=numerimondes/modules
   NUMERIMONDES_MASTER_SECRET=your-secret-key
   NUMERIMONDES_AUDIT_ENABLED=true
   ```

5. **Set Permissions**
   ```bash
   chmod 770 storage/app/numerimondes
   ```

## Configuration

Edit `config/master-connector.php`:

```php
return [
    'storage_disk' => 'local', // or 's3'
    'modules_path' => 'numerimondes/modules',
    
    'rate_limits' => [
        'auth' => 60,       // validations per hour
        'download' => 10,   // downloads per hour
        'list' => 300,      // list requests per hour
    ],
    
    'audit' => [
        'enabled' => true,
        'log_downloads' => true,
    ],
];
```

## Usage

### Creating a License

Via Filament admin panel:
1. Navigate to **Licenses** resource
2. Click **Create**
3. Enter domain (e.g., `client-app.com`)
4. Select authorized modules
5. Set expiration date (optional, leave empty for perpetual)
6. Save and **copy the token** (shown once only)

Via code:
```php
use Platform\Numerimondes\MasterConnector\Services\LicenseManager;

$manager = app(LicenseManager::class);

$result = $manager->createLicense(
    domain: 'client-app.com',
    moduleIds: [1, 2, 3],
    expiresAt: now()->addYear(),
    metadata: ['client' => 'Acme Corp', 'plan' => 'premium']
);

// IMPORTANT: Show token to user immediately
$token = $result['token']; // e.g., "a7Fg3k9L..."
$license = $result['license'];
```

### Uploading a Module

Via Filament admin panel:
1. Navigate to **Modules** resource
2. Click **Create**
3. Upload ZIP file (must contain valid `composer.json` with Webkernel app-class)
4. System auto-extracts metadata and validates structure
5. Module is stored with SHA256 hash

Via code:
```php
use Platform\Numerimondes\MasterConnector\Services\ModuleUploader;

$uploader = app(ModuleUploader::class);

$module = $uploader->uploadModule(
    zipFile: $request->file('module'),
    organizationId: null // or specific org ID for custom modules
);
```

### API Endpoints

#### 1. Validate License
```http
POST /api/auth/validate
Authorization: Bearer {token}
Content-Type: application/json

{
  "domain": "client-app.com"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "license_id": 1,
    "expires_at": "2026-01-01T00:00:00Z",
    "status": "active",
    "modules": [
      {
        "id": 1,
        "identifier": "crm-pro",
        "name": "CRM Professional",
        "version": "1.2.0"
      }
    ]
  }
}
```

#### 2. List Modules
```http
GET /api/modules/list
Authorization: Bearer {token}
```

#### 3. Download Modules
```http
GET /api/modules/download?modules=1,2,3
Authorization: Bearer {token}
```

Returns ZIP file with `manifest.json` and `modules/` directory.

#### 4. Check Updates
```http
POST /api/modules/updates
Authorization: Bearer {token}
Content-Type: application/json

{
  "modules": [
    {
      "identifier": "crm-pro",
      "version": "1.1.0",
      "hash": "abc123..."
    }
  ]
}
```

Response:
```json
{
  "success": true,
  "data": {
    "updates": [
      {
        "identifier": "crm-pro",
        "current_version": "1.1.0",
        "new_version": "1.2.0",
        "type": "minor",
        "changelog": "Added new features..."
      }
    ]
  }
}
```

## Database Schema

### licenses
- `id`, `token_hash` (SHA256), `domain`, `status`, `expires_at`
- `metadata` (JSON), `last_validated_at`, `organization_id`

### modules
- `id`, `identifier`, `name`, `version`, `description`
- `zip_path`, `hash` (SHA256), `file_size`, `metadata` (JSON), `status`

### license_modules (pivot)
- `license_id`, `module_id`, `granted_at`, `revoked_at`

### download_logs (audit)
- `license_id`, `module_id`, `ip_address`, `success`, `error_message`, `downloaded_at`

## Security

### Token Security
- 256-bit entropy via `random_bytes(32)`
- Base64url encoding (64 characters)
- Server stores SHA256 hash only
- One-time display during creation
- Transmitted via Bearer header only

### Domain Validation
- Strict exact matching (no wildcards in MVP)
- Uses `HTTP_HOST` header via `Request::getHost()`
- Logs mismatches with IP addresses

### Rate Limiting
- 60 validation requests per hour per IP
- 10 downloads per hour per token
- 300 list/checksum requests per hour per token

### File Integrity
- SHA256 hash calculated on upload
- Hash validated pre-extraction by clients
- ZipArchive structure validation

## Troubleshooting

### "Module ZIP file not found"
Check storage disk configuration and file permissions:
```bash
php artisan storage:link
chmod -R 770 storage/app/numerimondes
```

### "Rate limit exceeded"
Adjust limits in `config/master-connector.php` or wait for rate limit window to reset.

### "Domain mismatch"
Ensure client uses exact domain from license. Check logs:
```bash
tail -f storage/logs/laravel.log | grep "domain mismatch"
```

### "Invalid composer.json"
Module ZIP must have:
```json
{
  "name": "vendor/module-name",
  "version": "1.0.0",
  "extra": {
    "webkernel": {
      "app-class": "Vendor\\ModuleName\\ModuleClass"
    }
  }
}
```

## Advanced Features

### Organization Support (PROPLUS)

Enable in `.env`:
```env
NUMERIMONDES_ORGANIZATIONS_ENABLED=true
```

Create organization:
```php
$org = Organization::create([
    'name' => 'Acme Corp',
    'slug' => 'acme-corp',
    'namespace' => 'OrgAcmeCorp',
    'status' => 'active',
]);
```

Upload custom module:
```php
$module = $uploader->uploadModule($zipFile, $org->id);
```

### Custom Overrides

Modules can include `overrides/` directory for:
- `composer.json` (with custom namespace autoload)
- `app/Models/User.php`
- `config/app.php`
- `basix/` (entire directory)

System backs up host files before applying.

## Monitoring

### Download Statistics
```php
use Platform\Numerimondes\MasterConnector\Models\DownloadLog;

// Recent downloads
$recent = DownloadLog::recent(24)->successful()->count();

// Detect abuse
$isAbuse = DownloadLog::detectAbuse($licenseId, hours: 1, threshold: 20);
```

### License Statistics
```php
$manager->getStatistics();
// ['total' => 100, 'active' => 85, 'expired' => 10, 'revoked' => 5]
```

## Testing

Run test suite:
```bash
php artisan test --filter=MasterConnector
```

Simulate dry-run upload:
```php
$extractor = app(ModuleExtractor::class);
$tempDir = storage_path('app/temp/test');
$valid = $extractor->extractAndValidate($zipPath, $tempDir, $expectedHash);
```

## Support

For issues or questions:
- Documentation: https://numerimondes.com/docs
- Support: support@numerimondes.com
- GitHub: https://github.com/numerimondes/webkernel

## License

Proprietary. All rights reserved. Unauthorized distribution prohibited.
