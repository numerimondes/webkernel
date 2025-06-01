# WebkernelTestpackage

A test package to test if it works

## Installation

Install the package via Composer:

```bash
composer require webkernel/webkernel-testpackage
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=webkernel-testpackage-config
```

This will create a `config/webkernel_testpackage.php` file where you can customize the package settings.

Publish the assets:

```bash
php artisan vendor:publish --tag=webkernel-testpackage-assets
```

Publish the views (optional):

```bash
php artisan vendor:publish --tag=webkernel-testpackage-views
```

## Usage

### Basic Usage

The package integrates automatically with Webkernel. Once installed, you can:

1. Access the package via the configured routes
2. Use the provided helpers in your application
3. Extend the base functionality as needed

### Configuration Options

Key configuration options in `config/webkernel_testpackage.php`:

- `enabled`: Enable/disable the package
- `settings.debug`: Enable debug mode
- `settings.cache_enabled`: Enable caching
- `database.table_prefix`: Database table prefix

### Helper Functions

The package provides several helper functions:

```php
// Get package version
$version = webkernel_testpackage_version();

// Get configuration value
$config = webkernel_testpackage_config('settings.debug');

// Check if package is enabled
$enabled = webkernel_testpackage_enabled();

// Generate package route
$url = webkernel_testpackage_route('index');
```

## Development

### Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

### Directory Structure

```
src/
├── Console/           # Artisan commands
├── Http/              # Controllers and middleware
├── Models/            # Eloquent models
├── Providers/         # Service providers
├── config/            # Configuration files
├── database/          # Migrations and seeders
├── lang/              # Translation files
├── resources/         # Views and assets
└── routes/            # Route definitions
```

## Requirements

- PHP ^8.1
- Laravel ^10.0|^11.0
- Webkernel ^1.0

## Changelog

### v0.0.1

- Initial release
- Basic package structure
- Configuration system
- Helper functions
- Test suite

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## Security

If you discover any security-related issues, please email team@webkernel.org instead of using the issue tracker.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- [Webkernel Team](https://github.com/webkernel)
- [All Contributors](../../contributors)

## Support

For support, please contact team@webkernel.org or create an issue on GitHub.
