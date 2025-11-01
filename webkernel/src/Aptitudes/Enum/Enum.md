# Global Enum System

A minimalist, powerful enum system for Laravel that eliminates boilerplate code and provides automatic validation, querying, and form integration.

## Philosophy

**ZERO BOILERPLATE**: Developers should write almost no enum-related code but get full enum functionality.

## 🚀 **Ultra-Simple Usage**

### 1. **Minimal Model Configuration**

```php
class WebsiteProject extends Model
{
    use HasGlobalEnumTrait;
    
    // ONLY configuration needed
    protected array $enumTypes = [
        'status_id' => 'website_project_status',
        'type_id' => 'website_project_type',
    ];
}
```

### 2. **Usage in Views/Templates**

```php
// ✅ ULTRA SIMPLE - Shortcut methods
$project->getEnumLabel('status_id');     // "Draft"
$project->getEnumIcon('status_id');      // "heroicon-o-document-text"
$project->getEnumCssClass('status_id');  // "text-gray-500"
$project->isEnum('status_id', 'active'); // true/false

// ✅ ULTRA SIMPLE - Automatic accessors
$project->status_enum->default_label;    // "Draft"
$project->type_enum->icon;               // "heroicon-o-building-office"
$project->status_enum->css_class;        // "text-gray-500"
```

### 3. **Usage in Filament (Tables/Forms)**

```php
// ✅ Table - Simplified display
TextColumn::make('status_id')
    ->formatStateUsing(fn ($record) => $record->getEnumLabel('status_id'))
    ->color(fn ($record) => $record->getEnumCssClass('status_id'))
    ->icon(fn ($record) => $record->getEnumIcon('status_id')),

// ✅ Form - Automatic options
Select::make('status_id')
    ->options(fn () => (new WebsiteProject())->getEnumOptions('status_id'))
```

### 4. **Usage in Blade**

```blade
<!-- ✅ ULTRA SIMPLE -->
<div class="badge {{ $project->getEnumCssClass('status_id') }}">
    <i class="{{ $project->getEnumIcon('status_id') }}"></i>
    {{ $project->getEnumLabel('status_id') }}
</div>

<!-- ✅ Or with automatic accessor -->
<div class="badge {{ $project->status_enum->css_class }}">
    <i class="{{ $project->status_enum->icon }}"></i>
    {{ $project->status_enum->default_label }}
</div>
```

### 5. **Simplification Benefits**

#### **Before (Complex)**
```php
// ❌ Old system - too much code
$project->status_enum?->getLabel()
$project->status_enum?->css_class
$project->status_enum?->icon

// ❌ Manual accessors to create
public function getStatusEnumAttribute() {
    return $this->status_id ? GlobalEnum::find($this->status_id) : null;
}
```

#### **After (Ultra-Simple)**
```php
// ✅ New system - one line
$project->getEnumLabel('status_id')
$project->getEnumCssClass('status_id')
$project->getEnumIcon('status_id')

// ✅ Automatic accessors
$project->status_enum->default_label
$project->status_enum->css_class
$project->status_enum->icon
```

### 6. **Result**

- **1 line** of configuration per enum
- **1 method** for each use case
- **0 manual accessors** to create
- **100% automatic** for common cases

**The system is now truly "ZERO BOILERPLATE"!** 🚀

## Quick Start

### 1. Add the trait to your model

```php
use Webkernel\Aptitudes\Enum\Traits\HasGlobalEnum;

class Company extends Model
{
    use HasGlobalEnum;
    
    // ONLY thing needed: map your fields to enum types
    protected array $enumTypes = [
        'company_type_id' => 'nm_company_type',
        'status_id' => 'nm_company_status',
    ];
}
```

### 2. Use enum functionality immediately

```php
$company = Company::find(1);

// Get enum data
$icon = $company->enum('company_type_id', 'icon');
$label = $company->enum('status_id', 'label');

// Validation
$isActive = $company->enumIs('status_id', 'active');

// Form options
$statusOptions = $company->getEnumOptions('status_id');

// Queries
$activeCompanies = Company::whereEnum('status_id', 'active')->get();
```

## Core Components

### GlobalEnum Model

Central model that stores all enum data in `apt_global_enums` table.

#### Key Fields

- `type`: Enum category (e.g., 'nm_company_type')
- `key`: Enum value (e.g., 'llc', 'active')
- `default_label`: Display label
- `label_key`: Translation key (optional)
- `icon`: Icon identifier
- `css_class`: CSS styling classes
- `sort_order`: Display ordering
- `metadata`: JSON additional data
- `parent_id`: For hierarchical enums

### HasGlobalEnum Trait

Provides all enum functionality to models with minimal configuration.

#### Required Configuration

```php
protected array $enumTypes = [
    'field_name' => 'enum_type',
    // Examples:
    'status_id' => 'company_status',
    'priority_id' => 'priority',
    'category_id' => 'product_category',
];
```

## API Reference

### Model Instance Methods

#### `enum(string $field, ?string $requesting = null)`

Get enum data for a field.

```php
// Get full enum object
$enum = $company->enum('status_id');

// Get specific field
$icon = $company->enum('status_id', 'icon');
$label = $company->enum('status_id', 'label');
$cssClass = $company->enum('status_id', 'css_class');
```

#### `getEnumOptions(string $field, bool $hierarchical = false)`

Get options array for dropdowns.

```php
$options = $company->getEnumOptions('status_id');
// Returns: ['active' => 'Active', 'inactive' => 'Inactive']

$hierarchical = $company->getEnumOptions('category_id', true);
// Returns: ['parent' => 'Parent', 'child' => '-- Child']
```

#### `isValidEnum(string $field, string $value)`

Check if enum value is valid.

```php
$isValid = $company->isValidEnum('status_id', 'active'); // true
$isValid = $company->isValidEnum('status_id', 'invalid'); // false
```

#### `setEnum(string $field, string $value, bool $validate = true)`

Set enum value with optional validation.

```php
$company->setEnum('status_id', 'inactive'); // OK
$company->setEnum('status_id', 'invalid');  // Throws exception
$company->setEnum('status_id', 'invalid', false); // No validation
```

#### `enumIs(string $field, string $value)`

Check if enum field matches value.

```php
$isActive = $company->enumIs('status_id', 'active');
```

#### `enumIn(string $field, array $values)`

Check if enum field is in array of values.

```php
$isActiveOrSuspended = $company->enumIn('status_id', ['active', 'suspended']);
```

#### `getEnumValidationRules()`

Get Laravel validation rules for all enum fields.

```php
$rules = $company->getEnumValidationRules();
// Returns: [
//     'status_id' => ['nullable', 'string', 'in:active,inactive,suspended'],
//     'company_type_id' => ['nullable', 'string', 'in:individual,llc,corporation']
// ]
```

### Ultra-Simple Shortcut Methods

#### `getEnumLabel(string $field)`

Get enum label for a field.

```php
$label = $company->getEnumLabel('status_id'); // "Active"
```

#### `getEnumIcon(string $field)`

Get enum icon for a field.

```php
$icon = $company->getEnumIcon('status_id'); // "heroicon-o-check-circle"
```

#### `getEnumCssClass(string $field)`

Get enum CSS class for a field.

```php
$cssClass = $company->getEnumCssClass('status_id'); // "text-green-500"
```

#### `isEnum(string $field, string $value)`

Check if enum field has specific value.

```php
$isActive = $company->isEnum('status_id', 'active'); // true/false
```

### Automatic Accessors

The trait automatically creates accessors for all enum fields:

```php
// Automatic accessors (no manual code needed)
$company->status_enum->default_label;    // "Active"
$company->status_enum->icon;             // "heroicon-o-check-circle"
$company->status_enum->css_class;        // "text-green-500"
$company->type_enum->default_label;      // "LLC"
```

### Query Scopes

#### `scopeWhereEnum($query, string $field, string $value)`

Filter by enum value.

```php
Company::whereEnum('status_id', 'active')->get();
```

#### `scopeWhereEnumIn($query, string $field, array $values)`

Filter by multiple enum values.

```php
Company::whereEnumIn('status_id', ['active', 'suspended'])->get();
```

### Static GlobalEnum Methods

#### `GlobalEnum::get(string $type, ?string $id = null, ?string $requesting = null)`

Main method to get enum data with automatic type resolution.

```php
// Auto-resolves field name to enum type
$icon = GlobalEnum::get('company_type_id', 'llc', 'icon');

// Direct enum type usage
$label = GlobalEnum::get('nm_company_type', 'llc', 'label');

// Get all enums of a type
$allTypes = GlobalEnum::get('nm_company_type');
```

#### `GlobalEnum::options(string $type)`

Get key-value options for dropdowns.

```php
$options = GlobalEnum::options('nm_company_status');
// Returns: ['active' => 'Active', 'inactive' => 'Inactive']
```

#### `GlobalEnum::hierarchicalOptions(string $type)`

Get hierarchical options with parent grouping.

```php
$options = GlobalEnum::hierarchicalOptions('category');
// Returns: ['parent' => 'Parent', 'child' => '-- Child']
```

## Database Schema

### apt_global_enums Table

```sql
CREATE TABLE apt_global_enums (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,           -- Enum category
    key VARCHAR(50) NOT NULL,            -- Enum value
    label_key VARCHAR(100) NOT NULL,     -- Translation key
    default_label VARCHAR(100) NOT NULL, -- Default display label
    description_key VARCHAR(100),        -- Optional description translation key
    icon VARCHAR(50),                    -- Icon identifier
    css_class VARCHAR(50),               -- CSS classes
    sort_order INT DEFAULT 0,            -- Display order
    is_active BOOLEAN DEFAULT TRUE,      -- Enable/disable
    parent_id BIGINT,                    -- For hierarchical enums
    metadata JSON,                       -- Additional data
    contexts JSON,                       -- Context restrictions
    model_type VARCHAR(100),             -- Related model
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE(type, key),
    INDEX(type, is_active, sort_order)
);
```

### Model Tables

Store enum values as strings (keys), not foreign keys:

```sql
CREATE TABLE nm_core_companies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_type_id VARCHAR(50) NOT NULL,  -- Stores 'llc', 'corporation', etc.
    status_id VARCHAR(50) NOT NULL,        -- Stores 'active', 'inactive', etc.
    -- other fields...
);
```

## Adding New Enums

### 1. In Migration

```php
private function addEnumData(): void
{
    $enums = [
        [
            'type' => 'product_status',
            'key' => 'published',
            'label_key' => 'enum_product_status_published',
            'default_label' => 'Published',
            'icon' => 'eye',
            'css_class' => 'text-success',
            'sort_order' => 1,
            'is_active' => true,
        ],
        // Add more...
    ];
    
    foreach ($enums as $enum) {
        DB::table('apt_global_enums')->insertOrIgnore($enum);
    }
}
```

### 2. In Model

```php
class Product extends Model
{
    use HasGlobalEnum;
    
    protected array $enumTypes = [
        'status_id' => 'product_status',
        'category_id' => 'product_category',
    ];
}
```

That's it! All enum functionality is now available.

## Advanced Features

### Hierarchical Enums

Create parent-child relationships:

```php
// Parent category
[
    'type' => 'product_category',
    'key' => 'electronics',
    'default_label' => 'Electronics',
    'parent_id' => null,
]

// Child category
[
    'type' => 'product_category',
    'key' => 'smartphones',
    'default_label' => 'Smartphones',
    'parent_id' => 1, // ID of electronics category
]
```

### Metadata

Store additional data:

```php
[
    'type' => 'priority',
    'key' => 'urgent',
    'default_label' => 'Urgent',
    'metadata' => ['sla_hours' => 4, 'color' => '#ff0000'],
]

// Access metadata
$slaHours = $enum->getMeta('sla_hours', 24); // Default 24 if not found
```

### Context Restrictions

Limit enums to specific contexts:

```php
[
    'type' => 'status',
    'key' => 'draft',
    'default_label' => 'Draft',
    'contexts' => ['product', 'article'], // Only for products and articles
]

// Check if valid for context
$isValid = $enum->isValidForContext('product'); // true
```

### Localization

Support multiple languages:

```php
[
    'type' => 'status',
    'key' => 'active',
    'label_key' => 'enum.status.active',      // Translation key
    'default_label' => 'Active',              // Fallback
    'description_key' => 'enum.status.active.desc',
]

// In lang/en/enum.php
'status' => [
    'active' => 'Active',
    'active.desc' => 'Item is currently active',
],

// Usage - automatically uses translation if available
$label = $enum->getLabel(); // Returns translated version or default_label
```

## Form Integration

### Laravel Form Requests

```php
class CompanyRequest extends FormRequest
{
    public function rules()
    {
        $company = new Company();
        
        return array_merge([
            'legal_name' => 'required|string|max:255',
            'email' => 'required|email',
        ], $company->getEnumValidationRules());
    }
}
```

### Blade Templates

```blade
@php
    $company = new \Numerimondes\Numerimondes\Models\NMCoreCompanies();
@endphp

<h3>Company Status Enums:</h3>
<select name="status_id" class="form-control">
    @foreach($company->getEnumOptions('status_id') as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
    @endforeach
</select>

<h3>Company Type Enums:</h3>
<select name="company_type_id" class="form-control">
    @foreach($company->getEnumOptions('company_type_id') as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
    @endforeach
</select>
```

## API Serialization

### Custom Attributes

```php
class Company extends Model
{
    use HasGlobalEnum;
    
    protected $appends = ['status_data', 'company_type_data'];
    
    public function getStatusDataAttribute(): ?array
    {
        $enum = $this->enum('status_id');
        return $enum ? [
            'key' => $enum->key,
            'label' => $enum->getLabel(),
            'icon' => $enum->icon,
            'css_class' => $enum->css_class,
        ] : null;
    }
}
```

### API Resource

```php
class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'legal_name' => $this->legal_name,
            'status' => [
                'key' => $this->status_id,
                'label' => $this->enum('status_id', 'label'),
                'icon' => $this->enum('status_id', 'icon'),
                'css_class' => $this->enum('status_id', 'css_class'),
            ],
            'company_type' => [
                'key' => $this->company_type_id,
                'label' => $this->enum('company_type_id', 'label'),
                'icon' => $this->enum('company_type_id', 'icon'),
            ],
        ];
    }
}
```

## Performance Considerations

### Caching

- Enums are automatically cached using Laravel's cache system
- Cache is cleared when enums are modified
- Compatible with Octane/Swoole (no static variables)

### Optimizations

- Indexes on `type`, `is_active`, and `sort_order`
- Chunk loading for large enum datasets
- Lazy loading of relationships

## Testing

```php
/** @test */
public function it_handles_enum_operations()
{
    $company = Company::factory()->create([
        'status_id' => 'active',
        'company_type_id' => 'llc',
    ]);
    
    // Test enum access
    $this->assertEquals('Active', $company->enum('status_id', 'label'));
    $this->assertEquals('circle-check', $company->enum('status_id', 'icon'));
    
    // Test validation
    $this->assertTrue($company->isValidEnum('status_id', 'active'));
    $this->assertFalse($company->isValidEnum('status_id', 'invalid'));
    
    // Test queries
    $activeCompanies = Company::whereEnum('status_id', 'active')->get();
    $this->assertCount(1, $activeCompanies);
    
    // Test form options
    $options = $company->getEnumOptions('status_id');
    $this->assertArrayHasKey('active', $options);
    $this->assertEquals('Active', $options['active']);
}
```

## Migration from Existing Systems

### From Individual Enum Classes

```php
// Old way
class CompanyStatus extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    
    public static function labels(): array
    {
        return [
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        ];
    }
}

// New way - just add to database and use trait
// No PHP enum classes needed!
```

### From Database Tables

```php
// Old way: separate tables for each enum type
CREATE TABLE company_statuses (id, name, label, ...)
CREATE TABLE company_types (id, name, label, ...)

// New way: single global table
CREATE TABLE apt_global_enums (type, key, label, ...)
```

## Best Practices

### Naming Conventions

- Enum types: `snake_case` (e.g., `company_status`, `product_category`)
- Enum keys: `snake_case` (e.g., `active`, `pending_approval`)
- Field names: end with `_id` (e.g., `status_id`, `category_id`)

### Database Design

- Store enum keys as strings, not IDs
- Use descriptive enum type names
- Keep sort_order consistent
- Use contexts for enum restrictions

### Code Organization

- Define enums in migrations
- Keep $enumTypes arrays minimal
- Use business methods for complex logic
- Leverage automatic validation

## One Line Summary

Developers write minimal code but get maximum enum functionality.
