# User Extensions System

A comprehensive, scalable system for extending User model functionality with related data while maintaining clean architecture principles. This enhanced version supports extension models across any namespace structure while preserving simplicity and operational reliability.

## System Architecture Overview

This system provides a robust framework that automatically detects appropriate extension models based on attribute patterns, manages creation and updates of extension records with minimal code complexity, maintains User model integrity through a single trait implementation, and scales infinitely by supporting new extension models across diverse namespace structures.

The architecture supports intuitive access patterns for extension data through multiple naming conventions including snake_case, camelCase, and lowercase variants. Extension models function as independent Eloquent models while maintaining seamless integration with the primary User model through sophisticated relationship management.

## Core System Components

### Enhanced ExtensionManager Class

The ExtensionManager serves as the central orchestration component that handles creation and updates of extension records across any namespace structure. The manager automatically detects correct models based on attribute patterns and provides intelligent relationship name mapping for property access operations.

The manager utilizes ArcaneBuildModule for extension discovery and supports multiple naming conventions through dynamic relationship mapping. Performance optimization includes extension map caching per request cycle and comprehensive error handling with detailed logging for troubleshooting scenarios.

Key capabilities include dynamic extension model detection based on provided attributes, support for updateOrCreate operations with proper conflict resolution, intelligent relationship name resolution supporting multiple coding style preferences, comprehensive validation of extension model requirements and configurations, and detailed logging for system monitoring and debugging purposes.

### UserExtensionsRelationsTrait Implementation

The trait provides the primary interface for User model integration while maintaining minimal code footprint. Implementation includes extension manager caching to avoid redundant instantiation, intelligent namespace-agnostic model resolution, and optimized relationship caching to prevent duplicate database queries.

The trait implements magic property access that delegates relationship resolution to the ExtensionManager rather than making namespace assumptions. This approach ensures extension models can exist in any namespace while preserving intuitive access patterns that developers expect from Eloquent relationships.

Performance optimizations include extension manager instance caching, relationship result caching with automatic cache invalidation, and intelligent query prevention through relationship loading status checks. The trait maintains full backward compatibility while supporting advanced namespace configurations.

### Extension Model Requirements

Extension models function as standard Eloquent models with specific interface requirements. Each extension model must implement the getHandledAttributes static method that returns an array of attribute names the model can process. Models support standard Eloquent features including casts, fillable attributes, relationships, scopes, and model events.

Models can exist in any namespace structure including application-specific namespaces, dedicated extension namespaces, third-party package namespaces, and custom business logic namespaces. The system automatically discovers and maps these models regardless of their namespace location.

### Extension Class Discovery System

The User Extensions system employs a sophisticated two-tier discovery mechanism through the `allUserExtensions()` method in `ArcaneBuildModule`. This method combines dynamic module scanning with static configuration to provide comprehensive extension model resolution.

#### Dynamic Discovery Process

The system automatically scans all registered Webkernel modules for extension models located in their `Models/UserExtensions` directories. For each discovered module, the system:

- Locates the `Models/UserExtensions` directory within the module structure
- Scans all PHP files in the directory and attempts to load them
- Constructs fully qualified class names based on the module's namespace
- Validates class existence using `class_exists()` before adding to the collection
- Handles loading errors gracefully with comprehensive logging

This approach ensures that extension models are automatically discovered when modules are added or modified, eliminating manual registration requirements for standard module-based extensions.

#### Static Configuration Integration

The discovery system merges dynamically found extensions with statically defined ones from `bootstrap/user-extensions.php`. This file allows developers to register extension models that exist outside the standard module structure or require explicit inclusion.

```php
<?php
// bootstrap/user-extensions.php
return [
    App\Models\UserProfile::class,
    External\CRM\Models\CustomerData::class,
    Vendor\Analytics\UserTracking::class,
];
```

The system validates each statically configured class using `class_exists()` to prevent runtime errors from missing dependencies. Invalid or non-existent classes are filtered out with appropriate logging for debugging purposes.

#### Deduplication and Error Handling

The `allUserExtensions()` method employs `array_unique()` to eliminate duplicate class names that might appear in both dynamic discovery and static configuration. Comprehensive error handling ensures system stability:

- Module scanning errors are logged but do not interrupt the discovery process
- File loading failures are handled gracefully with debug logging
- Invalid class references are filtered out with warning logs
- Bootstrap file loading errors are caught and logged without system failure

This robust discovery mechanism ensures reliable extension model resolution while maintaining system stability even when individual modules or configuration files contain errors.

## Installation and Configuration

### User Model Integration

Integration requires adding the UserExtensionsRelationsTrait to your User model with no additional configuration requirements. The trait provides immediate access to extension functionality through the extension method and automatic magic property access for all configured extension models.

```php
<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Webkernel\Aptitudes\Users\UserExtensions\Traits\UserExtensionsRelationsTrait;

/**
 * User model with extension system integration.
 * Provides seamless access to extension models across any namespace.
 */
class User extends Authenticatable
{
    use UserExtensionsRelationsTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

### Extension Model Creation

Extension models require minimal setup while maintaining full Eloquent functionality. Models must implement the getHandledAttributes static method and can exist in any namespace that suits your application architecture.

```php
<?php
namespace App\Extensions;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Developer profile extension model.
 * Handles developer-specific attributes and relationships.
 */
class DeveloperProfile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'developer_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'github_username', 'preferred_language', 'experience_years', 
        'skills', 'is_active', 'user_id'
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'skills' => 'array',
        'experience_years' => 'integer',
        'is_active' => 'boolean',
        'last_contribution' => 'datetime',
    ];
    
    /**
     * Define which attributes this extension model can handle.
     * These attributes determine when this model is selected for operations.
     *
     * @return array<string>
     */
    public static function getHandledAttributes(): array
    {
        return [
            'github_username', 'preferred_language', 'experience_years', 
            'skills', 'is_active'
        ];
    }
    
    /**
     * Define the relationship back to the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, DeveloperProfile>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### ArcaneBuildModule Configuration

The ArcaneBuildModule must return a comprehensive array of extension class names with full namespace qualifiers. The configuration supports extension models from diverse sources including application models, dedicated extension namespaces, third-party packages, and external modules.

```php
<?php
namespace Webkernel\Arcanes\Assemble;

/**
 * Build module configuration for user extensions.
 * Centralizes extension model discovery and registration.
 */
class ArcaneBuildModule
{
    /**
     * Get all user extension model class names.
     * Returns fully qualified class names from any namespace.
     *
     * @return array<class-string>
     */
    public function allUserExtensions(): array
    {
        return [
            'App\\Models\\Developer',
            'App\\Extensions\\UserProfile',
            'App\\Extensions\\Subscription',
            'Vendor\\CRM\\Models\\CustomerData',
            'External\\Analytics\\UserTracking',
            'Company\\Modules\\HR\\EmployeeDetails',
        ];
    }
}
```

## Operational Usage Patterns

### Extension Record Management

The system provides streamlined methods for creating and updating extension records with automatic model detection. Operations include creation of new extension records with automatic model selection, update-or-create functionality with conflict resolution, and comprehensive error handling for invalid attribute combinations.

```php
/**
 * Create a new developer profile extension.
 * System automatically selects DeveloperProfile model based on github_username attribute.
 */
$developer = Auth::user()->extension()->create([
    'github_username' => 'numerimondes',
    'preferred_language' => 'PHP',
    'experience_years' => 5,
    'skills' => ['Laravel', 'Vue.js', 'MySQL'],
    'is_active' => true
]);

/**
 * Update existing profile or create if it doesn't exist.
 * Maintains data consistency through proper conflict resolution.
 */
$updated = Auth::user()->extension()->updateOrCreate([
    'github_username' => 'updated_username',
    'preferred_language' => 'Python',
    'skills' => ['Django', 'React', 'PostgreSQL']
]);
```

### Extension Data Access

Extension data access utilizes magic property functionality that supports multiple naming conventions. Access patterns include direct property access through relationship names, automatic cast application for data type conversion, and lazy loading optimization to prevent unnecessary database queries.

```php
/**
 * Access extension data through various naming conventions.
 * All variants resolve to the same extension model instance.
 */
$developer = Auth::user()->developer_profile;  // snake_case
$developer = Auth::user()->developerProfile;   // camelCase  
$developer = Auth::user()->developerprofile;   // lowercase

/**
 * Cast attributes are automatically applied during access.
 * No manual data conversion required.
 */
echo $developer->github_username;        // String value
$skills = $developer->skills;            // Array (cast from JSON)
$active = $developer->is_active;         // Boolean value
$experience = $developer->experience_years; // Integer value
```

## Advanced Implementation Considerations

### Cast Behavior and Data Transformation

Extension models maintain full Eloquent casting capabilities with automatic application during data access. Cast definitions in extension models apply seamlessly when accessing data through User model relationships, ensuring consistent data type handling across all access patterns.

Cast behavior includes automatic JSON to array conversion, date string to Carbon instance transformation, integer and boolean type casting, and custom cast class application. All cast operations occur transparently during property access regardless of the access method used.

The system preserves cast functionality when accessing extensions through magic properties, direct relationship queries, and eager-loaded relationship data. Custom cast classes defined in extension models function normally and receive proper attribute values for transformation.

### Fillable Attributes and Mass Assignment

Extension model fillable attributes function identically to standard Eloquent models with important considerations regarding the relationship between fillable properties and handled attributes. The getHandledAttributes method should typically return a subset of fillable attributes excluding relationship keys.

Mass assignment protection applies normally to extension model operations. Attributes specified in create and updateOrCreate operations must be included in the model fillable array to prevent mass assignment violations. The system does not bypass standard Laravel mass assignment protection mechanisms.

Critical implementation guidelines include ensuring getHandledAttributes returns attributes also present in fillable arrays, excluding user_id from handled attributes since it represents a relationship rather than extension data, and implementing proper validation for mass assignment scenarios.

### Model Events and Lifecycle Management

Extension models support complete Eloquent model event functionality including creating, created, updating, updated, saving, saved, deleting, and deleted events. These events fire independently for each extension model and do not interfere with User model events.

Model events provide opportunities for data validation, automatic field population, relationship management, and audit trail creation. Event handlers can access full model context including relationship data and attribute changes.

```php
/**
 * Extension model with comprehensive event handling.
 * Demonstrates lifecycle management capabilities.
 */
class Subscription extends Model
{
    /**
     * Boot method for model event registration.
     * Handles automatic field population and validation.
     */
    protected static function boot()
    {
        parent::boot();
        
        /**
         * Set activation timestamp during creation.
         * Ensures consistent data initialization.
         */
        static::creating(function (Subscription $subscription) {
            $subscription->activated_at = now();
            $subscription->status = 'active';
        });
        
        /**
         * Log subscription modifications for audit purposes.
         * Provides comprehensive change tracking.
         */
        static::updated(function (Subscription $subscription) {
            Log::info('Subscription updated', [
                'user_id' => $subscription->user_id,
                'changes' => $subscription->getChanges(),
                'timestamp' => now()
            ]);
        });
        
        /**
         * Handle cleanup operations during deletion.
         * Maintains data integrity across related systems.
         */
        static::deleting(function (Subscription $subscription) {
            $subscription->user->update(['subscription_active' => false]);
        });
    }
}
```

### Relationships and Complex Data Structures

Extension models support full relationship functionality including hasMany, belongsTo, hasOne, and belongsToMany relationships. These relationships operate independently of User model relationships and can reference any application models.

Relationships within extension models provide opportunities for complex data modeling including hierarchical data structures, many-to-many associations, and polymorphic relationships. The system maintains full Eloquent relationship functionality without restrictions.

```php
/**
 * Extension model with complex relationships.
 * Demonstrates advanced data modeling capabilities.
 */
class DeveloperProfile extends Model
{
    /**
     * Projects associated with this developer.
     * Demonstrates hasMany relationship within extension.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Project>
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'developer_id');
    }
    
    /**
     * Primary programming language preference.
     * Demonstrates belongsTo relationship within extension.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ProgrammingLanguage, DeveloperProfile>
     */
    public function primaryLanguage()
    {
        return $this->belongsTo(ProgrammingLanguage::class, 'primary_language_id');
    }
    
    /**
     * Skills associated with this developer profile.
     * Demonstrates many-to-many relationship functionality.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Skill>
     */
    public function skillsRelation()
    {
        return $this->belongsToMany(Skill::class, 'developer_skills')
                    ->withPivot(['proficiency_level', 'years_experience'])
                    ->withTimestamps();
    }
}

/**
 * Usage examples for extension relationships.
 * Demonstrates relationship access through User model.
 */
$projects = Auth::user()->developer->projects()->where('status', 'active')->get();
$language = Auth::user()->developer->primaryLanguage;
$skills = Auth::user()->developer->skillsRelation()->wherePivot('proficiency_level', '>=', 8)->get();
```

### Scope Functionality and Query Modification

Local and global scopes defined on extension models apply when querying extension models directly but do not automatically apply when accessing extensions through User model relationships. This distinction is crucial for understanding when explicit extension model queries are necessary.

Scope application requires direct extension model querying rather than accessing through User model magic properties. This behavior ensures predictable query results and prevents unexpected filtering when accessing extension data through relationships.

```php
/**
 * Extension model with comprehensive scope definitions.
 * Demonstrates query modification capabilities.
 */
class Subscription extends Model
{
    /**
     * Scope for filtering active subscriptions.
     * Applies to direct model queries only.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Subscription> $query
     * @return \Illuminate\Database\Eloquent\Builder<Subscription>
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }
    
    /**
     * Scope for subscriptions expiring within specified days.
     * Provides flexible expiration filtering.
     *
     * @param \Illuminate\Database\Eloquent\Builder<Subscription> $query
     * @param int $days Number of days for expiration check
     * @return \Illuminate\Database\Eloquent\Builder<Subscription>
     */
    public function scopeExpiringWithin($query, int $days = 7)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
                    ->where('expires_at', '>', now());
    }
}

/**
 * Scope usage patterns and limitations.
 * Demonstrates when scopes apply and when they don't.
 */

// Scopes apply when querying extension models directly
$activeSubscriptions = Subscription::active()->get();
$expiring = Subscription::expiringWithin(14)->get();

// Scopes do NOT apply when accessing through User relationships
$subscription = Auth::user()->subscription; // No scopes applied automatically

// To use scopes with User relationships, query the extension directly
$activeUserSubscription = Auth::user()->extensionRelation(Subscription::class)->active()->first();
```

### Attribute Mutators and Accessors

Mutators and accessors defined in extension models function normally when interacting with extension data through any access method. Attribute transformation occurs transparently during property access and assignment regardless of whether data is accessed through User model relationships or direct extension model queries.

Accessor functionality includes computed attribute generation, data formatting and presentation, related data aggregation, and conditional value transformation. Mutator capabilities encompass data normalization, validation and sanitization, format conversion, and automatic field population.

```php
/**
 * Extension model with comprehensive attribute transformation.
 * Demonstrates mutator and accessor functionality.
 */
class UserProfile extends Model
{
    /**
     * Accessor for computed full name attribute.
     * Combines first and last name fields automatically.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    /**
     * Accessor for formatted phone number display.
     * Provides consistent phone number presentation.
     *
     * @return string|null
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $this->phone);
    }
    
    /**
     * Mutator for email address normalization.
     * Ensures consistent email format storage.
     *
     * @param string|null $value
     * @return void
     */
    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }
    
    /**
     * Mutator for phone number standardization.
     * Removes formatting characters for consistent storage.
     *
     * @param string|null $value
     * @return void
     */
    public function setPhoneAttribute(?string $value): void
    {
        if ($value) {
            $this->attributes['phone'] = preg_replace('/\D/', '', $value);
        } else {
            $this->attributes['phone'] = null;
        }
    }
}

/**
 * Usage examples demonstrating automatic attribute transformation.
 * Mutators and accessors work through all access methods.
 */
echo Auth::user()->profile->full_name;        // Accessor applied automatically
echo Auth::user()->profile->formatted_phone;  // Formatting applied transparently

Auth::user()->extension()->create([
    'email' => '  USER@EXAMPLE.COM  ',  // Stored as 'user@example.com'
    'phone' => '(555) 123-4567'         // Stored as '5551234567'
]);
```

### Performance Optimization and Query Management

The system implements comprehensive performance optimizations including extension manager instance caching, relationship result caching with automatic invalidation, and intelligent query prevention through relationship loading status verification.

Performance considerations include N+1 query prevention through eager loading strategies, extension map caching per request cycle, relationship mapping optimization, and efficient memory usage patterns. The system maintains Laravel Octane compatibility through careful state management.

```php
/**
 * Performance optimization strategies for extension system usage.
 * Demonstrates efficient query patterns and caching approaches.
 */

/**
 * Inefficient pattern - causes N+1 query problems.
 * Each user access triggers separate extension queries.
 */
$users = User::all();
foreach ($users as $user) {
    echo $user->developer->github_username ?? 'No GitHub';  // Separate query per user
    echo $user->profile->full_name ?? 'No Profile';         // Another query per user
}

/**
 * Efficient pattern - uses eager loading to minimize queries.
 * Pre-loads all extension relationships in single queries.
 */
$users = User::with(['developer', 'profile'])->get();
foreach ($users as $user) {
    echo $user->developer?->github_username ?? 'No GitHub';  // No additional queries
    echo $user->profile?->full_name ?? 'No Profile';         // No additional queries
}

/**
 * Advanced eager loading with constraints.
 * Combines relationship loading with filtering for optimal performance.
 */
$activeUsers = User::with([
    'developer' => function ($query) {
        $query->where('is_active', true)->with('projects');
    },
    'subscription' => function ($query) {
        $query->where('status', 'active');
    }
])->get();

/**
 * Conditional relationship loading based on user attributes.
 * Prevents unnecessary queries for users without specific extensions.
 */
$users = User::when($needsDeveloperData, function ($query) {
    return $query->with('developer');
})->when($needsProfileData, function ($query) {
    return $query->with('profile');
})->get();
```

### Validation Implementation Strategies

Extension system validation requires application-level implementation since extension models are selected dynamically based on provided attributes. Validation strategies include attribute-based validation rule selection, extension model capability verification, and comprehensive error handling for invalid data scenarios.

Validation implementation patterns encompass pre-creation validation checks, dynamic rule application based on detected extension models, and integration with Laravel validation infrastructure. The system supports both manual validation approaches and automated validation through form request classes.

```php
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Form request class for extension data validation.
 * Demonstrates dynamic validation based on extension model detection.
 */
class UpdateExtensionRequest extends FormRequest
{
    /**
     * Validation rules mapped by extension class names.
     * Provides type-specific validation for different extension models.
     *
     * @var array<class-string, array<string, mixed>>
     */
    protected array $extensionRules = [
        'App\\Models\\Developer' => [
            'github_username' => 'required|string|max:255|unique:developers,github_username',
            'preferred_language' => 'required|string|max:100',
            'experience_years' => 'required|integer|min:0|max:50',
            'skills' => 'required|array|min:1',
            'skills.*' => 'string|max:100',
            'is_active' => 'boolean'
        ],
        'App\\Models\\Profile' => [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar_url' => 'nullable|url|max:500',
            'phone' => 'nullable|string|regex:/^\d{10}$/'
        ],
        'App\\Models\\Subscription' => [
            'plan_name' => 'required|string|max:100',
            'billing_cycle' => 'required|in:monthly,yearly',
            'amount' => 'required|numeric|min:0',
            'expires_at' => 'required|date|after:now'
        ]
    ];
    
    /**
     * Get validation rules based on detected extension model.
     * Dynamically applies appropriate validation for provided attributes.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->user();
        $extensionManager = $user->extension();
        $extensionClass = $extensionManager->findExtensionByAttributes($this->all());
        
        if (!$extensionClass) {
            throw ValidationException::withMessages([
                'attributes' => ['No valid extension model found for provided attributes.']
            ]);
        }
        
        return $this->extensionRules[$extensionClass] ?? [];
    }
    
    /**
     * Custom validation messages for extension-specific rules.
     * Provides user-friendly error messages for validation failures.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'github_username.unique' => 'This GitHub username is already registered.',
            'skills.min' => 'At least one skill must be specified.',
            'phone.regex' => 'Phone number must be exactly 10 digits.',
            'expires_at.after' => 'Subscription expiration must be in the future.'
        ];
    }
}
```

### Database Transaction Management

Extension model operations occur independently of User model transactions unless explicitly wrapped in transaction boundaries. Transaction management becomes critical when performing operations that affect both User and extension data to maintain consistency.

Transaction strategies include wrapping related operations in database transactions, implementing rollback mechanisms for failed operations, and ensuring atomic updates across multiple extension models. The system supports both automatic and manual transaction management approaches.

```php
/**
 * Transaction management examples for extension operations.
 * Demonstrates proper handling of multi-model updates.
 */
use Illuminate\Support\Facades\DB;

/**
 * Atomic user and extension update within transaction boundary.
 * Ensures data consistency across related models.
 */
DB::transaction(function () {
    $user = Auth::user();
    
    /**
     * Update user model with timestamp tracking.
     * Maintains audit trail for profile modifications.
     */
    $user->update([
        'last_profile_update' => now(),
        'profile_completion_status' => 'in_progress'
    ]);
    
    /**
     * Create or update extension data atomically.
     * Rollback occurs automatically if extension operation fails.
     */
    $user->extension()->updateOrCreate([
        'bio' => 'Updated professional biography',
        'phone' => '+1234567890',
        'linkedin_url' => 'https://linkedin.com/in/username'
    ]);
    
    /**
     * Update completion status after successful extension update.
     * Demonstrates multi-step transaction with conditional logic.
     */
    $user->update(['profile_completion_status' => 'completed']);
});

/**
 * Complex transaction with multiple extension models.
 * Handles scenarios where multiple extensions require coordinated updates.
 */
DB::transaction(function () {
    $user = Auth::user();
    
    try {
        /**
         * Update developer profile information.
         * Includes technical skills and experience data.
         */
        $user->extension()->updateOrCreate([
            'github_username' => 'updated_username',
            'preferred_language' => 'TypeScript',
            'skills' => ['React', 'Node.js', 'PostgreSQL']
        ]);
        
        /**
         * Update subscription information.
         * Coordinates billing and access level changes.
         */
        $user->extension()->updateOrCreate([
            'plan_name' => 'professional',
            'billing_cycle' => 'yearly',
            'amount' => 299.99,
            'expires_at' => now()->addYear()
        ]);
        
        /**
         * Log successful coordinated update.
         * Provides audit trail for complex operations.
         */
        Log::info('User extensions updated successfully', [
            'user_id' => $user->id,
            'timestamp' => now(),
            'extensions_updated' => ['developer', 'subscription']
        ]);
        
    } catch (\Exception $e) {
        /**
         * Log transaction failure for debugging.
         * Transaction rollback occurs automatically.
         */
        Log::error('Extension update transaction failed', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        throw $e;
    }
});
```

## Critical System Limitations and Behavioral Considerations

### Extension Model Isolation and Data Integrity

Extension models operate as completely independent Eloquent models with separate database tables, lifecycle events, and behavioral patterns. This architectural isolation provides significant flexibility for data modeling but introduces specific considerations for data integrity and consistency management.

The most critical consideration relates to referential integrity between User and extension data. Since extensions are stored in separate tables, database constraints must be carefully implemented to maintain data consistency. Foreign key constraints should be configured at the database level with appropriate cascading behavior for deletion scenarios.

Extension model isolation means that standard Eloquent relationship features like cascading updates and automatic cleanup do not apply across the User-extension boundary. Application-level logic must handle scenarios where User records are deleted but extension records remain, or where extension data becomes orphaned due to system failures.

### Magic Property Access Behavioral Limitations

The magic property access functionality provides intuitive syntax for extension data retrieval but has behavioral limitations that differ significantly from native Eloquent relationships. Magic property access performs fresh database queries each time unless relationships have been explicitly eager loaded, which can lead to performance issues in data-intensive applications.

Magic property access returns null when extension records do not exist, rather than providing methods to create or manipulate relationships. This behavior differs from standard Eloquent relationship access patterns and may require adjustment of existing code patterns that rely on relationship method availability.

The magic property system does not support relationship method chaining or query modification. Operations beyond simple data retrieval require explicit use of the extension manager interface or direct extension model queries, limiting the intuitive API surface for complex operations.

### Attribute Conflict Resolution and Model Selection

When multiple extension models define overlapping attributes in their getHandledAttributes methods, the system selects the first matching extension based on the order returned by ArcaneBuildModule configuration. This selection behavior can lead to unexpected results when extension models have conflicting attribute definitions or when model registration order changes.

Attribute conflict resolution does not include priority mechanisms or sophisticated matching algorithms. The first-match selection pattern means that extension model registration order becomes critical for system behavior, requiring careful documentation and maintenance of ArcaneBuildModule configuration.

To prevent conflicts and ensure predictable behavior, extension models should handle distinct sets of attributes whenever possible. When attribute overlap is intentional, explicit priority documentation and testing procedures should be implemented to verify expected model selection behavior.

### Migration and Schema Design Requirements

Extension tables require careful schema design to support the relationship patterns and performance characteristics of the extension system. Each extension table must include a properly configured user_id foreign key column with appropriate constraints, indexing, and cascading behavior for optimal operation.

Database schema design must account for the fact that the extension system does not automatically handle cleanup of extension records when User records are deleted. Implementing appropriate database-level cascading rules or application-level event handlers becomes essential for maintaining data consistency and preventing orphaned records.

Index design for extension tables should consider the query patterns used by the extension system, particularly for user_id foreign key lookups and attribute-based filtering operations. Performance optimization may require additional indexes based on specific application usage patterns and query complexity.

### Performance Characteristics and Scaling Considerations

The extension system builds extension maps and relationship maps once per request cycle to maintain Laravel Octane compatibility and optimize performance. However, the map building process requires loading and analyzing all configured extension models, which can impact application startup time as the number of extensions grows.

Extension relationship access through magic properties utilizes lazy loading by default, which can lead to N+1 query problems when working with collections of users that have extensions. Application developers must implement explicit eager loading strategies to maintain acceptable performance characteristics in data-intensive scenarios.

The system maintains relationship caching at the User model instance level to prevent duplicate queries within a single request cycle. However, this caching does not persist across request boundaries, and applications with complex extension access patterns may benefit from additional caching strategies at the application level.

System scaling considerations include the overhead of extension map building for large numbers of extension models, memory usage patterns for relationship caching with large user datasets, and database query optimization for extension table joins and filtering operations. Performance testing should include scenarios with realistic numbers of users and extensions to verify acceptable scaling characteristics.

This comprehensive documentation provides complete coverage of the enhanced User Extensions System, including implementation details, usage patterns, performance considerations, and critical limitations. The system maintains simplicity while providing robust functionality for complex application scenarios requiring flexible user data extension capabilities.
     # User Extensions System (Enhanced)

A simple, scalable system for extending User model with related data without polluting the main User model. This enhanced version supports extension models in any namespace while maintaining simplicity and accuracy.

## Overview

This system allows you to:
- Automatically detect which extension model to use based on attributes
- Create and update extension records with minimal code
- Keep User model clean with just one trait
- Scale infinitely by adding new extension models in any namespace
- Access extensions through intuitive relationship names

## Core Components

### 1. Enhanced ExtensionManager
- Handles creation and updates of extension records across any namespace
- Automatically detects correct model based on attributes
- Provides intelligent relationship name mapping for property access
- Uses ArcaneBuildModule for extension discovery
- Supports multiple naming conventions (snake_case, camelCase, lowercase)

### 2. UserExtensionsRelationsTrait
- Single trait added to User model
- Provides `extension()` method and magic property access
- Intelligent namespace-agnostic model resolution
- Minimal impact on User model

### 3. Extension Models
- Standard Eloquent models with `getHandledAttributes()` method
- Define which attributes they handle
- Can exist in any namespace (App\Models, App\Extensions, etc.)
- Support casts, fillable, and model events

## Installation

1. Add trait to User model:
```php
use Webkernel\Aptitudes\Users\UserExtensions\Traits\UserExtensionsRelationsTrait;

class User extends Authenticatable
{
    use UserExtensionsRelationsTrait;
    
    // ... rest of your User model
}
```

2. Create extension models with `getHandledAttributes()` method in any namespace
3. Configure ArcaneBuildModule to return array of extension class names with full namespaces

## Usage

### Create Extension Record
```php
// Automatically detects correct model based on 'github_username' attribute
// Works regardless of namespace (App\Models\Developer, App\Extensions\Developer, etc.)
Auth::user()->extension()->create([
    'github_username' => 'numerimondes',
    'preferred_language' => 'PHP'
]);
```

### Update or Create
```php
Auth::user()->extension()->updateOrCreate([
    'github_username' => 'new_username'
]);
```

### Access Extension Data
```php
// Magic property access works with multiple naming conventions
$developer = Auth::user()->developer;        // snake_case
$developer = Auth::user()->developerProfile; // camelCase
echo $developer->github_username;

// Array cast works automatically
print_r($developer->skills);
```

## Adding New Extensions

1. Create migration for the extension table
2. Create model with `getHandledAttributes()` method in any namespace:

```php
<?php
namespace App\Extensions; // Any namespace works

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = ['bio', 'avatar_url', 'phone', 'user_id'];
    
    public static function getHandledAttributes(): array
    {
        return ['bio', 'avatar_url', 'phone'];
    }
    
    // ... rest of model
}
```

3. Update ArcaneBuildModule to include the new extension class
4. Access via multiple naming conventions: `user_profile`, `userProfile`, or `userprofile`

## ArcaneBuildModule Integration

The `allUserExtensions()` method should return an array of fully qualified class names from any namespace:

```php
public function allUserExtensions(): array
{
    return [
        'App\\Models\\Developer',
        'App\\Extensions\\UserProfile',
        'My\\Custom\\Namespace\\Subscription',
        'Vendor\\Package\\Models\\ExternalExtension',
        // ... more extensions from any namespace
    ];
}
```

## Enhanced Features

### Namespace Flexibility
- Extension models can exist in any namespace
- Automatic class name extraction for relationship mapping
- Support for complex namespace structures

### Intelligent Property Access
- Multiple naming convention support (snake_case, camelCase, lowercase)
- Automatic relationship name resolution
- Fallback to parent model behavior when extension not found

### Improved Error Handling
- Clear exceptions for missing extensions with detailed attribute information
- Logging for unmatched attributes and missing classes
- Validation for required methods on extension classes

### Performance Optimizations
- Extension map built once per request (Octane compatible)
- Relationship map cached for fast property access
- Lazy loading of extension relationships
- Minimal overhead on User model

## Error Handling

The system provides comprehensive error handling:

**InvalidArgumentException thrown when:**
- No extension model handles the provided attributes
- Extension model class doesn't exist
- Extension model doesn't have `getHandledAttributes()` method

**Warning logs generated for:**
- Unmatched attributes during creation
- Missing extension classes referenced in ArcaneBuildModule
- Extension classes lacking required methods

## File Structure

```
app/
├── Models/
│   ├── User.php (minimal changes)
│   └── Developer.php
├── Extensions/
│   ├── UserProfile.php
│   └── Subscription.php
└── ...

vendor/package/Models/
└── ExternalExtension.php

Webkernel/Aptitudes/
├── Users/
│   ├── ExtensionManager.php
│   └── Traits/
│       └── UserExtensionsRelationsTrait.php
└── ...
```

## Configuration Examples

### Multi-Namespace Extension Setup
```php
// ArcaneBuildModule configuration
public function allUserExtensions(): array
{
    return [
        // Core application extensions
        'App\\Models\\Developer',
        'App\\Models\\Profile',
        
        // Dedicated extensions namespace
        'App\\Extensions\\Subscription',
        'App\\Extensions\\Preferences',
        
        // Third-party package extensions
        'Vendor\\CRM\\Models\\CustomerData',
        'External\\Analytics\\UserTracking',
        
        // Custom business logic extensions
        'Company\\Modules\\HR\\EmployeeDetails',
    ];
}
```

### Extension Model Template
```php
<?php
namespace Your\Chosen\Namespace;

use Illuminate\Database\Eloquent\Model;

class YourExtension extends Model
{
    protected $fillable = ['attribute1', 'attribute2', 'user_id'];
    
    protected $casts = [
        'json_field' => 'array',
        'date_field' => 'datetime',
    ];
    
    public static function getHandledAttributes(): array
    {
        return ['attribute1', 'attribute2'];
    }
    
    // Standard Eloquent model methods and relationships
}
```

## Critical Behavioral Notes and Limitations

### Extension Model Isolation

Extension models operate as completely independent Eloquent models with their own database tables, lifecycle events, and behavioral patterns. This isolation provides flexibility but introduces specific considerations that must be understood for proper implementation.

The most important consideration relates to data integrity and consistency. Since extensions are stored in separate tables, database constraints between User and extension data must be carefully managed. Foreign key constraints should be implemented at the database level to maintain referential integrity, and cascading deletion behavior must be explicitly configured.

### Magic Property Access Limitations  

The magic property access provided by the trait offers convenient syntax but has behavioral limitations that differ from native Eloquent relationships. When accessing extension data through magic properties, the system performs a fresh database query each time unless the relationship has been explicitly loaded.

Additionally, magic property access returns null when an extension record does not exist, rather than providing methods to create or manipulate the relationship. For operations beyond simple data retrieval, the explicit extension manager interface must be used.

### Attribute Conflict Resolution

When multiple extension models handle overlapping attributes, the system selects the first matching extension based on the order returned by ArcaneBuildModule. This behavior can lead to unexpected results if extension models have conflicting attribute definitions.

To prevent conflicts, ensure that extension models handle distinct sets of attributes, or implement explicit priority logic within the ArcaneBuildModule configuration. Document any intentional attribute overlaps and their resolution priority for future maintenance.

### Migration and Schema Considerations

Extension tables require careful schema design to support the relationship patterns used by this system. Each extension table must include a user_id foreign key column with appropriate constraints and indexing for optimal performance.

Consider implementing database-level cascading rules for deletion scenarios, as the system does not automatically handle cleanup of extension records when User records are deleted. Implement appropriate database triggers or application-level event handlers to maintain data consistency.
