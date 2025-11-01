# Builder V1 Architecture

## Overview
This is the optimized V1 structure for the Website Builder, organized for better maintainability, performance, and scalability.

## Directory Structure

```
Resources/Views/builder/V1/
├── app.blade.php                    # Main layout (HTML shell + CSS/JS)
├── index.blade.php                  # Main builder view (grid layout)
├── components/
│   ├── toolbar.blade.php           # Top toolbar (save, preview, etc.)
│   ├── sidebar-blocks.blade.php    # Left: Available blocks library
│   ├── canvas.blade.php            # Center: Page canvas/editor
│   ├── properties.blade.php        # Right: Block properties panel
│   ├── pages-nav.blade.php         # Page navigation/tabs
│   ├── context-menu.blade.php      # Right-click context menu
│   └── dynamic-island.blade.php    # Floating toolbar
└── modals/
    ├── page-settings.blade.php     # Page settings modal
    ├── block-library.blade.php     # Block library modal
    └── confirm-delete.blade.php    # Confirmation modals
```

## Asset Organization

```
Resources/
├── css/builder/V1/
│   ├── app.css                     # Main builder styles
│   └── components/
│       ├── toolbar.css
│       ├── canvas.css
│       ├── sidebar.css
│       └── properties.css
└── js/builder/V1/
    ├── app.js                      # Main builder JavaScript
    └── components/
        ├── toolbar.js
        ├── canvas.js
        ├── sidebar.js
        └── properties.js
```

## Key Features

### 1. Single Entry Point
- `app.blade.php` - Contains all CSS/JS imports, base HTML structure
- `index.blade.php` - Main builder grid layout, includes components

### 2. Component-Based Architecture
- Each component is self-contained with its CSS/JS
- Clear responsibility: toolbar, sidebar, canvas, properties
- Easy to maintain and modify

### 3. Grid-Based Layout
```css
.builder-layout {
    display: grid;
    grid-template-areas: 
        "toolbar toolbar toolbar"
        "pages   pages   pages"
        "sidebar canvas  properties";
    grid-template-rows: 60px 40px 1fr;
    grid-template-columns: 300px 1fr 300px;
    height: 100vh;
}
```

### 4. Component Responsibilities

**Toolbar** (`toolbar.blade.php`):
- Save/Publish buttons
- Preview button
- Undo/Redo
- Project selector

**Sidebar** (`sidebar-blocks.blade.php`):
- Available blocks library
- Search/filter blocks
- Block categories

**Canvas** (`canvas.blade.php`):
- Live page preview
- Block selection/highlighting
- Drag & drop zones
- Context menus

**Properties** (`properties.blade.php`):
- Selected block configuration
- Page settings
- SEO settings

**Pages Navigation** (`pages-nav.blade.php`):
- Page tabs
- Add/delete pages
- Page status indicators

## Benefits

1. **Performance**: Single CSS/JS load, minimal HTTP requests
2. **Maintainability**: Clear separation of concerns
3. **Scalability**: Easy to add new components
4. **Development Speed**: No complex inheritance chains
5. **Debugging**: Easy to isolate issues to specific components

## File Size Optimization

Each component is optimized to be:
- < 200 lines of Blade template
- < 100 lines of CSS
- < 150 lines of JavaScript
- Total builder payload < 50KB (gzipped)

## Migration from Current Structure

The V1 structure preserves all existing functionality while providing:
- Better organization
- Improved performance
- Easier maintenance
- Cleaner code structure

All existing styling and functionality has been preserved and reorganized into the new structure.
