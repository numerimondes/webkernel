# Platform Composer Command

This Laravel Artisan command automates the maintenance of composer.json files and validates namespace compliance across a WebKernel platform project, with enhanced support for custom namespace mappings and Laravel convention handling.

## Overview

The `webkernel:prepare-platform-composer` command ensures proper PSR-4 autoloading configuration and namespace consistency for projects structured with WebKernel packages and platform directories. The command now includes flexible namespace mapping capabilities and maintains compatibility with Laravel framework conventions.

## Project Structure

The command handles the following directory structure:

```
project/
├── packages/
│   ├── webkernel/               # Uses Webkernel namespace
│   │   ├── settings/           # Custom namespace: WebkernelSettings
│   │   └── components/         # Maintains Laravel conventions
│   └── <other-packages>/       # Dynamic namespace discovery
├── platform/                   # Uses Numerimondes namespace
├── app/                        # Standard Laravel app
└── composer.json               # Main composer configuration
```

## Features

The command performs comprehensive validation and maintenance tasks with enhanced flexibility:

**Prerequisites Check**: Verifies that the autoload system is functional before executing the main logic. If vendor/autoload.php is missing, the command automatically runs composer install to establish the autoloading foundation.

**Flexible Namespace Mapping**: Supports both primary namespace mappings and additional custom namespace mappings through configurable arrays. This allows for fine-grained control over specific subdirectories that require different namespace conventions.

**Laravel Convention Preservation**: Maintains Laravel framework conventions for directory naming while ensuring proper PSR-4 compliance. The system handles special directory mappings to preserve Laravel's expected structure for components, middleware, controllers, and other framework-specific directories.

**Namespace Validation and Correction**: Scans all PHP files in the packages and platform directories to ensure namespace declarations match the expected PSR-4 structure. The validation process now respects both primary and additional namespace mappings, automatically correcting violations while preserving Laravel conventions.

**Enhanced Composer Configuration Management**: Updates the composer.json file with comprehensive autoload configurations, including PSR-4 mappings for all discovered namespaces, file autoloading for helper functions, required dependencies for Laravel 12 and FilamentPHP v4 BETA, and repository definitions for WebKernel access.

**Intelligent Package Discovery**: Automatically discovers additional packages in the packages directory by reading their individual composer.json files and extracting namespace information, enabling seamless integration of new packages without manual configuration.

## Command Usage

The command is designed to run automatically during composer operations:

```json
{
  "scripts": {
    "pre-autoload-dump": [
      "@php artisan webkernel:prepare-platform-composer"
    ]
  }
}
```

Manual execution is also supported:

```bash
php artisan webkernel:prepare-platform-composer
```

## Technical Implementation

The command consists of three main components with enhanced functionality:

**PlatformComposer Class**: The main command class that coordinates the entire process, manages both primary and additional namespace mappings, handles Laravel convention preservation, and provides comprehensive user feedback through the console interface.

**NamespaceValidator Class**: Handles the validation and correction of namespace declarations in PHP files, with support for special directory mappings that preserve Laravel conventions. The validator ensures PSR-4 compliance while respecting framework requirements for directory structure.

**ComposerUpdater Class**: Manages all modifications to the composer.json file, including autoload configuration for all namespace mappings, dependency management for Laravel 12 and FilamentPHP v4 BETA, and repository definitions for WebKernel package access.

## Configuration

The command uses a flexible configuration system with multiple mapping arrays:

### Primary Namespace Mappings
```php
[
    'packages/webkernel' => 'Webkernel',
    'platform' => 'Numerimondes',
]
```

### Additional Namespace Mappings
```php
[
    'packages/webkernel/settings' => 'WebkernelSettings',
    // Add more custom namespace mappings here as needed
]
```

### Special Directory Mappings
```php
[
    'components' => 'Components',
    'middleware' => 'Middleware',
    'controllers' => 'Controllers',
    'providers' => 'Providers',
    'models' => 'Models',
    'commands' => 'Commands',
    'jobs' => 'Jobs',
    'events' => 'Events',
    'listeners' => 'Listeners',
    'policies' => 'Policies',
    'services' => 'Services',
    'repositories' => 'Repositories',
    // Additional Laravel convention mappings
]
```

## Enhanced Namespace Handling

The command now provides sophisticated namespace management capabilities:

**Custom Namespace Support**: The additional namespace mappings allow for specific subdirectories to use custom namespaces, enabling complex project structures while maintaining PSR-4 compliance.

**Laravel Convention Compatibility**: The special directory mappings ensure that Laravel framework conventions are preserved, preventing conflicts between PSR-4 requirements and Laravel's expected directory structure.

**Flexible Mapping Extension**: New namespace mappings can be easily added to the configuration arrays without modifying the core command logic, providing a scalable solution for growing project requirements.

## Dependencies

The command requires the following dependencies:

- Laravel 12 framework components
- FilamentPHP v4 BETA
- WebKernel package repository access
- PHP 8.1 or higher with strict type declarations

## Error Handling

The command includes comprehensive error handling for common scenarios:

- Missing vendor directory or autoload files
- Invalid composer.json syntax
- Inaccessible file system paths
- Composer binary not found in system PATH
- Failed composer install operations
- Namespace validation conflicts
- File permission issues during namespace correction

## Output

The command provides detailed feedback about all operations performed:

- Prerequisites check results with specific failure reasons
- Namespace validation findings for all processed directories
- Automatic corrections applied with file-specific details
- Composer configuration updates with change summaries
- Package discovery results with namespace information
- Complete summary of all changes made during execution

## Safety Features

The command includes several safety mechanisms to prevent data loss and ensure reliability:

- Validates composer.json syntax before making changes to prevent corruption
- Creates detailed logging of all operations for audit purposes
- Uses atomic file operations to prevent partial updates
- Provides comprehensive error messages for troubleshooting
- Maintains backup information through change tracking
- Implements rollback capabilities for critical failures

## Integration

This command integrates seamlessly with the WebKernel platform architecture and supports advanced development workflows. The enhanced namespace mapping capabilities enable complex project structures while maintaining clean separation of concerns. The system supports both automated execution through composer scripts and manual execution for development and debugging purposes.

The command's flexible configuration system allows for easy extension and customization as project requirements evolve, making it suitable for both small-scale applications and large enterprise platforms built on the WebKernel architecture.

## Troubleshooting

Common issues and their solutions:

**Namespace Conflicts**: When Laravel conventions conflict with PSR-4 requirements, the command automatically applies special directory mappings to resolve the conflict while maintaining framework compatibility.

**Missing Dependencies**: If required packages are not available, the command provides clear instructions for installing missing dependencies and configuring repository access.

**Permission Issues**: File permission problems during namespace correction are handled gracefully with informative error messages and suggested solutions.

**Composer Installation Failures**: The command includes fallback mechanisms for composer binary detection and provides detailed error reporting for installation failures.