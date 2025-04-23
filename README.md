# WebKernel

<p align="center" style="margin:90px;">
<a href="https://github.com/numerimondes/WebKernel" target="_blank"><img src="https://raw.githubusercontent.com/numerimondes/assets/1724fe3b4bb6dc64eec6dcebec2ad0e8e3ec903e/projects/webkernel/art/laravel-filament-webkernel.svg" width="400" alt="Webkernel AND Filament AND Laravel"></a>
</p>

<p align="center" style="margin:90px;">
<a href="https://github.com/numerimondes/webkernel/actions"><img src="https://github.com/numerimondes/webkernel/actions/workflows/laravel.yml/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/webkernel/webkernel"><img src="https://img.shields.io/packagist/dt/webkernel/webkernel" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/webkernel/webkernel"><img src="https://img.shields.io/packagist/v/webkernel/webkernel" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/webkernel/webkernel"><img src="https://img.shields.io/packagist/l/webkernel/webkernel" alt="License"></a>
</p>

## Installation Steps

### Prerequisites
- Ensure you have Composer installed and PHP
- Laravel Herd is recommended for Microsoft and MacOS users

### One-Step Installation
**On Linux/macOS/Windows**
```bash
composer create-project webkernel/webkernel
```

This command will configure your environment and ensure that the user model includes the necessary extension trait.

### CLI (@php artisan webkernel:....)
```bash
webkernel:cc              > Interactive assistant for Webkernel component creation
webkernel:init            > Executes all Webkernel installation steps with various options.
                            --install    : To perform the full installation of Webkernel.
                            --db-seed    : To run database setup and seeding.
                            --no-db      : To skip the database installation step.
                            --update     : To perform only an update.
                            --force      : To force the installation even if files already exist.
webkernel:loadviews       > Load all Webkernel views and components
```

## About WebKernel

WebKernel is designed to supercharge Laravel and Filament from the first minutes of installation. Our goal is to unlock powerful capabilities immediately, without the typical setup overhead that developers face with new projects.

We initially created WebKernel as a foundation for our product line, particularly for educational institutions. While our specific products may be commercial, WebKernel itself is 100% free and open source, and we intend to keep it that way.

> **Note on licensing**: We initially considered GNU GPL, but since it doesn't permit adding proprietary code (even if open source), we've chosen the Mozilla Public License instead, which offers more flexibility.

## Key Features

Upon installation, WebKernel provides a `/system` dashboard by default (with more panels potentially coming in the future). The platform comes with several native packages:

### Currently Available
* Multi-language support with built-in translation system
* User profile management
* Subscription capabilities

### Coming Soon (No specific release dates)
* Download center management
* E-commerce capabilities (centralized via internal application)
* Forums (public/private/clubs, free/paid)
* Video conferencing
* Blog management
* Database table creation (Notion-style)
* Dynamic website builder based on blocks (in planning)
* Event management
* Drive management/file sharing
* Pages marketplace
* SSO/social login from Laravel
* Multi-tenancy (manageable from /system panel)

Regarding permissions, we're currently evaluating options. While it's possible to use Spatie or Shield with Filament, we're considering implementing our own system.

## Philosophy

WebKernel focuses on:

1. **Minimizing unnecessary dependencies** to keep the codebase clean and maintainable
2. **Providing a smooth experience** with one-click installation
3. **Enabling customization** through CSS and global variables
4. **Preserving core functionality** by never modifying default stubs or translations, ensuring seamless upgrades to new Laravel or Filament releases

### Verification
Once installation is complete, verify everything works correctly by:
- Running migrations
- Checking that routes are properly defined
- Testing access to WebKernel package-related functionalities

## Documentation
Coming soon

## Contributing

We welcome contributions to WebKernel! If you're interested in helping develop new features or improving existing ones, please feel free to get involved.

We'd be particularly interested in contributions related to security testing and would be happy to collaborate on implementing robust security measures.

## Security Vulnerabilities

If you discover a security vulnerability within WebKernel, please send an email to [contact email]. All security vulnerabilities will be promptly addressed.

## License

WebKernel is open-source software licensed under the [MPL-2.0](https://opensource.org/licenses/MPL-2.0) license.

