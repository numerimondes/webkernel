# LanguageSelector Component

A modern Webkernel component for language selection using ComponentBase and UIQuery.

## Usage

### Basic usage
```blade
<x-i18n::language-selector />
```

### With custom options
```blade
<x-i18n::language-selector 
    size="lg" 
    placement="bottom-end"
    :show-flags="true"
    :show-labels="true"
    change-url="/custom-lang"
/>
```

### Without flags
```blade
<x-i18n::language-selector :show-flags="false" />
```

### Without labels
```blade
<x-i18n::language-selector :show-labels="false" />
```

## Available Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `size` | string | 'md' | Button size (xs, sm, md, lg, xl) |
| `disabled` | boolean | false | Disable the component |
| `placement` | string | 'bottom-start' | Dropdown position |
| `showFlags` | boolean | true | Show flag icons |
| `showLabels` | boolean | true | Show language labels |
| `currentLanguage` | string | null | Current language (override) |
| `changeUrl` | string | '/lang' | URL for language change |

## Features

- ✅ Uses ComponentBase and ComponentSchema
- ✅ UIQuery integration for flag assets
- ✅ Active languages support via Language model
- ✅ User preferences management via UserPreference
- ✅ Alpine.js interface for interactivity
- ✅ Filament compatible design
- ✅ Fallback for missing flags
- ✅ Dark mode support

## Assets Used

The component uses UIQuery to load flags from:
`webkernel/src/Aptitudes/I18n/Resources/Assets/flags/language/{code}.svg`

If a flag doesn't exist, a generic SVG is used as fallback.
