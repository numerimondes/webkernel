# Webkernel UI Components

A magical, fluent component system that makes creating and using UI components feel like pure magic.

## Philosophy

- **One file per component** - Keep it simple
- **Zero configuration bloat** - Everything in the `define()` method
- **Pure magic** - Developers should feel like wizards, not configuration managers
- **Website builder ready** - Components automatically work with visual builders

## Quick Start

### Using in Blade Templates

```blade
{{-- Basic button --}}
<x-ui::button text="Click me" />

{{-- Button with icon --}}
<x-ui::button text="Save" icon="save" color="success" />

{{-- Link button --}}
<x-ui::button text="Visit" href="https://example.com" target="_blank" />

{{-- Custom styling --}}
<x-ui::button text="Custom" custom_class="my-custom-classes" />

{{-- Using slots --}}
<x-ui::button color="primary" icon="heart">
    I love this button!
</x-ui::button>

{{-- Complex button --}}
<x-ui::button 
    text="Process" 
    color="primary" 
    variant="outline" 
    size="lg" 
    icon="arrow-right" 
    icon_position="after"
    loading="false"
    badge="3"
    tooltip="Click to process items"
    key_bindings="ctrl+p,cmd+p" 
/>
```

### All Button Options

| Property | Type | Default | Options | Description |
|----------|------|---------|---------|-------------|
| `text` | string | 'Button' | - | Button text content |
| `color` | enum | 'primary' | primary, secondary, success, danger, warning, info, gray | Color scheme |
| `variant` | enum | 'solid' | solid, outline, ghost | Visual variant |
| `size` | enum | 'md' | xs, sm, md, lg, xl | Button size |
| `icon` | string | null | lucide icons | Icon name |
| `icon_position` | enum | 'before' | before, after | Icon placement |
| `href` | url | null | - | Makes button a link |
| `target` | enum | '_self' | _self, _blank, _parent, _top | Link target |
| `type` | enum | 'button' | button, submit, reset | Button type |
| `disabled` | boolean | false | - | Disable button |
| `loading` | boolean | false | - | Show loading spinner |
| `badge` | string | null | - | Badge text |
| `tooltip` | string | null | - | Tooltip text |
| `key_bindings` | string | null | - | Keyboard shortcuts (comma-separated) |
| `custom_class` | string | null | - | Additional CSS classes |
| `remove_styling` | boolean | false | - | Remove all default styling |

## Creating New Components

### 1. Create the Component Class

```php
<?php
declare(strict_types=1);

namespace Webkernel\Aptitudes\UI\Resources\Views\Components\MyComponent;

use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\Support\ComponentSchema;

class MyComponent extends ComponentBase
{
    protected function define(ComponentSchema $schema): void
    {
        $schema
            // Define your fields
            ->string('title')->default('Default Title')
            ->boolean('active')->default(false)
            ->enum('type')->options(['info', 'warning', 'error'])->default('info')
            
            // Computed properties
            ->compute('classes', fn($config) => 'my-' . $config['type'])
            
            // Base styling
            ->baseClasses(['component', 'rounded', 'p-4'])
            
            // Variants
            ->variantClasses('type', [
                'info' => 'bg-blue-100 text-blue-800',
                'warning' => 'bg-yellow-100 text-yellow-800',
                'error' => 'bg-red-100 text-red-800',
            ])
            
            // Conditional classes
            ->conditionalClass('active', 'border-l-4 border-blue-500')
            
            // Attributes
            ->attribute('data-type', 'type')
            ->conditionalAttribute('active', 'aria-expanded', 'true');
    }
}
```

### 2. Create the Blade Template

Create `webkernel/src/Aptitudes/UI/Resources/Views/components/my-component/index.blade.php`:

```blade
@php
use Webkernel\Aptitudes\UI\Resources\Views\Components\MyComponent\MyComponent;
$component = new MyComponent($attributes->getAttributes());
@endphp

<div {!! $component->getAttributes() !!} id="{{ $component->getId() }}">
    <h3>{{ $component->title }}</h3>
    {{ $slot }}
</div>

@once
    @push('ui-scripts')
        <script>
            // Component-specific JavaScript
        </script>
    @endpush
@endonce
```

### 3. Use Your Component

```blade
<x-ui::my-component title="Hello World" type="warning" active="true">
    This is my custom component content!
</x-ui::my-component>
```

## Advanced Schema Features

### Dynamic Classes

```php
->dynamicClass('responsive_classes', function($config) {
    $classes = [];
    if ($config['mobile_hidden']) $classes[] = 'hidden md:block';
    if ($config['desktop_hidden']) $classes[] = 'md:hidden';
    return implode(' ', $classes);
})
```

### Complex Computed Properties

```php
->compute('icon_size', function($config) {
    $sizeMap = [
        'sm' => $config['dense'] ? 'w-3 h-3' : 'w-4 h-4',
        'md' => $config['dense'] ? 'w-4 h-4' : 'w-5 h-5',
        'lg' => $config['dense'] ? 'w-5 h-5' : 'w-6 h-6',
    ];
    return $sizeMap[$config['size']] ?? 'w-5 h-5';
})
```

### Conditional Styling

```php
->conditionalClass('error', 'border-red-500 bg-red-50')
->conditionalClass('success', 'border-green-500 bg-green-50')
->conditionalClass('loading', 'opacity-50 pointer-events-none')
```

### Dynamic Attributes

```php
->dynamicAttribute('aria-label', fn($config) => 
    $config['accessible_label'] ?: $config['title']
)
->dynamicAttribute('role', fn($config) => 
    $config['interactive'] ? 'button' : 'presentation'
)
```

## Website Builder Integration

Components automatically include metadata for visual builders:

```html
<button 
    class="..."
    data-component="button"
    data-builder='{"id":"button_123","component":"Button","schema":{...},"config":{...}}'
>
```

Test it using Tinker
```bash
php artisan tinker --execute="use Webkernel\Aptitudes\UI\Resources\Views\components\button\Button; \$button = new Button(['color' => 'primary', 'notification' => '3']); echo 'Generated attributes:' . PHP_EOL; echo \$button->getAttributes() . PHP_EOL;"
```

This enables:
- Visual editing in website builders
- Property panels
- Real-time preview
- Component inspection

## Asset Management

### Automatic Asset Loading

Components can include CSS/JS that loads automatically:

```blade
@once
    @push('ui-styles')
        <link rel="stylesheet" href="{{ asset('components/my-component/style.css') }}">
    @endpush
    
    @push('ui-scripts')
        <script src="{{ asset('components/my-component/script.js') }}"></script>
    @endpush
@endonce
```

### Manual Asset Control

For more control, exclude `@once` and manage loading yourself:

```blade
{{-- In your layout --}}
@stack('ui-styles')
@stack('ui-scripts')
```

## Component Architecture

```
webkernel/src/Aptitudes/UI/
├── ComponentBase.php              # Base component class
├── Support/
│   └── ComponentSchema.php        # Fluent schema builder
└── Resources/Views/components/
    ├── button/
    │   ├── Button.php             # Component logic
    │   └── index.blade.php        # Template
    ├── card/
    │   ├── Card.php
    │   └── index.blade.php
    └── ...
```

## Best Practices

### 1. Keep Components Focused
Each component should have a single responsibility.

### 2. Use Semantic Naming
```php
->string('primary_action')->label('Primary Action Text')
->boolean('show_secondary')->label('Show Secondary Button')
```

### 3. Provide Good Defaults
```php
->string('text')->default('Click me')
->enum('size')->options(['sm', 'md', 'lg'])->default('md')
```

### 4. Use Computed Properties for Logic
```php
->compute('is_external_link', fn($c) => 
    $c['href'] && !str_starts_with($c['href'], config('app.url'))
)
```

### 5. Make Components Accessible
```php
->conditionalAttribute('external_link', 'aria-label', 'Opens in new window')
->attribute('role', 'button')
```

## Component Examples

### Alert Component

```php
protected function define(ComponentSchema $schema): void
{
    $schema
        ->string('title')->nullable()
        ->string('message')->default('')
        ->enum('type')->options(['info', 'success', 'warning', 'error'])->default('info')
        ->boolean('dismissible')->default(false)
        ->icon('icon')->nullable()
        
        ->baseClasses(['alert', 'rounded-lg', 'p-4', 'mb-4'])
        ->variantClasses('type', [
            'info' => 'bg-blue-50 border-blue-200 text-blue-800',
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'error' => 'bg-red-50 border-red-200 text-red-800',
        ])
        
        ->conditionalClass('dismissible', 'pr-12')
        ->attribute('role', 'alert');
}
```

### Card Component

```php
protected function define(ComponentSchema $schema): void
{
    $schema
        ->string('title')->nullable()
        ->string('subtitle')->nullable()
        ->boolean('bordered')->default(true)
        ->boolean('shadowed')->default(true)
        ->enum('padding')->options(['none', 'sm', 'md', 'lg'])->default('md')
        
        ->baseClasses(['card', 'rounded-lg', 'bg-white'])
        ->conditionalClass('bordered', 'border border-gray-200')
        ->conditionalClass('shadowed', 'shadow-sm')
        
        ->variantClasses('padding', [
            'none' => '',
            'sm' => 'p-3',
            'md' => 'p-6',
            'lg' => 'p-8',
        ]);
}
```

## Troubleshooting

### Component Not Found
Ensure your component class follows the naming convention:
- Class: `Webkernel\Aptitudes\UI\Resources\Views\Components\Button\Button`
- Usage: `<x-ui::button />`

### Styles Not Applying
Check that Tailwind CSS is properly configured and includes your component paths.

### Assets Not Loading
Make sure you're using `@stack('ui-styles')` and `@stack('ui-scripts')` in your layout.

---

**Happy coding! 🎉** Create beautiful, maintainable components with minimal effort.
